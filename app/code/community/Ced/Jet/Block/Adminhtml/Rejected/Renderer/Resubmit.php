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


class Ced_Jet_Block_Adminhtml_Rejected_Renderer_Resubmit extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
	 
	public function render(Varien_Object $row)
	{
		$html='';
		if(trim($row->status)=='Resubmit Requested'){
			$html = "<span><strong>N/A</strong></span>";
		}else{ 
			$html = "<a href=". $this->getUrl('adminhtml/adminhtml_jetproduct/jerrorDetails/',array('id'=>$row->id)).">View Errors & Resubmit</a>";
		}
	 	return $html;
	 
	}	
 		
}
?>