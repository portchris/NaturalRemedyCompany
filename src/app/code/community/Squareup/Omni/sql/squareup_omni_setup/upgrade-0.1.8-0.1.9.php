<?php
$installer = $this;
$installer->startSetup();
$inventoryTable = $installer->getTable('squareup_omni/square_inventory');
if ($installer->getConnection()->isTableExists($inventoryTable) != true) {
    $inventoryTable = $installer->getConnection()
        ->newTable($inventoryTable)
        ->addColumn(
            'entity_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'Id'
        )
        ->addColumn(
            'product_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'nullable'  => false,
            ),
            'Product Id'
        )
        ->addColumn(
            'location_id',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable'  => false,
            ),
            'Square Location Id'
        )
        ->addColumn(
            'status',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            40,
            array(
                'nullable'  => false,
            ),
            'Status'
        )
        ->addColumn(
            'quantity',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'nullable'  => true,
            ),
            'Quantity'
        )
        ->addColumn(
            'calculated_at',
            Varien_Db_Ddl_Table::TYPE_DATETIME,
            null,
            array(
                'nullable'  => true,
            ),
            'Calculated At'
        )
        ->addColumn(
            'received_at',
            Varien_Db_Ddl_Table::TYPE_DATETIME,
            null,
            array(
                'nullable'  => false,
            ),
            'Received At'
        )
        ->addIndex(
            $installer->getIdxName(
                'squareup_omni/square_inventory',
                array('product_id')
            ),
            array('product_id')
        );

    $installer->getConnection()->createTable($inventoryTable);
}

$installer->endSetup();