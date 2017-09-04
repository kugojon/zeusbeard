<?php
/**
 * @category    Ms
 * @package     Ms_AddressVerification
 * @license     https://markshust.com/eula/
 */
require_once Mage::getModuleDir('controllers', 'Mage_Customer') . DS . 'AddressController.php';

class Ms_AddressVerification_Customer_AddressController
    extends Mage_Customer_AddressController
{
    public function formPostAction()
    {
        if (! $this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }

        $address = Mage::helper('ms_addressverification')->getPostAddressAsObject();
        $result = Mage::helper('ms_addressverification')->verifyAddress($address);

        if (empty($result['error'])) {
            if (isset($result['address'])) {
                Mage::helper('ms_addressverification')->updatePostAddress($result['address']);
            }

            return parent::formPostAction();
        } else {
            $this->_getSession()->addError($result['error']['message']);

            $customerAddress = Mage::getModel('customer/address');
            $post = $this->getRequest()->getPost();
            $post['addressverification_failed'] = 1;
            $this->_getSession()->setAddressFormData($post);

            if ($customerAddress->getId()) {
                return $this->_redirectError(Mage::getUrl('*/*/edit', array('id' => $customerAddress->getId())));
            } else {
                return $this->_redirect('*/*/new/');
            }
        }
    }
}
