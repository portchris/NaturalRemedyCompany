<?php
/**
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('simplegoogleshopping'), 'simplegoogleshopping_category_type', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'length'    => 1,
        'nullable'  => true,
        'default'   => 0,
        'comment'   => 'Category type'
    )
);

$installer->getConnection()->addColumn(
    $installer->getTable('simplegoogleshopping'), 'simplegoogleshopping_report', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'comment'   => 'Report'
    )
);

$actualCollection = Mage::getModel('simplegoogleshopping/simplegoogleshopping')->getCollection();
$actualCollection->addFieldToFilter(
    'simplegoogleshopping_filename', 
    array('GoogleShopping_full.xml', 'GoogleShopping_inventory.xml')
);

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
{G:ID}
{G:TITLE}
{G:LINK}
{G:DESCRIPTION}
{G:GOOGLE_PRODUCT_CATEGORY}
{G:PRODUCT_TYPE,[10]}
{G:IMAGE_LINK}
<g:condition>{condition}</g:condition>

<!-- Availability & Price -->
{G:AVAILABILITY}
{G:PRICE,[USD],[0]}
{G:SALE_PRICE,[USD],[0]}

<!-- Unique Product Identifiers-->
<g:brand>{brand}</g:brand>
<g:gtin>{upc}</g:gtin>
<g:mpn>{mpn}</g:mpn>
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
<g:custom_label_0>{custom_label_0}</g:custom_label_0>
<g:custom_label_1>{custom_label_1}</g:custom_label_1>
<g:custom_label_2>{custom_label_2}</g:custom_label_2>
<g:custom_label_3>{custom_label_3}</g:custom_label_3>
<g:custom_label_4>{custom_label_4}</g:custom_label_4>';

    $now = Mage::getSingleton('core/date')->date('Y-m-d H:i:s');

    $collection = Mage::getSingleton('core/store')->getCollection();
    $collection->getSelect()->limit(1, 0);
    $storeId = $collection->getFirstItem()->getStoreId();
        $fullDatafeed = array(
        'simplegoogleshopping_id' => null,
        'simplegoogleshopping_filename' => 'GoogleShopping_full.xml',
        'simplegoogleshopping_path' => '/',
        'simplegoogleshopping_time' => $now,
        'store_id' => $storeId,
        'simplegoogleshopping_url' => 'http//www.example.com',
        'simplegoogleshopping_title' => 'Full data feed',
        'simplegoogleshopping_description' => 'This is the main feed for your online product data for Google Shopping '
                                            . 'and should be submitted at least every 30 days.',
        'simplegoogleshopping_xmlitempattern' => $pattern,
        'simplegoogleshopping_categories' => $categories,
        'simplegoogleshopping_category_filter' => 1,
        'simplegoogleshopping_category_type' => 0,
        'simplegoogleshopping_type_ids' => 'simple,configurable,bundle,grouped,virtual,downloadable',
        'simplegoogleshopping_visibility' => '1,2,3,4',
        'simplegoogleshopping_attribute_sets' => '*',
        'simplegoogleshopping_attributes' => '[]',
        'cron_expr' => '{"days":["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"], '
                    . '"hours":["04:00"]}',
        'simplegoogleshopping_report' => null
    );

    $fullDatafeedModel = Mage::getSingleton('simplegoogleshopping/simplegoogleshopping')->setData($fullDatafeed);
    $fullDatafeedModel->save();

    $inventoryDatafeed = array(
        'simplegoogleshopping_id' => null,
        'simplegoogleshopping_filename' => 'GoogleShopping_inventory.xml',
        'simplegoogleshopping_path' => '/',
        'simplegoogleshopping_time' => $now,
        'store_id' => $storeId,
        'simplegoogleshopping_url' => 'http//www.example.com',
        'simplegoogleshopping_title' => 'Inventory data feed',
        'simplegoogleshopping_description' => 'Submit this feed throughout the day to update your price, '
                                        . 'availability and/or sale price information for specific items already '
                                        . 'submitted in your full product feed.',
        'simplegoogleshopping_xmlitempattern' => '{G:ID}
{G:AVAILABILITY}
{G:PRICE,[USD],[0]}
{G:SALE_PRICE,[USD],[0]}',
        'simplegoogleshopping_categories' => $categories,
        'simplegoogleshopping_category_filter' => 1,
        'simplegoogleshopping_category_type' => 0,
        'simplegoogleshopping_type_ids' => 'simple,configurable,bundle,grouped,virtual,downloadable',
        'simplegoogleshopping_visibility' => '1,2,3,4',
        'simplegoogleshopping_attribute_sets' => '*',
        'simplegoogleshopping_attributes' => '[]',
        'cron_expr' => '{"days":["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"], '
                    . '"hours":["05:00"]}',
        'simplegoogleshopping_report' => null
    );

    $inventoryDatafeedModel = Mage::getSingleton('simplegoogleshopping/simplegoogleshopping')
                                ->setData($inventoryDatafeed);
    $inventoryDatafeedModel->save();
}

$installer->endSetup();