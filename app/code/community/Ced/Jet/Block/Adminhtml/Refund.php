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

class Ced_Jet_Block_Adminhtml_Refund extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_refund';
        $this->_blockGroup = 'jet';
        $this->_headerText = 'Refund management';
        $this->_addButtonLabel = 'Create New Refund';
        $this->_addButton(
            "Get Updated Refund Status", array(
                    "label"     => Mage::helper("jet")->__("Get Updated Refund Status"),
                    "onclick"   => "location.href = '".$this->getUrl('adminhtml/adminhtml_jetrefundsettlement/updaterefund')."';",
                    "class"     => "btn btn-danger",
            )
        );
        parent::__construct();
    }
}
