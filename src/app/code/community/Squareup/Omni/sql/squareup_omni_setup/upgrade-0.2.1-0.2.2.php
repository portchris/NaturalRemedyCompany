<?php
$installer = $this;
$installer->startSetup();
$resource = Mage::getSingleton('core/resource');
$connection = $installer->getConnection();

$salesTable = $installer->getTable('sales/order');
if ($connection->tableColumnExists($salesTable, 'square_order_id') === false) {
    $installer->getConnection()
        ->addColumn(
            $installer->getTable('sales/order'),
            'square_order_id',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => TRUE,
                'default' => null,
                'comment' => 'Square Order ID'
            )
        );
}

$installer->endSetup();