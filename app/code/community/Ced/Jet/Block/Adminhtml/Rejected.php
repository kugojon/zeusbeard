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

class Ced_Jet_Block_Adminhtml_Rejected extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		
		$this->_controller = 'adminhtml_rejected';
		$this->_blockGroup = 'jet';
		$this->_addButtonLabel = 'Sync Feeds';
		$this->_headerText = Mage::helper('jet')->__('Sync Feeds');
		$this->_addButton("Clear All Logs", array(
                    "label"     => Mage::helper("jet")->__("Clear All Logs"),
                    "onclick"   => "location.href = '".$this->getUrl('adminhtml/adminhtml_jetproduct/clearall')."';",
                    "class"     => "btn btn-danger",
                ));
		parent::__construct();

	}
}
