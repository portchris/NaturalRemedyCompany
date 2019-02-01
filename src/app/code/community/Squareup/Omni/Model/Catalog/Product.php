<?php
/**
 * SquareUp
 *
 * Product Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Catalog_Product extends Squareup_Omni_Model_Square
{
    protected $_catalogApi;
    protected $_mapping;

    public function _construct()
    {
        $this->init();
        $apiClient = $this->_helper->getClientApi();
        $this->_catalogApi = new \SquareConnect\Api\CatalogApi($apiClient);
        $this->_mapping = Mage::helper('squareup_omni/mapping');
    }

    public function createProduct($product)
    {
        $isChildOfConfig = $this->isChild($product);
        if ($isChildOfConfig !== false) {
            foreach ($isChildOfConfig as $configId) {
                $configProduct = Mage::getModel('catalog/product')->load($configId);
                $this->updateProduct($configProduct);
            }

            return $this;
        }

        $idemPotency = uniqid();
        $catalogObjectArr = array(
            "idempotency_key" => $idemPotency,
            "object" => $this->_mapping->setCatalogObject($product, null, $isChildOfConfig)
        );

        $this->_log->info(json_encode($catalogObjectArr));
        $catalogObject = new SquareConnect\Model\UpsertCatalogObjectRequest($catalogObjectArr);

        try {
            $apiResponse = $this->_catalogApi->UpsertCatalogObject($catalogObject);
        } catch (\SquareConnect\ApiException $e) {
            $this->_log->error($e->__toString());
            return $this;
        }

        if (null !== $apiResponse->getErrors()) {
            $this->_log->error(
                'There was an error in the response, when calling UpsertCatalogObject' . __FILE__ . __LINE__
            );
            return $this;
        }

        $this->saveIdsInMagento($apiResponse, $product);
        $this->doInventory($product);

        return $this;
    }

    public function updateProduct($product)
    {
        $isChildOfConfig = $this->isChild($product);
        if ($isChildOfConfig !== false) {
            foreach ($isChildOfConfig as $configId) {
                $configProduct = Mage::getModel('catalog/product')->load($configId);
                $this->updateProduct($configProduct);
            }

            return $this;
        }

        $forDeletion = array();
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $forDeletion = $this->prepDelete($product, $forDeletion);
        }

        try {
            $receivedObj = $this->_catalogApi->retrieveCatalogObject($product->getSquareId(), true);
        } catch (\SquareConnect\ApiException $e) {
            $this->_log->error($e->__toString());
            return $this;
        }

        $idemPotency = uniqid();
        $catalogObjectArr = array(
            "idempotency_key" => $idemPotency,
            "object" => $this->_mapping->setCatalogObject($product, $receivedObj)
        );

        $this->_log->info(json_encode($catalogObjectArr));
        $catalogObject = new SquareConnect\Model\UpsertCatalogObjectRequest($catalogObjectArr);

        try {
            $apiResponse = $this->_catalogApi->UpsertCatalogObject($catalogObject);
        } catch (\SquareConnect\ApiException $e) {
            $this->_log->error($e->__toString());
            return $this;
        }

        if (null !== $apiResponse->getErrors()) {
            $this->_log->error(
                'There was an error in the response, when calling UpsertCatalogObject' . __FILE__ . __LINE__
            );
            return $this;
        }

        if (null !== $apiResponse->getIdMappings()) {
            $this->_mapping->saveSquareIdsInMagento($apiResponse->getIdMappings());
        }

        $this->doInventory($product);

        if (!empty($forDeletion)) {
            Mage::getModel('squareup_omni/catalog_export')->deleteDuplicateFromSquare($forDeletion);
        }


        return $this;
    }

    public function saveIdsInMagento($apiResponse, $product)
    {
        $idMappings = $apiResponse->getIdMappings();

        $ids = array();
        $varIds = array();
        foreach ($idMappings as $map) {
            if (stripos($map->getClientObjectId(), "::") !== false) {
                $idWithoutSharp = str_replace("#", "", $map->getClientObjectId());
                $id = str_replace("::", "", $idWithoutSharp);
                $varIds[$id][] = $map->getObjectId();
            } else {
                $ids[str_replace("#", "", $map->getClientObjectId())] = $map->getObjectId();
            }
        }

        $product->setSquareId($ids[$product->getId()]);
        if (isset($varIds[$product->getId()])) {
            $product->setSquareVariationId(implode(":", $varIds[$product->getId()]));
        }

        try {
            $product->getResource()->saveAttribute($product, 'square_id');
            if (isset($varIds[$product->getId()])) {
                $product->getResource()->saveAttribute($product, 'square_variation_id');
            }
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        return true;
    }

    public function isChild($product)
    {
        $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
        return (!empty($parentIds))? $parentIds : false;
    }

    public function doInventory($product)
    {
        if (false === $this->_config->isInventoryEnabled()) {
            return true;
        }

        $stockArrays = array();
        $inventory = Mage::getModel('squareup_omni/inventory_export');
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $conf = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
            $collection = $conf->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();
            foreach ($collection as $aProduct) {
                $qty = (null == $aProduct->getStockItem()) ?
                        $aProduct->getStockData()['qty'] :
                        $aProduct->getStockItem()->getQty();
                $stockValues = array(
                    "entity_id" => $aProduct->getId(),
                    "square_variation_id" => $aProduct->getSquareVariationId(),
                    "qty" => $qty
                );
                $stockArr = $inventory->buildInventory($stockValues);
                $stockArrays[] = $stockArr;
            }
        } else {
            $qty = (null == $product->getStockItem()) ?
                    $product->getStockData()['qty'] :
                    $product->getStockItem()->getQty();
            $stockValues = array(
                "entity_id" => $product->getId(),
                "square_variation_id" => $product->getSquareVariationId(),
                "qty" => $qty
            );
            $stockArr = $inventory->buildInventory($stockValues);
            $stockArrays[] = $stockArr;
            $inventoryCollection = Mage::getModel('squareup_omni/inventory')
                ->getCollection()
                ->addFieldToFilter('product_id', array('eq' => $product->getId()))
                ->addFieldToFilter('location_id', array('eq' => $this->_config->getLocationId()));

            if ($inventoryCollection->getSize() > 0) {
                foreach ($inventoryCollection as $item) {
                    $item->setQuantity($qty);
                    $item->save();
                }
            } else {
                $inventoryItem = Mage::getModel('squareup_omni/inventory');
                $inventoryItem->setData(
                    array(
                        'product_id' => $product->getId(),
                        'location_id' => $this->_config->getLocationId(),
                        'status' => $qty > 0 ? 'IN_STOCK' : 'OUT_OF_STOCK',
                        'quantity' => $qty,
                        'calculated_at' => now(),
                        'received_at' => now(),
                    )
                )->save();
            }
        }

        if (empty($stockArrays)) {
            $this->_log->info('No stock to update');
            return true;
        }

        try {
            $inventory->setStockArr($stockArrays);
            $inventory->batchCall();
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        return true;
    }

    /**
     * @param $product
     * @param $forDeletion
     * @return array
     * @throws Varien_Exception
     */
    protected function prepDelete($product, $forDeletion)
    {
        $conf = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
        $collection = $conf->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();
        foreach ($collection as $aProduct) {
            if ($aProduct->getSquareId() != $aProduct->getSquareVariationId()) {
                $forDeletion[] = $aProduct->getSquareId();
            }
        }

        return $forDeletion;
    }

    public function getExistingSquareIds()
    {
        $idsArray = array();
        $collection = Mage::getResourceModel('catalog/product_collection')
                        ->addAttributeToSelect(array('square_id'))
                        ->addAttributeToFilter('square_id', array('notnull' => true));

        Mage::getSingleton('core/resource_iterator')->walk(
            $collection->getSelect(),
            array(array($this, 'processSquareIds')),
            array('idsArray' => &$idsArray)
        );

        return $idsArray;
    }

    public function processSquareIds($args)
    {
        $args['idsArray'][$args['row']['entity_id']] = $args['row']['square_id'];
        return $args;
    }

    public function resetChildrenIds($product)
    {
        $ids = array();
        $conf = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
        $collection = $conf->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();
        foreach ($collection as $aProduct) {
            $ids[] = $aProduct->getId();
        }

        return $ids;
    }
}

/* Filename: Product.php */
/* Location: app/code/community/Squareup/Omni/Model/Catalog/Product.php */