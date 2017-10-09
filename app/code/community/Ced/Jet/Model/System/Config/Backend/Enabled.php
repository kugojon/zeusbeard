<?php
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_Jet
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */


class Ced_Jet_Model_System_Config_Backend_Enabled extends Mage_Core_Model_Config_Data
{

    /**
     * Perform API call to Validate Jet Keys
     *
     */
    public function _afterSaveCommit()
    {
        $data = $this->_getCredentials();
        $usr = "";
        $pswd = "";

        if($data['sandbox']['value'] == 1){
            $usr = $data['jet_sandbox_user']['value'];
            $pswd = $data['jet_sandbox_userpwd']['value'];
        }else{
            $usr = $data['jet_user']['value'];
            $pswd = $data['jet_userpwd']['value'];
        }
        $token = Mage::helper('jet/data')->JrequestTokenCurlVerify($usr, $pswd);
        if($token){
            Mage::getModel('core/config')->saveConfig('jet_options/ced_jet/is_credentials_valid', 1);
            Mage::getSingleton('core/session')->addSuccess("All of your Jet API Credentials are correct!");
        }else{
            Mage::getModel('core/config')->saveConfig('jet_options/ced_jet/is_credentials_valid', 0);
            Mage::getSingleton('core/session')->addError("Wrong Jet API Details. Please Check Jet API Credentials Again!");
        }


        return parent::_afterSaveCommit();
    }



    /**
     * Return credentials
     */
    private function _getCredentials()
    {
        $groups = $this->getData('groups');
        return $groups['ced_jet']['fields'];
    }


}
