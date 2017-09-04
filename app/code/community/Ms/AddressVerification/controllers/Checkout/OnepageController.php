<?php
/**
 * @category    Ms
 * @package     Ms_AddressVerification
 * @license     https://markshust.com/eula/
 */
require_once Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'OnepageController.php';

class Ms_AddressVerification_Checkout_OnepageController
    extends Mage_Checkout_OnepageController
{
    /**
     * Perform address validation on checkout billing address.
     */
    public function saveBillingAction()
    {
        $address = Mage::helper('ms_addressverification')->getPostAddressAsObject('billing');
        $result = Mage::helper('ms_addressverification')->verifyAddress($address);

        if (empty($result['error'])) {
            if (isset($result['address'])) {
                Mage::helper('ms_addressverification')->updatePostAddress($result['address'], 'billing');
            }

            parent::saveBillingAction();
        } else {
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result['error']));
        }
    }

    /**
     * Perform address validation on checkout shipping address.
     */
    public function saveShippingAction()
    {
        $address = Mage::helper('ms_addressverification')->getPostAddressAsObject('shipping');
        $result = Mage::helper('ms_addressverification')->verifyAddress($address);

        if (empty($result['error'])) {
            if (isset($result['address'])) {
                Mage::helper('ms_addressverification')->updatePostAddress($result['address'], 'shipping');
            }

            parent::saveShippingAction();
        } else {
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result['error']));
        }
    }
}
