<?php
/**
 * @category    Ms
 * @package     Ms_AddressVerification
 * @license     https://markshust.com/eula/
 */
class Ms_AddressVerification_Adminhtml_Addressverification_AjaxController
    extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        if (! $this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }

        $address = Mage::helper('ms_addressverification')->getPostAddressAsObject();
        $result = Mage::helper('ms_addressverification')->verifyAddress($address);

        if (empty($result['error'])) {
            $response = Mage::helper('ms_addressverification')->transformResultToAdminAjaxFormat($result);
            $response['message'] = $this->__('Address verified and updated.');
        } else {
            $response['error'] = 1;
            $response['message'] = $result['error']['message'];
        }

        // Return response in JSON
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));

        return $this;
    }
}
