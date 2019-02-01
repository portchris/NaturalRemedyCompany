<?php

$attrInstaller = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$attrInstaller->startSetup();

//Create new Square updated atId attribute
$attrInstaller->addAttribute(
    'catalog_product',
    'square_product_image',
    array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Square Product Image',
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => false,
        'required'          => false,
        'user_defined'      => true,
        'default'           => null,
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'system'            => false,
        'used_in_product_listing' => false,
        'group'             => 'General'
    )
);

$attrInstaller->endSetup();