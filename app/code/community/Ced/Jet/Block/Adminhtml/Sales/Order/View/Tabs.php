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
 
class Ced_Jet_Block_Adminhtml_Sales_Order_View_Tabs extends Mage_Adminhtml_Block_Template 
implements Mage_Adminhtml_Block_Widget_Tab_Interface{
	

	public function _construct()
    {
		parent::_construct();
		$data= Mage::registry('current_order')->getData();
	
		$order = Mage::getModel('sales/order')->load($data['entity_id']);
    	$Incrementid = $order->getIncrementId();
    		
    	$orderdata=Mage::getModel('jet/jetorder')->getCollection()->addFieldToFilter('magento_order_id',$Incrementid)->getData();

    	if($orderdata){
            
    		$this->setTemplate('ced/jet/order/view/tab/custom_tab.phtml');
            
			/*$this->addTab('Ship_By_Jet', array(
				'label'     => Mage::helper('jet')->__('Ship By  Jet'),
				'title'     => Mage::helper('jet')->__('Ship By Jet'),
				'content'   => $this->getLayout()->createBlock('core/template')->setTemplate('ced/jet/order/view/tab/custom_tab.phtml')->toHtml(),
			));*/
	  
		}
		else
		{
			$this->setTemplate('ced/jet/order/view/tab/custom_tab_no_order.phtml');
		}
    	
    	
    }

    public function getTabLabel() {
        return $this->__('Ship By  Jet');
    }

    public function getTabTitle() {
        return $this->__('Ship By  Jet');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

   public function getOrder(){
        return Mage::registry('current_order');
    }
}
