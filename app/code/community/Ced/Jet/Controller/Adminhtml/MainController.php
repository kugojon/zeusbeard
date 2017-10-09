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

class Ced_Jet_Controller_Adminhtml_MainController extends Mage_Adminhtml_Controller_Action
{
  public function preDispatch()
    {
        parent::preDispatch();


        //get the admin session
        Mage::getSingleton('core/session', array('name'=>'adminhtml'));


        if(Mage::getSingleton('admin/session')->isLoggedIn()){

            $isValid = Mage::helper('jet/data')->checkIfCredentialsValid();

            $session = Mage::getSingleton('adminhtml/session');

            if(!$isValid){
                $configUrl  = $this->getUrl('adminhtml/system_config/edit/section/jet_options');
                $message = 'Please Check JET API credentials we are unable to verify credntials with Jet.com, It might be wrong. Please correct it by going to the following <a href="'.$configUrl.'">Configuration</a> link.';
                $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
                $message1 = 'Enter fullfillmentnode id form Jet <a href="'.$configUrl.'">Configuration</a> link.';
                foreach ($session->getMessages()->getItems() as $item){
                    if($item->getText() == $message)
                        return $this;
                      if($item->getText() == $message1)
                        return $this;
                }
                $session
                    ->addError($message);
                    $session
                    ->addError($message1);
            }

        }

    }
}


