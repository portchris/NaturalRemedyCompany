<?php

$attrInstaller = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$attrInstaller->startSetup();

//Create new Square variation Id attribute
$attrInstaller->addAttribute(
    'catalog_product',
    'square_variation_id',
    array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Square Variation Id',
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => true,
        'default'           => null,
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

$productTypes = array(
    Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
);
$productTypes  = implode(',', $productTypes);

$attrInstaller->addAttribute(
    'catalog_product',
    'square_variation',
    array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Square Variation',
        'input'             => 'select',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'is_configurable'   => true,
        'apply_to'          => $productTypes,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => true,
        'default'           => null,
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