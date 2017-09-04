<?php
/**
 * @category    Ms
 * @package     Ms_AddressVerification
 * @license     https://markshust.com/eula/
 */
class Ms_AddressVerification_Helper_Data
    extends Mage_Core_Helper_Abstract
{
    /**
     * Get the current posted address by type, and return it as an object.
     *
     * @param $addressType string 'billing' or 'shipping'
     * @return object
     */
    public function getPostAddressAsObject($addressType = null) {
        $response = (object) array();

        if (Mage::app()->getRequest()->isPost()) {
            $address = Mage::app()->getRequest()->getPost($addressType, array());

            // Check to see if this address is a customer address book entry
            if ($existingAddressId = Mage::app()->getRequest()->getPost($addressType . '_address_id')) {
                $customer        = Mage::getModel('customer/session')->getCustomer();
                $existingAddress = $customer->getAddressById($existingAddressId);

                // Ensure customer id matches address book id to prevent fraudulent lookups
                if ($existingAddress->getId() == $existingAddressId
                    && $existingAddress->getCustomerId() == $customer->getId()
                ) {
                    $regionModel = Mage::getModel('directory/region')->load($existingAddress->getRegionId());
                    $regionCode  = $regionModel->getCode();
                    $street      = $existingAddress->getStreet();

                    $response->street1    = isset($street[0]) ? $street[0] : '';
                    $response->street2    = isset($street[1]) ? $street[1] : '';
                    $response->city       = $existingAddress->getCity();
                    $response->state      = isset($regionCode) ? $regionCode : '';
                    $response->postcode   = $existingAddress->getPostcode();
                    $response->countryId  = $existingAddress->getCountryId();
                    $response->type       = $addressType;
                    $response->isBypassed = isset($address['addressverification_bypass']) ? true : false;
                }
            }
            else {
                $regionModel = Mage::getModel('directory/region')->load($address['region_id']);
                $regionCode  = $regionModel->getCode();

                // Standardize request if coming from admin (uses different street format)
                if (isset($address['street0'])) {
                    $address['street'] = array(
                        0 => $address['street0'],
                        1 => $address['street1'],
                    );
                }

                $response->street1    = isset($address['street'][0]) ? $address['street'][0] : '';
                $response->street2    = isset($address['street'][1]) ? $address['street'][1] : '';
                $response->city       = isset($address['city']) ? $address['city'] : '';
                $response->state      = isset($regionCode) ? $regionCode : '';
                $response->postcode   = isset($address['postcode']) ? $address['postcode'] : '';
                $response->countryId  = isset($address['country_id']) ? $address['country_id'] : '';
                $response->type       = $addressType;
                $response->isBypassed = isset($address['addressverification_bypass']) ? true : false;
            }
        }

        return $response;
    }

    /**
     * Verify the address object, and return array of cleansed address and/or errors.
     * This will return an empty array if address was not to be verified.
     *
     * @param $address object
     * @return array
     */
    public function verifyAddress($address) {
        $response = array();

        if (Mage::helper('ms_addressverification')->shouldVerifyAddress($address)) {
            $response = Mage::helper('ms_addressverification')->verifyAddressPrimaryApi($address);
        }

        return $response;
    }

    /**
     * Check to see if the address should be verified.
     *
     * @param $address object
     * @return bool
     */
    public function shouldVerifyAddress($address) {
        $response = false;

        if (Mage::getStoreConfig('ms_addressverification/general/enabled')) {
            switch (Mage::getStoreConfig('ms_addressverification/general/primary_api')) {
                case Ms_AddressVerification_Model_Attribute_Source_Api::API_USPS:
                    $response = Mage::helper('ms_addressverification/usps')->shouldVerifyAddress($address);

                    break;

                case Ms_AddressVerification_Model_Attribute_Source_Api::API_SMARTYSTREETS:
                    $response = Mage::helper('ms_addressverification/smartystreets')->shouldVerifyAddress($address);

                    break;
            }
        }

        return $response;
    }

    /**
     * Verify address with the enabled API.
     *
     * @param $address
     * @return object
     */
    public function verifyAddressPrimaryApi($address) {
        $result = array();

        switch (Mage::getStoreConfig('ms_addressverification/general/primary_api')) {
            case Ms_AddressVerification_Model_Attribute_Source_Api::API_USPS:
                $result = Mage::helper('ms_addressverification/usps')->verifyAddress($address);

                break;

            case Ms_AddressVerification_Model_Attribute_Source_Api::API_SMARTYSTREETS:
                $result = Mage::helper('ms_addressverification/smartystreets')->verifyAddress($address);

                break;
        }

        return $result;
    }

    /**
     * Update the post object with the verified address.
     *
     * @param $address array
     * @param $addressType string 'billing' or 'shipping'
     */
    public function updatePostAddress($address, $addressType = null)
    {
        $post = Mage::app()->getRequest()->getPost($addressType, array());

        if ($addressType) {
            foreach ($address as $addressItemKey => $addressItemVal) {
                $post[$addressItemKey] = $addressItemVal;
            }

            Mage::app()->getRequest()->setPost($addressType, $post);
        } else {
            foreach ($address as $addressItemKey => $addressItemVal) {
                Mage::app()->getRequest()->setPost($addressItemKey, $addressItemVal);
            }
        }
    }

    /**
     * Transform the verified address response into a format needed for ajax in the admin.
     *
     * @param $result array
     * @return array
     */
    public function transformResultToAdminAjaxFormat($result)
    {
        return array(
            'street0' => array(
                0 => $result['address']['street'][0]
            ),
            'street1' => array(
                0 => $result['address']['street'][1]
            ),
            'city' => array(
                0 => $result['address']['city']
            ),
            'region_id' => $result['address']['region_id'],
            'postcode' => $result['address']['postcode'],
            'country_id' => $result['address']['country_id'],
        );
    }

    /**
     * Check to see if this address is a customer address book entry. If it is, update that address.
     *
     * @param $addressType string
     * @param $newAddress array
     */
    public function updateExistingAddress($addressType, $newAddress)
    {
        if ($existingAddressId = Mage::app()->getRequest()->getPost($addressType . '_address_id')) {
            $customer = Mage::getModel('customer/session')->getCustomer();
            $existingAddress = $customer->getAddressById($existingAddressId);

            // Ensure customer id matches address book id to prevent fraudulent lookups
            if ($existingAddress->getId() == $existingAddressId
                && $existingAddress->getCustomerId() == $customer->getId()
            ) {
                // Set existing address to verified address and save
                $existingAddress->setId($existingAddressId)
                    ->setStreet($newAddress['address']['street'])
                    ->setCity($newAddress['address']['city'])
                    ->setRegionId($newAddress['address']['region_id'])
                    ->setPostcode($newAddress['address']['postcode']);

                $existingAddress->save();
            }
        }
    }
}
