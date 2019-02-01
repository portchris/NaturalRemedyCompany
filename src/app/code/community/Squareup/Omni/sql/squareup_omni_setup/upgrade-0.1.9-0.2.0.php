<?php
$installer = $this;
$installer->startSetup();
// create refunds table
$refundsTable = $installer->getTable('squareup_omni/refunds');
if ($installer->getConnection()->isTableExists($refundsTable) != true) {
    $refundsTable = $installer->getConnection()
        ->newTable($refundsTable)
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
            'transaction_id',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable'  => false,
            ),
            'Transaction Id'
        )
        ->addColumn(
            'tender_id',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable'  => false,
            ),
            'Tender Id'
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
            'reason',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable'  => false,
            ),
            'Reason'
        )
        ->addColumn(
            'status',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'nullable'  => true,
            ),
            'Status'
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

    $installer->getConnection()->createTable($refundsTable);
}

$installer->endSetup();