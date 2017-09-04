<?php
/**
 * @category    Ms
 * @package     Ms_AddressVerification
 * @license     https://markshust.com/eula/
 */
class Ms_AddressVerification_Helper_Usps
    extends Mage_Core_Helper_Abstract
{
    /**
     * Check to see if the address should be verified.
     *
     * @param $address object
     * @return bool
     */
    public function shouldVerifyAddress($address)
    {
        return $address->countryId == 'US' && ! $address->isBypassed ? true : false;
    }

    /**
     * Verify address, run through error checking and return the result.
     *
     * @param $address
     * @return object
     */
    public function verifyAddress($address)
    {
        $result = array();

        // Prep object for USPS-specific call
        $address->address2 = $address->street1;
        $address->address1 = $address->street2;
        $address->zip      = $address->postcode;

        $apiResponse = Mage::helper('ms_addressverification/usps')->verifyAddressApi($address);
        $apiResponse = json_decode(json_encode($apiResponse)); // Convert to std object

        $result['error'] = Mage::helper('ms_addressverification/usps')->getErrorFromResponse($apiResponse);

        if (empty($result['error'])) {
            $region = Mage::getModel('directory/region')->loadByCode($apiResponse->Address->State, 'US');
            $zip4 = isset($apiResponse->Address->Zip4) ? '-' . $apiResponse->Address->Zip4 : '';
            $result['address'] = array(
                'street'     => array(
                    0 => isset($apiResponse->Address->Address2) ? $apiResponse->Address->Address2 : '',
                    1 => isset($apiResponse->Address->Address1) ? $apiResponse->Address->Address1 : '',
                ),
                'city'       => $apiResponse->Address->City,
                'region_id'  => $region->getId(),
                'postcode'   => $apiResponse->Address->Zip5 . $zip4,
                'country_id' => 'US' // everything passed to this API is in U.S.
            );

            Mage::helper('ms_addressverification')->updateExistingAddress($address->type, $result);
        }

        return $result;
    }

    /**
     * Call USPS API for address verification.
     *
     * @param $address object
     * @return \SimpleXMLElement
     */
    public function verifyAddressApi($address) {
        $xml = Mage::helper('ms_addressverification/usps')->addressToXml($address);

        $ch = curl_init(Mage::getStoreConfig('ms_addressverification/usps/gateway_url'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "API=Verify&XML=$xml");
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = (string) curl_exec($ch);
        $error = curl_error($ch);

        if ($error) {
            $result = '<AddressValidateRequest><Error><![CDATA[$error]]></Error></AddressValidateRequest>';
        }

        return new SimpleXMLElement($result);
    }

    /**
     * Returns an array containing errors (if any).
     *
     * @param $response object
     * @return array
     */
    public function getErrorFromResponse($response) {
        $error = array();

        if (isset($response->Error)) {
            // Check XML response for general error
            $message = 'USPS Error: ' . $response->Error;
            $error = array(
                'error' => -1,
                'message' => Mage::helper('ms_addressverification')->__($message),
                'error_addressverification' => 1,
                'allow_bypass' => Mage::getStoreConfig('ms_addressverification/general/allow_bypass'),
            );
        } elseif (empty($response->Address)) {
            // No Address object means API isn't setup for production access
            $message = 'USPS Error: The API key is not setup for production access.';
            $error = array(
                'error' => -1,
                'message' => Mage::helper('ms_addressverification')->__($message),
                'error_addressverification' => 1
            );
        } elseif (isset($response->Address->Error)) {
            // Check XML response for street address errors and store as error message
            $message = $response->Address->Error->Description;
            $error = array(
                'error' => -1,
                'message' => Mage::helper('ms_addressverification')->__($message),
                'error_addressverification' => 1,
                'allow_bypass' => Mage::getStoreConfig('ms_addressverification/general/allow_bypass'),
            );
        } elseif (isset($response->Address->ReturnText)) {
            // More info needed on address, let's err it out
            $message = str_replace('Default address: ', '', $response->Address->ReturnText);
            $error = array(
                'error' => -1,
                'message' => Mage::helper('ms_addressverification')->__($message),
                'error_addressverification' => 1,
                'allow_bypass' => Mage::getStoreConfig('ms_addressverification/general/allow_bypass'),
            );
        }

        return $error;
    }

    /**
     * Convert address object to XML object needed for USPS API call.
     *
     * @param $address object
     * @return string
     */
    public function addressToXml($address) {
        $account = Mage::getStoreConfig('ms_addressverification/usps/account_number');

        $xml = "<AddressValidateRequest USERID=\"$account\">"
            . "<Address ID=\"1\">"
                . "<Address1>{$address->address1}</Address1>"
                . "<Address2>{$address->address2}</Address2>"
                . "<City>{$address->city}</City>"
                . "<State>{$address->state}</State>"
                . "<Zip5>{$address->zip}</Zip5>"
                . "<Zip4></Zip4>"
            . "</Address>";

        if (isset($address->ship_address2)) {
            $xml .= "<Address ID=\"2\">"
                    . "<Address1>{$address->ship_address1}</Address1>"
                    . "<Address2>{$address->ship_address2}</Address2>"
                    . "<City>{$address->ship_city}</City>"
                    . "<State>{$address->ship_state}</State>"
                    . "<Zip5>{$address->ship_zip}</Zip5>"
                    . "<Zip4></Zip4>"
                . "</Address>";
        }

        $xml .= '</AddressValidateRequest>';

        return $xml;
    }
}
