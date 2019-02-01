<?php

$installer = $this;

$attrInstaller = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$attrInstaller->startSetup();

//Create new Square Id attribute
$attrInstaller->addAttribute(
    'catalog_product',
    'square_id',
    array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Square Id',
        'input'             => 'text',
        'class'             => '',
        'source'            => 'catalog/product_attribute_source_layout',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => true,
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'system'            => false,
        'used_in_product_listing' => true,
        'group'             => 'General'
    )
);
$attrInstaller->endSetup();