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
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('jet/profile')} (
  `id` int(10) unsigned NOT NULL auto_increment,
  `profile_code` varchar(40) character set utf8 NOT NULL default '',
  `profile_status`  smallint(3)  NOT NULL default '1',
  `profile_name` varchar(50) character set utf8 NOT NULL default '',
  `node_id` varchar(50) character set utf8 NOT NULL default '',
  `profile_attribute_mapping` text character set utf8 NOT NULL default '',
   PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Jet profiles';
");

$installer->getConnection()->addKey($this->getTable('jet/profile'), 'UNQ_JET_PROFILECODE', array('profile_code'), 'unique');

$table = $installer->getConnection()
    ->newTable($installer->getTable('jet/profileproducts'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'auto_increment' => true,
    ), 'Id')
    ->addColumn('profile_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Profile Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
    ), 'Product Id')
    ->addForeignKey(
        $installer->getFkName(
            'jet/profileproducts',
            'product_id',
            'catalog/product',
            'entity_id'
        ),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'jet/profileproducts',
            'profile_id',
            'jet/profile',
            'id'
        ),
        'profile_id', $installer->getTable('jet/profile'), 'id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);


$installer->getConnection()->addKey($installer->getTable('jet/profileproducts'), 'UNQ_JET_PROFILE_PRODUCT', array('profile_id', 'product_id'), 'unique');
$installer->getConnection()->addKey($installer->getTable('jet/profileproducts'), 'UNQ_JET_PRODUCT', array('product_id'), 'unique');




$installer->run("
  ALTER TABLE {$this->getTable('jet/fileinfo')} ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `id`,
   ADD `error` text DEFAULT NULL AFTER `status`,
   ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at` ;
  
");

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','jet_product_validation')->getId()) {
    $setup->addAttribute('catalog_product', 'jet_product_validation', array(
        'group'     	=> 'jetcom',
        'note'	   =>'Jet product validation',
        'input'         => 'hidden',
        'source'			=> 'Ced_Jet_Model_Source_Productvalidation',
        'type'          => 'varchar',
        'label'         => 'Jet product validation',
        'backend'       => '',
        'visible'       => 1,
        'required'		=> 0,
        'user_defined' => 1,
        'searchable' => 0,
        'filterable' => 0,
        'comparable'	=> 0,
        'visible_on_front' => 0,
        'visible_in_advanced_search'  => 0,
        'is_html_allowed_on_front' => 0,
        'default' => 'not_validated',
        'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    ));
}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','jet_product_validation_error')->getId()) {
    $setup->addAttribute('catalog_product', 'jet_product_validation_error', array(
        'group'     	=> 'jetcom',
        'note'	   =>'Jet product validation Error',
        'input'         => 'hidden',
        'type'          => 'text',
        'label'         => 'Jet product validation Error',
        'backend'       => '',
        'visible'       => 0,
        'required'		=> 0,
        'user_defined' => 1,
        'searchable' => 0,
        'filterable' => 0,
        'comparable'	=> 0,
        'visible_on_front' => 0,
        'visible_in_advanced_search'  => 0,
        'is_html_allowed_on_front' => 0,
        'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    ));
}



if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','standard_identifier')->getId()) {
    $setup->addAttribute('catalog_product', 'standard_identifier', array(
        'group'     	=> 'jetcom',
        'note'	   =>'Required Standard Identifier',
        'input'         => 'text',
        'type'          => 'text',
        'label'         => 'Standard Identifier',
        'backend'       => 'jet/product_attribute_backend_standardidentifier',
        'visible'       => 0,
        'required'		=> 0,
        'sort_order' => 0,
        'user_defined' => 1,
        'searchable' => 0,
        'filterable' => 0,
        'comparable'	=> 0,
        'visible_on_front' => 0,
        'visible_in_advanced_search'  => 0,
        'is_html_allowed_on_front' => 0,
        'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    ));
}


$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('jet/productchange')} (
  `id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int NOT NULL,
  `old_value`  text,
  `new_value` text,
  `action` varchar(50) character set utf8 NOT NULL default '',
  `cron_type` varchar(50)  character set utf8 NOT NULL default '',
   PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Jet Product Change';
");
$installer->endSetup();
