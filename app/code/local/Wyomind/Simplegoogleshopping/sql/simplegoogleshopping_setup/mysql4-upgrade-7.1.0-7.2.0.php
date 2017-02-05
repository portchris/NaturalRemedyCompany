<?php
/**
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
$installer = $this;

$installer->startSetup();

$actualCollection = Mage::getModel('simplegoogleshopping/simplegoogleshopping')->getCollection();
$actualCollection->addFieldToFilter('simplegoogleshopping_filename', 'GoogleShopping_datafeed.xml');

if ($actualCollection->count() == 0) {
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

    $pattern = '<!-- Basic Product Information -->
    <g:id>{sku}</g:id>
    <title>{name,[substr],[70],[...]}</title>
    <description>{description,[html_entity_decode],[strip_tags]}</description>
    {G:GOOGLE_PRODUCT_CATEGORY}
    {G:PRODUCT_TYPE,[10]}
    <link>{url parent}</link>
    {G:IMAGE_LINK}
    <g:condition>new</g:condition>

    <!-- Availability & Price -->
    <g:availability>{is_in_stock?[in stock]:[out of stock]:[available for order]}</g:availability>
    <g:price>{normal_price,[USD],[0]} USD</g:price>
    {G:SALE_PRICE,[USD],[0]}

    <!-- Unique Product Identifiers-->
    <g:brand>{manufacturer}</g:brand>
    <g:gtin>{upc}</g:gtin>
    <g:mpn>{sku}</g:mpn>
    <g:identifier_exists>TRUE</g:identifier_exists>

    <!-- Apparel Products -->
    <g:gender>{gender}</g:gender>
    <g:age_group>{age_group}</g:age_group>
    <g:color>{color}</g:color>
    <g:size>{size}</g:size>

    <!-- Product Variants -->
    {G:ITEM_GROUP_ID}
    <g:material>{material}</g:material>
    <g:pattern>{pattern}</g:pattern>

    <!-- Shipping -->
    <g:shipping_weight>{weight,[float],[2]}kg</g:shipping_weight>

    <!-- AdWords attributes -->
    <g:adwords_grouping>{adwords_grouping}</g:adwords_grouping>
    <g:adwords_labels>{adwords_labels}</g:adwords_labels>';

    $data = array(
        'simplegoogleshopping_id' => null,
        'simplegoogleshopping_filename' => 'GoogleShopping_datafeed.xml', 
        'simplegoogleshopping_path' => '/', 
        'simplegoogleshopping_time' => null, 
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
}

$installer->endSetup();