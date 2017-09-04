<?php
$this->startSetup();

/**
 * Note: there are many ways in Magento to achieve the same result of
 * creating a database table. For this tutorial, we have gone with the
 * Varien_Db_Ddl_Table method, but feel free to explore what Magento
 * does in CE 1.8.0.0 and earlier versions.
 */
$table = new Varien_Db_Ddl_Table();

/**
 * This is an alias of the real name of our database table, which is
 * configured in config.xml. By using an alias, we can refer to the same
 * table throughout our code if we wish, and if the table name ever has
 * to change, we can simply update a single location, config.xml
 * - smashingmagazine_branddirectory is the model alias
 * - brand is the table reference
 */
$table->setName($this->getTable('custombundleitem/items'));

/**
 * Add the columns we need for now. If you need more later, you can
 * always create a new setup script as an upgrade. We will introduce
 * that later in this tutorial.
 */
$table->addColumn(
    'id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    10,
    array(
        'auto_increment' => true,
        'unsigned' => true,
        'nullable'=> false,
        'primary' => true
    )
);
$table->addColumn(
    'store_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    null,
    array(
        'nullable' => false,
    )
);
$table->addColumn(
    'product_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    null,
    array(
        'nullable' => false,
    )
);

$table->addColumn(
    'option_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    null,
    array(
        'nullable' => false,
    )
);
$table->addColumn(
    'is_hidden',
    Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    null,
    array(
        'nullable' => false,
    )
);


/**
 * These two important lines are often missed.
 */
$table->setOption('type', 'InnoDB');
$table->setOption('charset', 'utf8');

/**
 * Create the table!
 */
$this->getConnection()->createTable($table);

$this->endSetup();