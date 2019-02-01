<?php
/**
 * SquareUp
 *
 * Export Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Catalog_Export extends Squareup_Omni_Model_Square
{
    protected $_prepProducts = array();
    protected $_newProducts = array();
    protected $_existingProducts = array();
    protected $_addToUpdate = array();
    protected $_mIdWithVersion = array();
    protected $_mIdWithVersionVar = array();
    protected $_squareVersions = array();
    protected $_forDeletion = array();
    protected $_mapping;

    public function start()
    {
        $this->_log->info('Start catalog export');
        $this->_mapping = Mage::helper('squareup_omni/mapping');
        $ranAt = Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s');
        $this->productsExport();
        $this->_helper->saveRanAt($ranAt);
        $this->_log->info('End catalog export');
        return true;
    }

    public function productsExport()
    {
        $this->exportNew();
        $this->exportExisting();
        if (!empty($this->_existingProducts)) {
            $this->getVersions();
            $this->addToUpdatePrep();
            $this->addVersions();
        }

        $toSend = array();
        $toSendCount = 0;
        foreach ($this->_newProducts as $catalogObject) {
            $noVariations = count($catalogObject['item_data']['variations']);
            $noObjects = $noVariations + 1;
            if ($toSendCount < 500) {
                $toSendCount += $noObjects;
                $toSend[] = $catalogObject;
            } else {
                $this->sendData($toSend);
                $toSendCount = 0;
                $toSend = array();
            }
        }

        if (!empty($toSend)) {
            $this->sendData($toSend);
            $toSendCount = 0;
            $toSend = array();
        }

        $toSendExisting = array();
        $toSendExistingCount = 0;
        foreach ($this->_existingProducts as $eCatalogObject) {
            $noEVariations = count($eCatalogObject['item_data']['variations']);
            $noEObjects = $noEVariations + 1;
            if ($toSendExistingCount < 500) {
                $toSendExistingCount += $noEObjects;
                $toSendExisting[] = $eCatalogObject;
            } else {
                $this->sendData($toSendExisting);
                $toSendExistingCount = 0;
                $toSendExisting = array();
            }
        }

        if (!empty($toSendExisting)) {
            $this->sendData($toSendExisting);
            $toSendExistingCount = 0;
            $toSendExisting = array();
        }

        $this->deleteDuplicateFromSquare();

        $process = Mage::getModel('index/indexer')->getProcessByCode('catalog_product_flat');
        $process->reindexAll();
        return true;
    }

    public function exportNew()
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(
                array(
                    'name',
                    'square_id',
                    'category_ids',
                    'price',
                    'sku',
                    'short_description',
                    'image',
                    'square_variation_id'
                ),
                'left'
            )
            ->addAttributeToFilter('square_id', array('null' => true));

        Mage::getSingleton('core/resource_iterator')->walk(
            $collection->getSelect(), array(array($this, 'processProduct')), array('type' => 'new')
        );
    }

    public function exportExisting()
    {
        $fromDate = $this->_config->cronRanAt();

        if (null === $fromDate) {
            $fromDate = Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s', 0);
        }

        $toDate = Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s');

        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(
                array(
                    'name',
                    'square_id',
                    'category_ids',
                    'price',
                    'sku',
                    'short_description',
                    'image',
                    'square_variation_id'
                ),
                'left'
            )
            ->addAttributeToFilter('updated_at', array('from' => $fromDate, 'to' => $toDate))
            ->addAttributeToFilter('square_id', array('notnull' => true));

        Mage::getSingleton('core/resource_iterator')->walk(
            $collection->getSelect(), array(array($this, 'processProduct')), array('type' => 'existing')
        );
    }

    public function getVersions()
    {
        $versions = array();
        $apiClient = $this->_helper->getClientApi();
        $catalogApi = new \SquareConnect\Api\CatalogApi($apiClient);
        $objectList = array(
            "object_ids" => array_keys($this->_mIdWithVersion)
        );
        $objectIds = new SquareConnect\Model\BatchRetrieveCatalogObjectsRequest($objectList);

        try {
            // Retrieve the objects
            $apiResponse = $catalogApi->BatchRetrieveCatalogObjects($objectIds);
        } catch (\SquareConnect\ApiException $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        if (null !== $apiResponse->getErrors()) {
            $this->_log->error('There was a error while requesting batch retrieve objects' . __FILE__ . __LINE__);
            return false;
        }

        if (null === $apiResponse->getObjects()) {
            return $versions;
        }

        foreach ($apiResponse->getObjects() as $object) {
            if ($object->getType() == 'ITEM_VARIATION') {
                $this->_squareVersions[$object->getId()] = $object->getVersion();
            } else {
                $this->_squareVersions[$object->getId()] = $object->getVersion();
                if (null === $object->getItemData()->getVariations()) {
                    continue;
                }

                foreach ($object->getItemData()->getVariations() as $variation) {
                    $this->_squareVersions[$variation->getId()] = $variation->getVersion();
                }
            }
        }

        return $versions;
    }

    public function addVersions()
    {
        foreach ($this->_squareVersions as $squareId => $squareV) {
            if (array_key_exists($squareId, $this->_mIdWithVersion)) {
                $mId = $this->_mIdWithVersion[$squareId];
                $this->_existingProducts[$mId]['version'] = $squareV;
                foreach ($this->_existingProducts[$mId]['item_data']['variations'] as &$variation) {
                    if (array_key_exists($variation['id'], $this->_squareVersions)) {
                        $variation['version'] = $this->_squareVersions[$variation['id']];
                    }
                }
            }
        }
    }

    public function processProduct($args)
    {
        if ($args['row']['type_id'] == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE
            || $args['row']['type_id'] == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
            return;
        }

        $parentIds = $this->_mapping->isChild($args['row']['entity_id']);
        if ($args['type'] == 'new' && $parentIds !== false) {
            return;
        }

        if ($args['type'] == 'existing' && $parentIds !== false) {
            if ($args['row']['square_id'] != $args['row']['square_variation_id']) {
                $this->_forDeletion[] = $args['row']['square_id'];
            }

            $this->_addToUpdate[] = $parentIds;
            return;
        }

        $property = '_' . $args['type'] . 'Products';
        $product = Mage::getModel('catalog/product');
        $product->setData($args['row']);
        $this->{$property}[$args['row']['entity_id']] = $this->_mapping->setCatalogObject($product);
        if ($args['type'] === 'existing') {
            $this->_mIdWithVersion[$args['row']['square_id']] = $args['row']['entity_id'];
            $this->_mIdWithVersionVar[$args['row']['square_variation_id']] = $args['row']['entity_id'];
        }

        $product = null;
    }


    public function doBatchCall($products)
    {
        $apiClient = $this->_helper->getClientApi();
        $catalogApi = new \SquareConnect\Api\CatalogApi($apiClient);
        $catalogObjectBatchArr = array(
            "idempotency_key" => uniqid(),
            "batches" => array(
                array(
                    "objects" => array_values($products)
                )
            )
        );

        $catalogObjectBatch = new SquareConnect\Model\BatchUpsertCatalogObjectsRequest($catalogObjectBatchArr);

        try {
            $apiResponse = $catalogApi->BatchUpsertCatalogObjects($catalogObjectBatch);
        } catch (\SquareConnect\ApiException $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        if (null !== $apiResponse->getErrors()) {
            $this->_log->error(
                'There was an error in the response, when calling UpsertCatalogObject' . __FILE__ . __LINE__
            );
            return false;
        }

        return $apiResponse;
    }

    public function saveBatchIdsInMagento($idMappings)
    {
        $ids = array();
        $varIds = array();
        foreach ($idMappings as $map) {
            if (stripos($map->getClientObjectId(), "::") !== false) {
                $idWithoutSharp = str_replace("#", "", $map->getClientObjectId());
                $mId = str_replace("::", "", $idWithoutSharp);
                $varIds[$map->getObjectId()] = $mId;
            } else {
                $mId = str_replace("#", "", $map->getClientObjectId());
                $ids[$map->getObjectId()] = $mId;
            }
        }

        foreach ($ids as $sKey => $sId) {
            $product = Mage::getModel('catalog/product');
            $product->setId($sId);
            $product->setStoreId(0);
            $product->setSquareId($sKey);

            try {
                $product->getResource()->saveAttribute($product, 'square_id');
            } catch (Exception $e) {
                $this->_log->error($e->__toString());
            }
        }

        foreach ($varIds as $vKey => $vId) {
            $isChild = $this->_mapping->isChild($vId);
            $product = Mage::getModel('catalog/product');
            $product->setId($vId);
            $product->setStoreId(0);
            if ($isChild !== false) {
                $product->setSquareId($vKey);
            }

            $product->setSquareVariationId($vKey);

            try {
                if ($isChild !== false) {
                    $product->getResource()->saveAttribute($product, 'square_id');
                }

                $product->getResource()->saveAttribute($product, 'square_variation_id');
            } catch (Exception $e) {
                $this->_log->error($e->__toString());
            }
        }

        return true;
    }

    public function addToUpdatePrep()
    {
        foreach ($this->_addToUpdate as $item) {
            foreach ($item as $_item) {
                if (array_key_exists($_item, $this->_existingProducts)) {
                    continue;
                }

                $product = Mage::getModel('catalog/product')->load($_item);
                $this->_existingProducts[$product->getId()] = $this->_mapping->setCatalogObject($product);
                $product = null;
            }
        }
    }

    public function deleteDuplicateFromSquare($ids = null)
    {
        if (empty($this->_forDeletion) && null == $ids) {
            return true;
        }

        $apiClient = $this->_helper->getClientApi();
        $catalogApi = new \SquareConnect\Api\CatalogApi($apiClient);
        $deleteBatch = new SquareConnect\Model\BatchDeleteCatalogObjectsRequest();
        if (null == $ids) {
            $deleteBatch->setObjectIds($this->_forDeletion);
        } else {
            $deleteBatch->setObjectIds($ids);
        }

        try {
            $apiResponse = $catalogApi->batchDeleteCatalogObjects($deleteBatch);
        } catch (\SquareConnect\ApiException $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        if (null !== $apiResponse->getErrors()) {
            $this->_log->error(
                'There was an error in the response, when calling UpsertCatalogObject' . __FILE__ . __LINE__
            );
            return false;
        }

        return true;
    }

    public function sendData($toSend)
    {
        $newApiResponse = $this->doBatchCall($toSend);
        if (false === $newApiResponse) {
            return false;
        }

        $idMappings = $newApiResponse->getIdMappings();
        if ($idMappings) {
            $this->saveBatchIdsInMagento($idMappings);
        }

        return true;
    }
}

/* Filename: Export.php */
/* Location: app/code/community/Squareup/Omni/Model/Catalog/Export.php */