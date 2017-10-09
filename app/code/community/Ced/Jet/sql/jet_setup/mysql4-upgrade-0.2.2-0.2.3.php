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
$this->getConnection()->addColumn($this->getTable('jet/jetorder'), 'order_data', 'TEXT DEFAULT NULL AFTER `status`');
$this->getConnection()->addColumn($this->getTable('jet/jetorder'), 'shipment_data', 'TEXT DEFAULT NULL AFTER `order_data`');
$this->getConnection()->addColumn($this->getTable('jet/jetorder'), 'reference_order_id', 'TEXT DEFAULT NULL AFTER `shipment_data`');

  
$installer->endSetup();   
$installer->startSetup();
$installer->run("
  ALTER TABLE {$this->getTable('jet/jetorder')} CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
  ALTER TABLE {$this->getTable('jet/jetorder')} CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
");
$installer->endSetup();
