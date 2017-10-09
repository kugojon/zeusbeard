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
class Ced_Jet_Block_Adminhtml_Jetattribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct(){
	    $this->_controller = 'adminhtml_jetattribute';
	    $this->_blockGroup = 'jet';
	    $this->_headerText = 'Attribute management';
	    //$this->_addButtonLabel = 'Import New attribute';
	    parent::__construct();
		$this->_removeButton('add');
	   
    }
}
