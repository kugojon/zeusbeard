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


class Ced_Jet_Block_Adminhtml_Prod extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $ur = Mage::helper('adminhtml')->getUrl('*/');
        $zz = array();
        $zz = explode('index/index', $ur);
        $profileId = $this->getRequest()->getParam('profile_id');

        $this->_controller = 'adminhtml_prod';
        $this->_blockGroup = 'jet';
        $this->_headerText = Mage::helper('jet')->__('Product Manager');
        $this->_addButtonLabel = 'Sync Jet Product Status';

        $profileId = $this->getRequest()->getParam('profile_id');
        $profile = Mage::getModel('jet/profile')->load($profileId);
        $data = array(
            'label' =>  'Back',
            'onclick'   => 'setLocation(\'' . $this->getUrl('adminhtml/adminhtml_profile/edit', array('pcode'=>$profile->getProfileCode() )) . '\')',
            'class'     =>  'back'
        );
        $this->addButton('my_back', $data, 0, 0, 'header');



        /*$this->_addButton("Upload All Product", array(
            "label"     => Mage::helper("jet")->__("Upload Visible Products"),
             "onclick"   => "saveTrigger('".$zz[0]."')",
            "class"     => "btn btn-danger",
        ));
		$this->_addButton("Sync Inventory & Price", array(
            "label"     => Mage::helper("jet")->__("Sync Inventory & Price"),
            "onclick"   => "location.href = '".$this->getUrl('adminhtml/adminhtml_jetajax/sync', array('profile_id' => $profileId))."';",
            "class"     => "btn btn-danger",
        ));*/

        
           parent::__construct();
        $this->_removeButton('add');

    }
    public function getCreateUrl()
    {
        $profileId = $this->getRequest()->getParam('profile_id');
        return $this->getUrl('*/*/new', array('profile_id' => $profileId));
    }
}
