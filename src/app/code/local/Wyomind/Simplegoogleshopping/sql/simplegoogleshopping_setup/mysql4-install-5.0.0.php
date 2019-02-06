<?php
/**
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
$installer = $this;

$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$this->getTable('simplegoogleshopping')};");

$table = $installer->getConnection()
        ->newTable($installer->getTable('simplegoogleshopping'))
        ->addColumn(
            'simplegoogleshopping_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'primary'   => true,
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false
            )
        )
        ->addColumn(
            'simplegoogleshopping_filename', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'length'    => 255,
            'nullable'  => true,
            'default'   => null
            )
        )
        ->addColumn(
            'simplegoogleshopping_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'length'    => 255,
            'nullable'  => true,
            'default'   => null
            )
        )
        ->addColumn(
            'simplegoogleshopping_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
            'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE
            )
        )
        ->addColumn(
            'store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'nullable'  => false,
            'default'   => 1
            )
        )
        ->addColumn(
            'simplegoogleshopping_url', Varien_Db_Ddl_Table::TYPE_TEXT, 120, array(
            'nullable'  => true,
            'default'   => null
            )
        )
        ->addColumn('simplegoogleshopping_title', Varien_Db_Ddl_Table::TYPE_TEXT)
        ->addColumn('simplegoogleshopping_description', Varien_Db_Ddl_Table::TYPE_TEXT)
        ->addColumn('simplegoogleshopping_xmlitempattern', Varien_Db_Ddl_Table::TYPE_TEXT)
        ->addColumn('simplegoogleshopping_categories', Varien_Db_Ddl_Table::TYPE_TEXT)
        ->addColumn(
            'simplegoogleshopping_type_ids', Varien_Db_Ddl_Table::TYPE_TEXT, 150, array(
            'nullable'  => true,
            'default'   => null
            )
        )
        ->addColumn(
            'simplegoogleshopping_visibility', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
            'nullable'  => true,
            'default'   => null
            )
        )
        ->addColumn('simplegoogleshopping_attributes', Varien_Db_Ddl_Table::TYPE_TEXT)
        ->addColumn(
            'cron_expr', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
            'default'   => '0 4 * * *'
            )
        );
$installer->getConnection()->createTable($table);

$categories = '*';

if (false !== strstr($_SERVER['HTTP_HOST'], 'wyomind.com')) {
    $categories = '[{"line": "1/3", "checked": false, "mapping": ""}, '
        . '{"line": "1/3/10", "checked": false, "mapping": ""}, '
        . '{"line": "1/3/10/22", "checked": false, "mapping": "Furniture > Living Room Furniture"}, '
        . '{"line": "1/3/10/23", "checked": false, "mapping": "Furniture > Bedroom Furniture"}, '
        . '{"line": "1/3/13", "checked": false, "mapping": ""}, '
        . '{"line": "1/3/13/12", "checked": false, "mapping": "Cameras & Optics"}, '
        . '{"line": "1/3/13/12/25", "checked": false, "mapping": "Cameras & Optics > Camera & Optic Accessories"}, '
        . '{"line": "1/3/13/12/26", "checked": false, "mapping": "Cameras & Optics > Cameras > Digital Cameras"}, '
        . '{"line": "1/3/13/15", "checked": false, "mapping": ""}, '
        . '{"line": "1/3/13/15/27", "checked": false, "mapping": "Electronics > Computers > Desktop Computers"}, '
        . '{"line": "1/3/13/15/28", "checked": false, "mapping": "Electronics > Computers > Desktop Computers"}, '
        . '{"line": "1/3/13/15/29", "checked": false, "mapping": "Electronics > Computers > Computer Accessorie"}, '
        . '{"line": "1/3/13/15/30", "checked": false, "mapping": "Electronics > Computers > Computer Accessorie"}, '
        . '{"line": "1/3/13/15/31", "checked": false, "mapping": "Electronics > Computers > Computer Accessorie"}, '
        . '{"line": "1/3/13/15/32", "checked": false, "mapping": "Electronics > Computers > Computer Accessorie"}, '
        . '{"line": "1/3/13/15/33", "checked": false, "mapping": "Electronics > Computers > Computer Accessorie"}, '
        . '{"line": "1/3/13/15/34", "checked": false, "mapping": "Electronics > Computers > Computer Accessorie"}, '
        . '{"line": "1/3/13/8", "checked": false, '
            . '"mapping": "Electronics > Communications > Telephony > Mobile Phones"}, '
        . '{"line": "1/3/18", "checked": false, "mapping": ""}, '
        . '{"line": "1/3/18/19", "checked": false, '
            . '"mapping": "Apparel & Accessories > Clothing > Activewear > Sweatshirts"}, '
        . '{"line": "1/3/18/24", "checked": false, "mapping": "Apparel & Accessories > Clothing > Pants"}, '
        . '{"line": "1/3/18/4", "checked": false, "mapping": "Apparel & Accessories > Clothing > Tops > Shirts"}, '
        . '{"line": "1/3/18/5", "checked": false, "mapping": "Apparel & Accessories > Shoes"}, '
        . '{"line": "1/3/18/5/16", "checked": false, "mapping": "Apparel & Accessories > Shoes"}, '
        . '{"line": "1/3/18/5/17", "checked": false, "mapping": "Apparel & Accessories > Shoes"}, '
        . '{"line": "1/3/20", "checked": false, "mapping": ""}]';
}

$pattern = '<g:id>{sku}</g:id>
<title>{name,[substr],[70],[...]}</title>
<link>{url}</link>
<!-- you must change the currency unit or convert the price to match 
with your magento store and your merchant account setting -->
<g:price>{normal_price,[USD]} </g:price>
{G:SALE_PRICE,[USD]}
<g:online_only>y</g:online_only>
<description>{description,[html_entity_decode],[strip_tags]}</description>
<g:condition>new</g:condition>
{G:PRODUCT_TYPE}
{G:GOOGLE_PRODUCT_CATEGORY}
{G:IMAGE_LINK}
<g:availability>{is_in_stock?[in stock]:[out of stock]}</g:availability>
<g:quantity>{qty}</g:quantity>
<g:featured_product>{is_special_price?[1]:[0]} </g:featured_product>
<g:color>{color,[implode],[,]}</g:color>
<g:shipping_weight>{weight,[float],[2]} kilograms</g:shipping_weight>
{G:PRODUCT_REVIEW}
<g:manufacturer>{manufacturer}</g:manufacturer>
<!-- In most of cases brand + mpn are sufficient, eg. :-->
<g:brand>{manufacturer}</g:brand>
<g:mpn>{sku}</g:mpn>
<!-- But it is better to use one of these identifiers if available : EAN, ISBN or UPC, eg : -->
<g:gtin>{upc}</g:gtin>';

$data = array(
    'simplegoogleshopping_filename' => 'GoogleShopping_standard.xml',
    'simplegoogleshopping_path' => '/',
    'simplegoogleshopping_time' => '2011-07-10 20:26:36',
    'store_id' => 1,
    'simplegoogleshopping_url' => 'http://wwww.website.com',
    'simplegoogleshopping_title' => 'Data feed title',
    'simplegoogleshopping_description' => 'Data feed description',
    'simplegoogleshopping_xmlitempattern' => $pattern,
    'simplegoogleshopping_categories' => $categories,
    'simplegoogleshopping_type_ids' => 'simple,configurable,bundle,virtual,downloadable',
    'simplegoogleshopping_visibility' => '1,2,3,4',
    'simplegoogleshopping_attributes' => '[{"line": "0", "checked": true, "code": "price", "condition": "gt", '
    . '"value": "0"}, {"line": "1", "checked": true, "code": "sku", "condition": "notnull", "value": ""}, '
    . '{"line": "2", "checked": true, "code": "name", "condition": "notnull", "value": ""}, '
    . '{"line": "3", "checked": true, "code": "short_description", "condition": "notnull", "value": ""}, '
    . '{"line": "4", "checked": false, "code": "cost", "condition": "eq", "value": ""}, '
    . '{"line": "5", "checked": false, "code": "cost", "condition": "eq", "value": ""}, '
    . '{"line": "6", "checked": false, "code": "cost", "condition": "eq", "value": ""}, '
    . '{"line": "7", "checked": false, "code": "cost", "condition": "eq", "value": ""}, '
    . '{"line": "8", "checked": false, "code": "cost", "condition": "eq", "value": ""}, '
    . '{"line": "9", "checked": false, "code": "cost", "condition": "eq", "value": ""}, '
    . '{"line": "10", "checked": false, "code": "cost", "condition": "eq", "value": ""}]'
);

$model = Mage::getSingleton('simplegoogleshopping/simplegoogleshopping')->setData($data);
$model->save();


$pattern = '<g:id>{sku}</g:id>
{G:ITEM_GROUP_ID}
<title>{name,[substr],[70],[...]}</title>
<link>{url parent}</link>
<g:price>{normal_price,[USD]}</g:price>
{G:SALE_PRICE,[USD]}
<g:online_only>y</g:online_only>
<description>{description parent,[html_entity_decode],[strip_tags]}</description>
<g:condition>new</g:condition>
{G:PRODUCT_TYPE parent}
{G:GOOGLE_PRODUCT_CATEGORY parent}
{G:IMAGE_LINK parent}
<g:availability>{is_in_stock parent?[in stock]:[out of stock]}</g:availability>
<g:quantity>{qty}</g:quantity>
<g:featured_product>{is_special_price?[1]:[0]} </g:featured_product>
<g:color>{color,[implode],[,]}</g:color>
<g:shipping_weight>{weight,[float],[2]} kilograms</g:shipping_weight>
{G:PRODUCT_REVIEW}
<g:manufacturer>{manufacturer}</g:manufacturer>
<!-- In most of cases brand + mpn are sufficient, eg. :-->
<g:brand>{manufacturer}</g:brand>
<g:mpn>{sku}</g:mpn>
<!-- But it is better to use one of these identifiers if available : EAN, ISBN or UPC, eg : -->
<g:gtin>{upc}</g:gtin>';

$data = array(
    'simplegoogleshopping_filename' => 'GoogleShopping_configurable_products.xml',
    'simplegoogleshopping_path' => '/',
    'simplegoogleshopping_time' => '2011-08-01 12:00:00',
    'store_id' => 1,
    'simplegoogleshopping_url' => 'http://wwww.website.com',
    'simplegoogleshopping_title' => 'Export for configurable products',
    'simplegoogleshopping_description' => 'This template is designed to publish the different variants of all '
                                            . 'configurable, grouped or bundle products.',
    'simplegoogleshopping_xmlitempattern' => $pattern,
    'simplegoogleshopping_categories' => $categories,
    'simplegoogleshopping_type_ids' => 'simple',
    'simplegoogleshopping_visibility' => '1',
    'simplegoogleshopping_attributes' => '[{"line": "0", "checked": true, "code": "price", "condition": "gt", '
    . '"value": "0"}, {"line": "1", "checked": true, "code": "sku", "condition": "notnull", "value": ""}, '
    . '{"line": "2", "checked": true, "code": "name", "condition": "notnull", "value": ""}, '
    . '{"line": "3", "checked": true, "code": "short_description", "condition": "notnull", "value": ""}, '
    . '{"line": "4", "checked": false, "code": "cost", "condition": "eq", "value": ""}, '
    . '{"line": "5", "checked": false, "code": "cost", "condition": "eq", "value": ""}, '
    . '{"line": "6", "checked": false, "code": "cost", "condition": "eq", "value": ""}, '
    . '{"line": "7", "checked": false, "code": "cost", "condition": "eq", "value": ""}, '
    . '{"line": "8", "checked": false, "code": "cost", "condition": "eq", "value": ""}, '
    . '{"line": "9", "checked": false, "code": "cost", "condition": "eq", "value": ""}, '
    . '{"line": "10", "checked": false, "code": "cost", "condition": "eq", "value": ""}]'
);

$model = Mage::getSingleton('simplegoogleshopping/simplegoogleshopping')->setData($data);
$model->save();


$pattern = '<g:id>{sku}</g:id>
<title>{name,[substr],[70],[...]}</title>
<!-- Stock In The Channel : url -->
{SC:URL}
<g:price>{normal_price,[USD]} </g:price>
{G:SALE_PRICE,[USD]}
<g:online_only>y</g:online_only>
<!-- Stock In The Channel : description -->
{SC:DESCRIPTION,[htmlentities],[strip_tags]}
<g:condition>new</g:condition>
{G:PRODUCT_TYPE}
{G:GOOGLE_PRODUCT_CATEGORY}
<!-- Stock In The Channel : image -->
{SC:IMAGES}
<g:availability>{is_in_stock?[in stock]:[out of stock]}</g:availability>
<g:quantity>{qty}</g:quantity>
<g:featured_product>{is_special_price?[1]:[0]} </g:featured_product>
<g:color>{color,[implode],[,]}</g:color>
<g:shipping_weight>{weight,[float],[2]} kilograms</g:shipping_weight>
{G:PRODUCT_REVIEW}
<g:manufacturer>{manufacturer}</g:manufacturer>
<g:brand>{manufacturer}</g:brand>
<g:mpn>{sku}</g:mpn>
<!-- Stock In The Channel : ean -->
{SC:EAN}';

$data = array(
    'simplegoogleshopping_filename' => 'Special_StockInTheChannel.xml',
    'simplegoogleshopping_path' => '/',
    'simplegoogleshopping_time' => '2011-07-10 20:26:36',
    'store_id' => 1,
    'simplegoogleshopping_url' => 'http://wwww.website.com',
    'simplegoogleshopping_title' => 'Data feed title',
    'simplegoogleshopping_description' => 'Stock In The Channel Template',
    'simplegoogleshopping_xmlitempattern' => $pattern,
    'simplegoogleshopping_categories' => $categories,
    'simplegoogleshopping_type_ids' => 'simple',
    'simplegoogleshopping_visibility' => '2,3,4',
    'simplegoogleshopping_attributes' => '[{"line":"0","checked":true,"code":"price","condition":"gt","value":"0"},'
    . '{"line":"1","checked":true,"code":"sku","condition":"notnull","value":""},'
    . '{"line":"2","checked":true,"code":"name","condition":"notnull","value":""},'
    . '{"line":"3","checked":true,"code":"small_image","condition":"neq","value":""},'
    . '{"line":"4","checked":true,"code":"ean","condition":"notnull","value":""},'
    . '{"line":"5","checked":true,"code":"description","condition":"notnull","value":""},'
    . '{"line":"6","checked":true,"code":"description","condition":"neq","value":""},'
    . '{"line":"7","checked":false,"code":"cost","condition":"eq","value":""},'
    . '{"line":"8","checked":false,"code":"cost","condition":"eq","value":""},'
    . '{"line":"9","checked":false,"code":"cost","condition":"eq","value":""},'
    . '{"line":"10","checked":false,"code":"cost","condition":"eq","value":""}]'
);

$model = Mage::getSingleton('simplegoogleshopping/simplegoogleshopping')->setData($data);
$model->save();

$installer->endSetup();