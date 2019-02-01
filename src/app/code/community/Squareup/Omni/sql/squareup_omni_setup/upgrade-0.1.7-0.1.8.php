<?php
$installer = $this;
$installer->startSetup();
// create transaction table
$transactionTable = $installer->getTable('squareup_omni/transaction');
if ($installer->getConnection()->isTableExists($transactionTable) != true) {
    $transactionTable = $installer->getConnection()
        ->newTable($transactionTable)
        ->addColumn(
            'id',
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
            'square_id',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable'  => false,
            ),
            'Square Id'
        )
        ->addColumn(
            'location_id',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable'  => false,
            ),
            'Location Id'
        )
        ->addColumn(
            'amount',
            Varien_Db_Ddl_Table::TYPE_DECIMAL,
            '12,4',
            array(),
            'Amount Money'
        )
        ->addColumn(
            'processing_fee_amount',
            Varien_Db_Ddl_Table::TYPE_DECIMAL,
            '12,4',
            array(),
            'Processing Fee Money'
        )
        ->addColumn(
            'type',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'nullable'  => true,
            ),
            'Type'
        )
        ->addColumn(
            'card_brand',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            null,
            array(
                'nullable'  => true,
            ),
            'Card Brand'
        )
        ->addColumn(
            'note',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            null,
            array(
                'nullable'  => true,
            ),
            'Note'
        )
        ->addColumn(
            'created_at',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            array(
                'nullable'  => true,
            ),
            'Created At'
        );

    $installer->getConnection()->createTable($transactionTable);
}

$installer->endSetup();