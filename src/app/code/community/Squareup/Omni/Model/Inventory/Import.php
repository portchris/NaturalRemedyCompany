<?php
ini_set("memory_limit", "-1");
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
class Squareup_Omni_Model_Inventory_Import extends Squareup_Omni_Model_Square
{
    protected $_variationIds = array();
    protected $_counts = array();
    public function start()
    {
        $this->_log->info('Start Inventory Import');
        $this->inventoryImport();
        $this->_log->info('End Inventory Import');
        return true;
    }

    public function inventoryImport()
    {
        $this->getVariationsIds();
        $apiResponse = $this->batchCall();
        if (false === $apiResponse) {
            return false;
        }

        $this->processStock();

        return true;
    }

    public function processStock()
    {
        foreach ($this->_counts as $item) {
            if (!array_key_exists($item->getCatalogObjectId(), $this->_variationIds)) {
                continue;
            }

            $sInventory = Mage::getResourceModel('squareup_omni/inventory')
                ->loadByProductIdAndLocationId(
                    $this->_variationIds[$item->getCatalogObjectId()],
                    $item->getLocationId()
                );

            if (null === $sInventory) {
                continue;
            }

            $stripDate = str_replace("T", ' ', $item->getCalculatedAt());
            $stripDate = str_replace("Z", '', $stripDate);

            if ($sInventory->getCalculatedAt() != $stripDate) {
                $sInventory->setStatus($item->getStatus());
                $sInventory->setQuantity($item->getQuantity());
                $sInventory->setCalculatedAt($item->getCalculatedAt());
                $sInventory->setReceivedAt(Mage::getModel('core/date')->gmtDate());
                if (null === $sInventory->getId()) {
                    $sInventory->setProductId($this->_variationIds[$item->getCatalogObjectId()]);
                    $sInventory->setLocationId($item->getLocationId());
                    $data["location_id"] = $item->getLocationId();
                }

                try {
                    $sInventory->save();
                    $this->_log->info(
                        'Inventory saved for location: #' . $sInventory->getLocationId() .
                        'for product: #' . $sInventory->getProductId()
                    );
                } catch (Exception $e) {
                    $this->_log->error($e->__toString());
                }
            }

            if ($sInventory->getLocationId() != $this->_config->getLocationId()) {
                continue;
            }

            $stock = Mage::getModel('cataloginventory/stock_item')
                ->loadByProduct($this->_variationIds[$item->getCatalogObjectId()]);

            if ($stock->getQty() == $item->getQuantity()) {
                continue;
            }

            $stock->setData('manage_stock', 1);
            $stock->setData('is_in_stock', 1);
            $stock->setData('use_config_notify_stock_qty', 0);
            $stock->setQty($item->getQuantity());

            try {
                $stock->save();
                $this->_log->info('Inventory updated for: #' . $stock->getProductId());
            } catch (Exception $e) {
                $this->_log->error($e->__toString());
            }
        }
    }

    public function getVariationsIds()
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
                    ->addAttributeToSelect('square_variation_id')
                    ->addAttributeToFilter('square_variation_id', array('notnull' => true));

        Mage::getSingleton('core/resource_iterator')->walk(
            $collection->getSelect(), array(array($this, 'processVariation'))
        );
    }

    public function batchCall()
    {
        $apiClient = $this->_helper->getClientApi();
        $inventoryApi = new \SquareConnect\Api\InventoryApi($apiClient);
        $chunks = array_chunk(array_keys($this->_variationIds), 999);

        foreach ($chunks as $chunk) {
            $cursor = null;

            do {
                $inventoryObjectBatchArr = array(
                    "catalog_object_ids" => $chunk,
                );

                $inventoryObjectBatch = new \SquareConnect\Model\BatchRetrieveInventoryCountsRequest(
                    $inventoryObjectBatchArr
                );
                $inventoryObjectBatch->setCursor($cursor);

                try {
                    $apiResponse = $inventoryApi->batchRetrieveInventoryCounts($inventoryObjectBatch);
                } catch (\SquareConnect\ApiException $e) {
                    $this->_log->error($e->__toString());
                    return false;
                }

                if (null !== $apiResponse->getErrors()) {
                    $this->_log->error(
                        'There was an error in the response, when calling batchRetrieveInventoryCounts' .
                        __FILE__ . __LINE__
                    );
                    return false;
                }

                if (null !== $apiResponse->getCounts()) {
                    $this->_counts = array_merge($this->_counts, $apiResponse->getCounts());
                }

                $cursor = $apiResponse->getCursor();
            } while ($cursor);
        }

        return true;
    }


    public function processVariation($args)
    {
        $this->_variationIds[$args['row']['square_variation_id']] = $args['row']['entity_id'];
    }
}

/* Filename: Import.php */
/* Location: app/code/community/Squareup/Omni/Model/Inventory/Import.php */