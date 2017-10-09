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
class Ced_Jet_Block_Adminhtml_Jetorder_Renderer_Vieworder extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
	 
	public function render(Varien_Object $row)
	{
		$order  = Mage::getModel('sales/order')->loadByIncrementId($row->magento_order_id);
		$html='';
		if(sizeof($order)>0){
			$html = "<a href=". $this->getUrl('adminhtml/sales_order/view',array('order_id'=>$order->getId())).">".$row->magento_order_id."</a>";
		}else{
			$html = "<span><strong> Order Not Found!</strong></span>";
		}
		return $html;
	 
	}	
 		
}
?>