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

class Ced_Jet_Block_Adminhtml_Prod_Renderer_Analysis extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action { 
	public function render(Varien_Object $row) {
					$sku=$row->getSku();
					$ur = Mage::helper('adminhtml')->getUrl('*/');
					$zz = array();
					$zz = explode('index/index',$ur);

			        //$id=$row->getData('entity_id');
			        $content='<a href="javascript: void(0);" id="analysis_'.$sku.'" onclick="analysisProduct(event,'."'".$sku."'".','."'".$zz[0]."'".')">Analyze</a>';
			        $content.='<div class="analysisall" id="amanage_'.$sku.'" ></div>';
			        return $content;
	 
	 	 }
}
