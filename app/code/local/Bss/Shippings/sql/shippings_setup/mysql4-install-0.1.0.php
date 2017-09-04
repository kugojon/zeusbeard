<?php
$installer = $this;
$installer->startSetup();
$this->_conn->addColumn($this->getTable('sales_flat_quote'), 'shipping_arrival_carrier', 'text');
$this->_conn->addColumn($this->getTable('sales_flat_quote'), 'shipping_arrival_account_number', 'text');
$this->_conn->addColumn($this->getTable('sales_flat_order'), 'shipping_arrival_carrier', 'text');
$this->_conn->addColumn($this->getTable('sales_flat_order'), 'shipping_arrival_account_number', 'text');
$installer->endSetup();