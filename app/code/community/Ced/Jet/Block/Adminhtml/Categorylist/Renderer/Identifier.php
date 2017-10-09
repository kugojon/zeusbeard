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

class Ced_Jet_Block_Adminhtml_Categorylist_Renderer_Identifier extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
	 
	public function render(Varien_Object $row)
	{
		$html='';
		$csv_cat_id="";
		$csv_cat_id=$row->getData('csv_cat_id');
		$csv_cat_id=trim($csv_cat_id);
		if($csv_cat_id !=""){
				$collection="";
				$magento_id=0;
				$collection=Mage::getModel('jet/jetcategory')->getCollection()->addFieldToFilter('jet_cate_id',$csv_cat_id);
				if(count($collection)>0){
					foreach($collection as $coll){
						$magento_id=$coll->getMagentoCatId();
						break;
					}
				}
				if($magento_id!=0){
					$html=$magento_id;
				}
				
		}
		return $html;
	 
	}	
 		
}
?>