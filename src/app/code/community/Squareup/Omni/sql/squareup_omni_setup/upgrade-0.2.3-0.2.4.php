<?php
$installer = $this;
$installer->startSetup();

$installer->run(
    "ALTER TABLE {$installer->getTable('sales/quote_payment')}
    ADD save_square_card VARCHAR( 255 ) NULL;
      
    ALTER TABLE {$installer->getTable('sales/order_payment')}
    ADD save_square_card VARCHAR( 255 ) NULL;"
);

//Add square_saved_cards attribute
$installer->addAttribute(
    'customer',
    'square_saved_cards',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'input' => 'text',
        'label' => 'Square Saved Cards',
        'global' => 1,
        'visible' => 0,
        'required' => 0,
        'user_defined' => 0,
        'default' => null,
        'visible_on_front' => 0,
        'source' =>   NULL,
        'comment' => 'Square Saved Cards'
    )
);

$installer->endSetup();