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

class Ced_Jet_Block_Adminhtml_Liveproducts extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $ur = Mage::helper('adminhtml')->getUrl('*/');
        $zz = array();
        $zz = explode('index/index', $ur);
        $this->_controller = 'adminhtml_liveproducts';
        $this->_blockGroup = 'jet';
        $this->_headerText = 'Jet Live Products Management';
        $this->_addButton(
            "analyze_all_products", array(
                    "label"     => Mage::helper("jet")->__("Analyze All Products"),
                    "onclick"   => "analysisAll('".$zz[0]."')",
                    "class"     => "btn btn-danger",
            )
        );
        $this->_addButton(
            "get_all_product_price", array(
                    "label"     => Mage::helper("jet")->__("Get All Product Price"),
                    "onclick"   => "priceAll('".$zz[0]."')",
                    "class"     => "btn btn-danger",
            )
        );
        $this->_addButton(
            "get_all_product_qty", array(
                    "label"     => Mage::helper("jet")->__("Get All Product Qty"),
                    "onclick"   => "qtyAll('".$zz[0]."')",
                    "class"     => "btn btn-danger",
            )
        );
        $this->_addButton(
            "archive_all_products", array(
                    "label"     => Mage::helper("jet")->__("Archive All Product"),
                    "onclick"   => "archieveAll('".$zz[0]."')",
                    "class"     => "btn btn-danger",
            )
        );
        $this->_addButton(
            "unarchive_all_products", array(
                    "label"     => Mage::helper("jet")->__("Unarchive All Product"),
                   "onclick"   => "unarchieveAll('".$zz[0]."')",
                    "class"     => "btn btn-danger",
            )
        );
        $this->_addButton(
            "enable_vacation_mode", array(
                    "label"     => Mage::helper("jet")->__("Enable Vacation Mode"),
                    "onclick"   => "location.href = '".$this->getUrl('adminhtml/adminhtml_jetedit/enablevacationmode')."';",
                    "class"     => "btn btn-danger",
            )
        );
        $this->_addButton(
            "disable_vacation_mode", array(
                    "label"     => Mage::helper("jet")->__("Disable Vacation Mode"),
                    "onclick"   => "location.href = '".$this->getUrl('adminhtml/adminhtml_jetedit/disablevacationmode')."';",
                    "class"     => "btn btn-danger",
            )
        );
        parent::__construct();
        $this->removeButton('add');
    }
}