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
$this->getConnection()->addColumn($this->getTable('jet/catlist'), 'jetattr_names', 'TEXT DEFAULT NULL AFTER `attribute_ids`');
$this->getConnection()->dropColumn($this->getTable('jet/catlist'), 'path');
$this->getConnection()->addColumn($this->getTable('jet/catlist'), 'jet_tax_code', 'TEXT DEFAULT NULL AFTER `level`');
$installer->endSetup();
