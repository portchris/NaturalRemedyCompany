<?php
$installer = $this;
$installer->startSetup();
// create location table
$locationTable = $installer->getTable('squareup_omni/location');
if ($installer->getConnection()->isTableExists($locationTable) != true) {
    $locationTable = $installer->getConnection()
        ->newTable($locationTable)
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
                'nullable'  => true,
            ),
            'Square Id'
        )
        ->addColumn(
            'name',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            null,
            array(
                'nullable'  => false,
            ),
            'Name'
        )
        ->addColumn(
            'address_line_1',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable'  => true,
            ),
            'Address Line 1'
        )
        ->addColumn(
            'locality',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable'  => true,
            ),
            'Locality'
        )
        ->addColumn(
            'administrative_district_level_1',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable'  => true,
            ),
            'Administrative District Level 1'
        )
        ->addColumn(
            'postal_code',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable'  => true,
            ),
            'Postal Code'
        )
        ->addColumn(
            'country',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable'  => true
            ),
            'Country'
        )
        ->addColumn(
            'phone_number',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable'  => true,
            ), 'Phone Number'
        )
        ->addColumn(
            'status',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'nullable'  => false
            ),
            'Status'
        )
        ->addIndex(
            $installer->getIdxName(
                'squareup_omni/location',
                array('square_id')
            ),
            array('square_id')
        );

    $installer->getConnection()->createTable($locationTable);
}

$installer->endSetup();