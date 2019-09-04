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


class Ced_Jet_Block_Adminhtml_Jetcategory_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $id= $this->getRequest()->getParam('id');
        $url=$this->getUrl('adminhtml/adminhtml_jetproduct/massarchived', array('product'=>$id));
        $url1=$this->getUrl('adminhtml/adminhtml_jetproduct/massunarchived', array('product'=>$id));
        $this->_removeButton('back');
        $this->_objectId = 'id';
        $this->_blockGroup = 'jet';
        $this->_controller = 'adminhtml_prod';
        $this->_removeButton('delete'); 
        $this->_removeButton('reset'); 
        
        $this->addButton(
            'back', array(
                'label'   => $this->__('Back'),
                'onclick' => "setLocation('{$this->getUrl('*/*/uploadproduct')}')",
                'class'   => 'back'
            )
        ); 
            
        $status=Mage::getModel('catalog/product')->load($id)->getData('jet_product_status');
        
        if($status=='ready_to_list' || $status=='available_for_purchase' || $status=='processing')
        {
            $this->addButton(
                'archived', array(
                'label'   => $this->__('Archive'),
                'onclick' => "setLocation('{$url}')",
                'class'   => 'button'
                )
            );
        }

        if($status=='Archived')
        {
            $this->addButton(
                'unarchive', array(
                'label'   => $this->__('Unarchive'),
                'onclick' => "setLocation('{$url1}')",
                'class'   => 'button'
                )
            );
        }

        $this->_updateButton('save', 'label', Mage::helper('jet')->__('Save'));
    }
 
    public function getHeaderText()
    {
        return "Product Information From Jet.com";
    }
}
