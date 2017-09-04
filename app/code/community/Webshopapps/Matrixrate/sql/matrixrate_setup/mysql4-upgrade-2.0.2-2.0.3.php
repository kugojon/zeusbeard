<?php

$installer = $this;

$installer->startSetup();

$installer->run("
                ALTER TABLE `{$this->getTable('shipping_matrixrate')}` 
                ADD COLUMN `order` int(5) default '0' after delivery_type;
    ");

$installer->endSetup();