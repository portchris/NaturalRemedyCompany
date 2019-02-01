<?php

$installer = $this;

$installer->startSetup();

$setup = Mage::getModel('customer/entity_setup', 'core_setup');

//Add squareup_customer_id attribute
$setup->addAttribute(
    'customer',
    'squareup_customer_id',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'input' => 'text',
        'label' => 'Squareup Customer Id',
        'global' => 1,
        'visible' => 0,
        'required' => 0,
        'user_defined' => 0,
        'default' => '',
        'visible_on_front' => 0,
        'source' =>   NULL,
        'comment' => 'Square Customer Id'
    )
);

$installer->endSetup();