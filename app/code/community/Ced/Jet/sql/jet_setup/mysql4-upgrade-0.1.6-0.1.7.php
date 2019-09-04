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
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$sNewSetName = 'jetcom';
$iCatalogProductEntityTypeId = (int) $setup->getEntityTypeId('catalog_product');

$oAttributeset = Mage::getModel('eav/entity_attribute_set')
    ->setEntityTypeId($iCatalogProductEntityTypeId)
    ->setAttributeSetName($sNewSetName);

if ($oAttributeset->validate()) {
    $setup->addAttributeGroup('catalog_product', 'Default', 'jetcom', 1000);
} 
 
 
if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', 'upc')->getId()) {
    $setup->addAttribute(
        'catalog_product', 'upc', array(
        'group'         => 'jetcom',
        'note'       =>'If standard_product_code_type is "UPC" - must be 12 digits. Still not using upc refer this url : http://cedcommerce.com/blog/generate-upc-code/',    
        'input'         => 'text',
        'type'          => 'text',
        'label'         => 'UPC',
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

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', 'asin')->getId()) {
    $setup->addAttribute(
        'catalog_product', 'asin', array(
        'group'         => 'jetcom',
        'note'       =>'If standard_product_code_type is "ASIN" - must be 10 digit alphanumeric',
        'input'         => 'text',
        'type'          => 'text',
        'label'         => 'ASIN',
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


if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', 'ean')->getId()) {
    $setup->addAttribute(
        'catalog_product', 'ean', array(
        'group'         => 'jetcom',
        'note'       =>'If standard_product_code_type is "EAN" - must be 13 digits',    
        'input'         => 'text',
        'type'          => 'text',
        'label'         => 'EAN',
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
