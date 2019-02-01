<?php

$installer = $this;

$installer->startSetup();

$setup = Mage::getModel('customer/entity_setup', 'core_setup');

/* Add squareup_updated_at attribute */
$setup->addAttribute(
    'customer',
    'square_updated_at',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'input' => 'text',
        'label' => 'Square Updated At',
        'global' => 1,
        'visible' => 0,
        'required' => 0,
        'user_defined' => 0,
        'default' => 0,
        'visible_on_front' => 0,
        'source' =>   NULL,
        'comment' => 'Square Updated At'
    )
);

$installer->endSetup();