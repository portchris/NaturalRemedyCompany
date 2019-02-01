<?php

$installer = $this;

$installer->startSetup();

$setup = Mage::getModel('customer/entity_setup', 'core_setup');

/* Add squareup_just_imported attribute */
$setup->addAttribute(
    'customer',
    'squareup_just_imported',
    array(
        'type' => 'int',
        'input' => 'text',
        'label' => 'Squareup Just Imported',
        'global' => 1,
        'visible' => 0,
        'required' => 0,
        'user_defined' => 0,
        'default' => 0,
        'visible_on_front' => 0,
        'source' =>   NULL,
        'comment' => 'Squareup Just Imported'
    )
);

$installer->endSetup();