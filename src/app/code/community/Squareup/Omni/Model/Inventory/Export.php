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
class Squareup_Omni_Model_Inventory_Export extends Squareup_Omni_Model_Square
{
    protected $_stock = array();
    protected $_products;
    protected $_location;
    protected $_qty;

    public function start($products = null, $location = null, $qty = null)
    {
        $this->_log->info('Start Export Inventory');
        $this->_products = $products;
        $this->_location = $location;
        $this->_qty = $qty;
        $this->inventoryExport();
        $this->batchCall();
        $this->_log->info('End Export Inventory');
        return true;
    }

    public function inventoryExport()
    {
        $this->getStockItems();
    }

    public function getStockItems()
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(
                array(
                    'sku',
                    'square_id',
                    'square_variation_id'
                ),
                'left'
            )
            ->addAttributeToSelect('stock_status_index.qty')
            ->joinField(
                'qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            );

        if ($this->_products) {
            $collection->addAttributeToFilter('entity_id', array('in' => $this->_products));
        }

        Mage::getSingleton('core/resource_iterator')->walk(
            $collection->getSelect(), array(array($this, 'processStock'))
        );
    }

    public function processStock($args)
    {
        if ($args['row']['type_id'] == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return;
        }

        $inventoryResult = $this->buildInventory($args['row']);

        if ($inventoryResult) {
            $this->_stock[] = $inventoryResult;
        }
    }

    public function batchCall()
    {
        $apiClient = $this->_helper->getClientApi();
        $inventoryApi = new SquareConnect\Api\InventoryApi($apiClient);

        $chunks = array_chunk(array_values($this->_stock), 99, true);

        try {
            foreach ($chunks as $chunk) {
                $inventoryObjectBatch = new \SquareConnect\Model\BatchChangeInventoryRequest();
                $inventoryObjectBatch->setIdempotencyKey(uniqid());
                $inventoryObjectBatch->setChanges(array_values($chunk));

                $apiResponse = $inventoryApi->batchChangeInventory($inventoryObjectBatch);
                $this->_log->info('Inventory Export Batch call');
                if (!$apiResponse->getErrors() && ($items = $apiResponse->getCounts())) {
                    $item = array_shift($items);

                    if (null === $this->_products && ($productId = Mage::registry('square_product'))) {
                        $this->_products[] = $productId;
                        Mage::unregister('square_product');
                    }

                    if ($this->_products) {
                        Mage::getModel('squareup_omni/inventory')->getCollection()
                            ->addFieldToFilter('product_id', array('in' => $this->_products))
                            ->addFieldToFilter('location_id', array('eq' => $item->getLocationId()))
                            ->getFirstItem()
                            ->addData(
                                array(
                                    'product_id' => current($this->_products),
                                    'location_id' => $this->_location ? $this->_location : $item->getLocationId(),
                                    'status' => $item->getStatus(),
                                    'quantity' => $item->getQuantity(),
                                    'calculated_at' => $item->getCalculatedAt(),
                                    'received_at' => $item->getCalculatedAt(),
                                )
                            )
                            ->save();
                    } else {
                        $this->batchUpdateInventory();
                    }
                }

                if (null !== $apiResponse->getErrors()) {
                    $this->_log->error(
                        'There was an error in the response, when calling batchChangeInventory' . __FILE__ . __LINE__
                    );
                    $this->_log->error(serialize($apiResponse->getErrors()));
                }

                if (null === $apiResponse->getCounts()) {
                    $this->_log->info('Square returned null counts');
                    Mage::unregister('square_product');
                }
            }
        } catch (\SquareConnect\ApiException $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        return true;
    }

    public function buildInventory($product)
    {
        if (empty($product['square_variation_id'])) {
            return false;
        }

        $physicalInventory = array(
            "reference_id" => $product['entity_id'],
            "catalog_object_id" => $product['square_variation_id'],
            "status" => 'IN_STOCK',
            "location_id" => $this->_location? $this->_location : $this->_config->getLocationId(),
            "quantity" => (string)round($this->_qty? $this->_qty : $product['qty'], 0),
            "occurred_at" => date('Y-m-d\TH:i:s\Z')
        );

        $physicalCount = new \SquareConnect\Model\InventoryPhysicalCount($physicalInventory);
        $inventory = new \SquareConnect\Model\InventoryChange();
        $inventory->setType('PHYSICAL_COUNT');
        $inventory->setPhysicalCount($physicalCount);

        return $inventory;
    }

    public function setStockArr($stock)
    {
        $this->_stock = $stock;
    }

    protected function batchUpdateInventory()
    {
        $data = array();
        foreach ($this->_stock as $stockItem) {
            $count = $stockItem->getPhysicalCount();

            $data[$count->getReferenceId()] = array(
                'product_id' => $count->getReferenceId(),
                'location_id' => $count->getLocationId(),
                'status' => $count->getStatus(),
                'quantity' => $count->getQuantity(),
                'calculated_at' => $count->getOccurredAt(),
                'received_at' => $count->getOccurredAt(),
            );
        }

        $products = Mage::getModel('squareup_omni/inventory')->getCollection()
            ->addFieldToFilter('product_id', array('in' => array_keys($data)))
            ->addFieldToFilter('location_id', array('eq' => $this->_config->getLocationId()));

        foreach ($products as $product) {
            if (isset($data[$product->getProductId()])) {
                $product->addData($data[$product->getProductId()])->save();
                unset($data[$product->getProductId()]);
            }
        }

        if (!empty($data)) {
            foreach ($data as $inventoryItem) {
                $inventory = Mage::getModel('squareup_omni/inventory');
                $inventory->addData($inventoryItem)->save();
            }
        }
    }

}

/* Filename: Export.php */
/* Location: app/code/community/Squareup/Omni/Model/Inventory/Export.php */