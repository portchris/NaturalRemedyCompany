<?php
/**
 * SquareUp
 *
 * Import Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Catalog_Import extends Squareup_Omni_Model_Square
{
    protected $_confAttrId;
    protected $_entityTypeId = 4;
    protected $_errors = array();
    protected $_receivedIds = array();
    protected $_objects = array();
    protected $_locations = array();
    protected $_inventory;
    protected $_date;

    public function _construct()
    {
        $this->init();
        $this->_entityTypeId = Mage::getModel('catalog/product')->getResource()->getTypeId();
        $this->setConfAttrId();
        $this->_inventory = Mage::getModel('squareup_omni/inventory');
        $this->_date = Mage::getModel('core/date')->gmtDate();
    }

    public function start()
    {
        $this->_log->info('Start catalog import');
        $this->getProducts();
        $this->deleteProducts();
        $this->_log->info('End catalog import');
        return true;
    }

    public function getProducts()
    {
        $locations = Mage::getModel('squareup_omni/location')
            ->getCollection()
            ->addFieldToFilter('status', array('eq' => 1));

        foreach ($locations as $location) {
            $this->_locations[] = $location->getSquareId();
        }

        Mage::app()->setCurrentStore(0);
        $this->callSquare();

        return true;
    }

    public function updateProduct($item, $type, $id)
    {
        $product = Mage::getModel('catalog/product')->load($id);
        if ($product->getTypeId() != $type) {
            $this->changeProduct($item, $type, $product->getId());
            return true;
        }

        $product = $this->prepUpdateProduct($item, $type, $product);
        try {
            if ($product) {
                $product = $product->save();
                $this->_receivedIds[] = $item->getId();
                $this->_log->info('Product: #'. $product->getId() . ' was updated');
            } else {
                return false;
            }
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        if ($type == 'simple') {
            $this->addLocations($item, $id, true);
            return true;
        }

        foreach ($item->getItemData()->getVariations() as $key => $variation) {
            // need to update or create
            $idExists = $this->productExists($variation->getId());
            if ($idExists !== false) {
                $simpleProduct = Mage::getModel('catalog/product')->load($idExists);
                $childProduct = $this->prepUpdateProduct($variation, 'simple', $simpleProduct, $key);
                $this->saveProduct($childProduct);
                $this->_log->info('Product: #'. $childProduct->getId() . ' was updated');
                $this->addLocations($variation, $childProduct->getId(), true);
                $this->_receivedIds[] = $variation->getId();
            } else {
                $childIds = $product->getTypeInstance()->getUsedProductIds();
                $childProduct = $this->prepProduct($variation, 'simple', $product->getId(), $key);
                $childId = $this->saveProduct($childProduct);
                $this->_log->info('Product: #'. $childId->getId() . ' was updated');
                if ($childId !== false) {
                    $childIds[] = $childId->getId();
                    $this->_receivedIds[] = $variation->getId();
                }

                $this->addLocations($variation, $childProduct->getId());
                Mage::getResourceSingleton('catalog/product_type_configurable')->saveProducts($product, $childIds);
            }
        }

        return true;
    }

    /**
     * Remove inventory locations
     *
     * @param $locations
     * @param $id
     */
    public function cleanLocations($locations, $id)
    {
        $collection = Mage::getModel('squareup_omni/location')
            ->getCollection()
            ->addFieldToFilter('status', array('neq' => 1));

        foreach ($collection as $item) {
            $locations[] = $item->getSquareId();
        }

        $locations = $this->_inventory->getCollection()
            ->addFieldToFilter('product_id', array('eq' => $id))
            ->addFieldToFilter('location_id', array('in' => $locations));

        try {
            foreach ($locations as $location) {
                $location->delete();
            }
        } catch (Exception $exception) {
            $this->_log->error($exception->__toString());
        }
    }

    /**
     * Add inventory locations
     *
     * @param $item
     * @param $id
     * @param bool $flag
     */
    public function addLocations($item, $id, $flag = false)
    {
        $productLocations = array();

        if ($flag) {
            $locations = $this->_inventory->getCollection()
                ->addFieldToFilter('product_id', array('eq' => $id));

            foreach ($locations as $location) {
                $productLocations[] = $location->getLocationId();
            }
        }

        if ($item->getPresentAtAllLocations()) {
            if ($item->getAbsentAtLocationIds()) {
                $locations = array_diff($this->_locations, $item->getAbsentAtLocationIds(), $productLocations);
            } else {
                $locations = array_diff($this->_locations, $productLocations);
            }
        } else {
            $importedLocation = array_intersect($this->_locations, $item->getPresentAtLocationIds() ? $item->getPresentAtLocationIds() : array());
            $locations = array_diff($importedLocation, $productLocations);
        }

        foreach ($locations as $location) {
            $data = array(
                'product_id' => $id,
                'location_id' => $location,
                'status' => '',
                'quantity' => 0,
                'calculated_at' => $this->_date,
                'received_at' => $this->_date
            );
            $this->_inventory->setData($data)->save();
        }

        $this->cleanLocations($item->getAbsentAtLocationIds(), $id);
    }

    public function createProduct($item, $type, $id = null)
    {
        $product = $this->prepProduct($item, $type);
        if (false === $product) {
            return false;
        }

        try {
            $product = $product->save();
            $this->_receivedIds[] = $item->getId();
            $this->_log->info(
                'Product with squareId: #' . $item->getId(). ' was created in magento with #' . $product->getId()
            );
            if ($type == 'simple') {
                $this->addLocations($item, $product->getId());
            }
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        if ($type == 'simple') {
            return true;
        }

        $childIds = array();
        $childData = array();
        $confPrice = 0;
        foreach ($item->getItemData()->getVariations() as $key => $variation) {
            $itemVariationData = $variation->getItemVariationData();
            if ($key == 0) {
                $confPrice = $this->_helper->transformAmount($itemVariationData->getPriceMoney()->getAmount());
            }

            $childProduct = $this->prepProduct($variation, 'simple', $product->getId(), $key);
            $child = $this->saveProduct($childProduct);
            $this->_log->info(
                'Product with squareId: #' . $variation->getId(). ' was created in magento with #' . $child->getId()
            );
            if ($child !== false) {
                $this->addLocations($variation, $child->getId());
                $childIds[] = $child->getId();
                $childData[] = array(
                    'id' => $child->getId(),
                    'value_id' => $child->getSquareVariation(),
                    'label' => $itemVariationData->getName(),
                    'price' => $child->getPrice()
                );

                $this->_receivedIds[] = $variation->getId();
            }
        }

        Mage::getResourceSingleton('catalog/product_type_configurable')->saveProducts($product, $childIds);
        $this->saveConfigurableData($product, $childData, $confPrice);

        return true;
    }

    public function changeProduct($item, $type, $id)
    {
        $this->deleteProduct($id);
        $this->createProduct($item, $type);
        return true;
    }

    public function prepProduct($data, $type, $parentId = false, $vKey = false)
    {
        if ($vKey === false) {
            $vKey = 0;
        }

        $itemData = $data->getItemData();
        if ($type == 'simple') {
            if ($parentId !== false) {
                $itemVariation = $data;
                $itemData = $data->getItemVariationData();
                $itemVariationData = $data->getItemVariationData();
                $sku = $itemVariationData->getSku();
                $description = 'Child product';
            } else {
                $itemVariation = $itemData->getVariations()[$vKey];
                $itemVariationData = $itemData->getVariations()[$vKey]->getItemVariationData();
                $sku = $itemVariationData->getSku();
                $description = $itemData->getDescription();
            }
        } else {
            $itemVariation =  $itemData->getVariations()[0];
            $itemVariationData = $itemData->getVariations()[0]->getItemVariationData();
            $sku = $data->getId();
            $description = $itemData->getDescription();
        }

        if (null === $itemVariationData->getPriceMoney()) {
            $this->_log->error('Product: ' . $itemData->getName() . ' does not have any price');
            $this->_errors[] = $itemData->getName() . ' no price';
            return false;
        }

        if (null === $sku) {
            $this->_log->error('Product: ' . $itemData->getName() . ' does not have any sku');
            $this->_errors[] = $itemData->getName() . ' no sku';
            return false;
        }

        $sku = $this->getUniqueSku($sku);
        $product = Mage::getModel('catalog/product');
        $inData = array(
            'sku' => $sku,
            'price' => $this->_helper->transformAmount($itemVariationData->getPriceMoney()->getAmount()),
            'name' => $itemData->getName(),
            'description' => $description,
            'short_description' => $description,
            'weight' => 1,
            'status' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
            'visibility' => ($parentId === false)?
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH :
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE,
            'attribute_set_id' => 4,
            'type_id' => $type,
            'tax_class_id' => 2,
            'website_ids' => array(1),
            'square_id' => $data->getId(),
            'square_variation_id' => ($type == 'configurable')? '' : $itemVariation->getId(),
            'square_updated_at' => $itemVariation->getUpdatedAt(),
            'stock_data' => array(
                'use_config_manage_stock' => 0,
                'manage_stock' => 1,
                'min_sale_qty' => 1,
                'max_sale_qty' => 2,
                'is_in_stock' => 0,
                'qty' => 0
            )
        );

        $product->setData($inData);
        if ($parentId !== false) {
            $product->setSquareVariation($this->getVariationOptionId($product->getName()));
        }


        if ($type == 'configurable') {
            $product->getTypeInstance()->setUsedProductAttributeIds(array($this->_confAttrId));
            $configurableAttributesData = $product->getTypeInstance()->getConfigurableAttributesAsArray();

            $product->setCanSaveConfigurableAttributes(true);
            $product->setConfigurableAttributesData($configurableAttributesData);
        }

        return $product;
    }

    public function prepUpdateProduct($data, $type, $product, $vKey = false)
    {
        if ($vKey === false) {
            $vKey = 0;
        }

        $itemData = $data->getItemData();
        if ($type == 'simple') {
            if ($data->getType() == 'ITEM_VARIATION') {
                $itemData = $data->getItemVariationData();
                $itemVariationData = $data->getItemVariationData();
                $sku = $itemVariationData->getSku();
                $description = 'Child product';
            } else {
                if (empty($itemData->getVariations()[$vKey]))
                    return false;
                $itemVariationData = $itemData->getVariations()[$vKey]->getItemVariationData();
                $sku = $itemVariationData->getSku();
                $description = $itemData->getDescription();
            }
        } else {
            $itemVariationData = $itemData->getVariations()[0]->getItemVariationData();
            $sku = $data->getId();
            $description = $itemData->getDescription();
        }

        $sku = $this->getUniqueSku($sku, $product->getId());

        $inData = array(
            'sku' => $sku,
            'name' => $itemData->getName(),
            'description' => $description,
            'short_description' => $description,
            'square_updated_at' => $data->getUpdatedAt()
        );
        $priceMoney = $itemVariationData->getPriceMoney();
        if (!empty($priceMoney)) {
            $inData['price'] = $this->_helper->transformAmount($itemVariationData->getPriceMoney()->getAmount());
        }

        $product->addData($inData);
        $parentId = $this->_mapping->isChild($product->getId());
        if ($parentId !== false) {
            $product->setSquareVariation($this->getVariationOptionId($product->getName()));
        }

        return $product;
    }

    public function saveProduct($product)
    {
        try {
            $id = $product->save();
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        return $id;
    }

    public function productExists($squareId)
    {
        return Mage::getModel('squareup_omni/catalog')->productExists($squareId);
    }

    public function setConfAttrId()
    {
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode($this->_entityTypeId, 'square_variation');
        $this->_confAttrId = $attribute->getId();
    }

    public function saveConfigurableData($product, $data, $confPrice)
    {
        $product = Mage::getModel('catalog/product')->load($product->getId());
        $configurableProductsData = $product->getConfigurableProductsData();
        $configurableAttributesData = $product->getTypeInstance()->getConfigurableAttributesAsArray();

        foreach ($data as $simple) {
            $productData = array(
                'label'         => $simple['label'],
                'attribute_id'  => $this->_confAttrId,
                'value_index'   => $simple['value_id'],
                'is_percent'    => 0,
                'pricing_value' => $simple['price'] - $confPrice
            );

            $configurableProductsData[$simple["id"]] = $productData;
            $configurableAttributesData[0]['values'][] = $productData;
        }

        try {
            $product->setConfigurableProductsData($configurableProductsData);
            $product->setConfigurableAttributesData($configurableAttributesData);
            $product->setCanSaveConfigurableAttributes(true);
            $product->save();
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
        }

    }

    public function downloadImage($image, $id)
    {
        $config = array(
            'adapter'   => 'Zend_Http_Client_Adapter_Curl',
            'curloptions' => array(CURLOPT_SSL_VERIFYPEER => false),
        );

        try {
            $client = new Zend_Http_Client($image, $config);
            $client->setMethod(Zend_Http_Client::GET);
            $client->setConfig(array('timeout' => 60));
            $response = $client->request();
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        if ($response->getStatus() != 200) {
            $this->_log->error('There was an error when trying to download image: ' . $image);
            return false;
        }

        $fileArr = explode('/', $image);
        $fileName = array_pop($fileArr);
        $extArr = explode('.', $fileName);
        $ext = array_pop($extArr);
        $fullPath = Mage::getBaseDir('media') . '/square/' . $id . '.' . $ext;
        $file = new SplFileObject($fullPath, 'w+');
        $file->fwrite($response->getBody());
        $file = null;

        return $fullPath;
    }

    public function getVariationOptionId($label)
    {
        $_product = Mage::getModel('catalog/product');
        $attr = $_product->getResource()->getAttribute(Squareup_Omni_Model_Square::SQUARE_VARIATION_ATTR);
        $optionId = false;
        if ($attr->usesSource()) {
            $optionId = $attr->getSource()->getOptionId($label);
        }

        if (null == $optionId) {
            return $this->addVariationOption($label);
        }

        return $optionId;
    }

    public function addVariationOption($label)
    {
        $_product = Mage::getModel('catalog/product');
        $attribute = $_product->getResource()->getAttribute(Squareup_Omni_Model_Square::SQUARE_VARIATION_ATTR);
        $value['option'] = array($label);
        $result = array('value' => $value);
        $attribute->setData('option', $result);
        try {
            $attribute = $attribute->save();
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
        }

        $attribute = Mage::getResourceModel('catalog/eav_attribute')->load($attribute->getId());
        if ($attribute->usesSource()) {
            $optionId = $attribute->getSource()->getOptionId($label);
        }

        return $optionId;
    }

    public function deleteProducts()
    {
        $existingArray = Mage::getModel('squareup_omni/catalog_product')->getExistingSquareIds();
        $toDeleteIds = array_diff($existingArray, $this->_receivedIds);
        $this->_log->info('Products to delete: ' . count($toDeleteIds));
        Mage::getResourceModel('squareup_omni/product')->deleteProducts(array_keys($toDeleteIds));
    }

    public function callSquare()
    {
        $client = $this->_helper->getClientApi();
        $api = new \SquareConnect\Api\CatalogApi($client);
        $cursor = null;

        $s = 1;
        while ($s != 0) {
            $types = 'ITEM';

            try {
                $apiResponse = $api->listCatalog($cursor, $types);
            } catch (\SquareConnect\ApiException $e) {
                $errors = $e->getResponseBody()->errors;
                $this->_log->error($e->__toString());
                $errorDetail = '';
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        $errorDetail = $error->category;
                    }

                    Mage::getSingleton('core/session')->addError(
                        $this->_helper->__(
                            '%s Make sure you retrieved OAuth Token or you selected a location.',
                            $errorDetail
                        )
                    );
                }

                return false;
            }

            $cursor = $apiResponse->getCursor();

            if (null !== $apiResponse->getErrors()) {
                $this->_log->error('There was an error in the response from Square, to catalog items');
                return false;
            }

            if (null === $apiResponse->getObjects()) {
                $this->_log->error('There are no items square to import');
                return false;
            }

            foreach ($apiResponse->getObjects() as $item) {
                $this->_log->info('Procesing Item: #' . $item->getId());
                $itemVariation = $item->getItemData()->getVariations();
                // need item variation for sku and price
                if (null === $itemVariation) {
                    continue;
                }

                $type = (!isset($itemVariation[1]))? 'simple' : 'configurable';
                $productExists = $this->productExists($item->getId());
                if ($productExists !== false) {
                    $this->_log->info('Product exists: #' . $productExists);
                    $squareUpdatedAt = $this->getSquareupUpdatedAt($productExists);
                    if (strtotime($item->getUpdatedAt()) <= strtotime($squareUpdatedAt)) {
                        if ($type != 'configurable') {
                            $this->_receivedIds[] = $item->getId();
                            $this->_log->info('Product: #' . $productExists . ' is up to date. skip');
                            continue;
                        }

                        $noUpdate = true;
                        $tempReceived = array();
                        foreach ($itemVariation as $variation) {
                            $tempReceived[] = $variation->getId();
                            $productExistsV = $this->productExists($variation->getId());
                            $squareUpdatedAtV = $this->getSquareupUpdatedAt($productExistsV);
                            if ($productExistsV === false ||
                                strtotime($variation->getUpdatedAt()) > strtotime($squareUpdatedAtV)) {
                                $noUpdate = false;
                            }
                        }

                        if (true === $noUpdate) {
                            $this->_receivedIds[] = $item->getId();
                            $this->_receivedIds = array_merge($this->_receivedIds, $tempReceived);
                            $this->_log->info('Product: #' . $productExists . ' is up to date. skip');
                            continue;
                        } else {
                            $this->_log->info('There are update for product with Square ID: #' . $item->getId());
                        }
                    } else {
                        $this->_log->info('Product: #' . $productExists . ' needs updating');
                    }
                }

                $action = ($productExists === false)? 'create' : 'update';
                $method = $action . 'Product';
                $this->{$method}($item, $type, $productExists);
            }

            if ($cursor === null) {
                $s = 0;
            }
        }

        return true;
    }

    public function getUniqueSku($sku, $productId = 0)
    {
        $a = 1;
        $newSku = $sku;
        while ($a != 0) {
            $skuExists = Mage::getResourceModel('squareup_omni/product')->skuExists($newSku);
            if ($skuExists === false || $skuExists === $productId) {
                $a = 0;
            } else {
                $oldProductId = $skuExists;
                $squareId = Mage::getResourceModel('catalog/product')
                    ->getAttributeRawValue($oldProductId, 'square_id', 0);
                if ($squareId === false) {
                    $newProductSku = $this->checkNewSku($newSku);
                    $product = Mage::getModel('catalog/product');
                    $product->setId($oldProductId);
                    $product->setSku($newProductSku);
                    try {
                        $product->save();
                    } catch (Exception $e) {
                        $this->_log->error($e->__toString());
                        return false;
                    }

                    $a++;
                } else {
                    $newSku = $this->checkNewSku($newSku);
                    $a++;
                }
            }
        }

        return $newSku;
    }

    public function checkNewSku($sku)
    {
        $i = 1;
        $newSku = $sku;
        while ($i != 0) {
            $skuExists = Mage::getResourceModel('squareup_omni/product')->skuExists($newSku);
            if ($skuExists === false) {
                $i = 0;
            } else {
                $newSku = $sku . $i;
                $i++;
            }
        }

        return $newSku;
    }

    public function deleteProduct($id)
    {
        try {
            Mage::getModel('catalog/product')->setId($id)->delete();
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        return true;
    }

    public function getSquareupUpdatedAt($pId)
    {
        $value = Mage::getResourceModel('catalog/product')->getAttributeRawValue(
            $pId,
            array('square_updated_at'),
            Mage::app()->getStore()
        );

        return $value;
    }

}

/* Filename: Import.php */
/* Location: app/code/community/Squareup/Omni/Model/Catalog/Import.php */