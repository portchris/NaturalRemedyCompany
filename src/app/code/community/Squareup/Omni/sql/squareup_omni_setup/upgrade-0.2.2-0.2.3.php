<?php
$installer = $this;
$installer->startSetup();

$installer->updateAttribute(
    'catalog_product',
    'square_id',
    'is_unique',
    '1'
);

$installer->updateAttribute(
    'catalog_product',
    'square_variation_id',
    'is_unique',
    '1'
);

$installer->endSetup();