<?php

$installer = $this;

$installer->startSetup();

$ordersSelect = (string) $installer->getConnection()->select()
    ->from(array('data' => $installer->getTable('aw_giftwrap/order_wrap')), array('order_id'))
;

$installer->getConnection()->addColumn(
    $installer->getTable('sales/order_grid'),
    'gift_wrapped',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'comment'   => 'Gift Wrapped',
        'length'    => 1,
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    )
);

$installer->getConnection()->update(
    $installer->getTable('sales/order_grid'), 
    array('gift_wrapped' => 1), 
    'entity_id IN (' . $ordersSelect . ')'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales/order'),
    'gift_wrapped',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'comment'   => 'Gift Wrapped',
        'length'    => 1,
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    )
);

$installer->getConnection()->update(
    $installer->getTable('sales/order'), 
    array('gift_wrapped' => 1), 
    'entity_id IN (' . $ordersSelect . ')'
);

$installer->endSetup();
