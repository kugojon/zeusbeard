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

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', 'jet_repricing_minimum_price')->getId()) {
    $setup->addAttribute(
        'catalog_product', 'jet_repricing_minimum_price', array(
            'group'         => 'jetcom',
            'note'       =>'Minimum price of product you want to sell on jet.com',
            'input'         => 'text',
            'type'          => 'text',
            'label'         => 'Jet Repricing Minimum Price',
            'backend'       => '',
            'visible'       => 1,
            'required'        => 0,
            'user_defined' => 1,
            'searchable' => 1,
            'filterable' => 0,
            'comparable'    => 0,
            'visible_on_front' => 0,
            'visible_in_advanced_search'  => 0,
            'is_html_allowed_on_front' => 0,
            'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        )
    );
}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', 'jet_repricing_maximum_price')->getId()) {
    $setup->addAttribute(
        'catalog_product', 'jet_repricing_maximum_price', array(
            'group'         => 'jetcom',
            'note'       =>'Maximum price of product you want to sell on jet.com',
            'input'         => 'text',
            'type'          => 'text',
            'label'         => 'Jet Repricing Maximum Price',
            'backend'       => '',
            'visible'       => 1,
            'required'        => 0,
            'user_defined' => 1,
            'searchable' => 1,
            'filterable' => 0,
            'comparable'    => 0,
            'visible_on_front' => 0,
            'visible_in_advanced_search'  => 0,
            'is_html_allowed_on_front' => 0,
            'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        )
    );
}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', 'jet_repricing_bidding_price')->getId()) {
    $setup->addAttribute(
        'catalog_product', 'jet_repricing_bidding_price', array(
            'group'         => 'jetcom',
            'note'       =>'Bidding price of product which you want to vary by marketplace best price , eg: If Marketplace Best Price is $100 and your product price is $105 & Bidding Price is 1 , so $99 will go on jet.com ofcourse if this price greator or equal to your minimum price.',
            'input'         => 'text',
            'type'          => 'text',
            'label'         => 'Jet Repricing Bidding Price',
            'backend'       => '',
            'visible'       => 1,
            'required'        => 0,
            'user_defined' => 1,
            'searchable' => 1,
            'filterable' => 0,
            'comparable'    => 0,
            'visible_on_front' => 0,
            'visible_in_advanced_search'  => 0,
            'is_html_allowed_on_front' => 0,
            'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        )
    );
}

$installer->endSetup();    