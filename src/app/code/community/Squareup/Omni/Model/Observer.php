<?php
/**
 * SquareUp
 *
 * Observer Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Observer
{
    /**
     * @var Squareup_Omni_Helper_Config
     */
    protected $_configHelper;
    protected $_log;
    protected $_helper;
    protected $_mapping;

    /**
     * Squareup_Omni_Model_Observer constructor.
     */
    public function __construct()
    {
        $this->_configHelper = Mage::helper('squareup_omni/config');
        $this->_log = Mage::helper('squareup_omni/log');
        $this->_helper = Mage::helper('squareup_omni');
        $this->_mapping = Mage::helper('squareup_omni/mapping');
    }

    /**
     * Loading the Square library in order to be available to entire Magento
     * @return $this
     */
    public function addAutoloader()
    {
        // Add our vendor folder to our include path
        set_include_path(get_include_path() . PATH_SEPARATOR . Mage::getBaseDir('lib') . DS . 'Square' . DS . 'vendor');

        // Include the autoloader for composer
        require_once Mage::getBaseDir('lib') . DS . 'Square' . DS . 'vendor' . DS . 'autoload.php';
        return $this;
    }

    /**
     * Send new customer to SquareUp or update customer
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws Varien_Exception
     */
    public function customerSaveAfter(Varien_Event_Observer $observer)
    {
        /* If Sync functionality is disabled, return */
        if (!$this->_configHelper->getAllowCustomerSync()) {
            return $this;
        }

        $customer = $observer->getEvent()->getCustomer();
        $exportModel = Mage::getModel('squareup_omni/customer_export_export');
        /* If customer is created from import cron send to SquareUp only ReferenceId */
        if ($customer->getSquareupJustImported() != 0) {
            $customer->setData('squareup_just_imported', $customer->getSquareupJustImported() - 1);
            $customer->getResource()->saveAttribute($customer, 'squareup_just_imported');
            return $this;
        }

        /* If customer exists already in Magento and SquareUp then update customer data */
        $origData = $customer->getOrigData();
        $squareCustomerId = $customer->getSquareupCustomerId();
        if (!empty($origData) && !empty($squareCustomerId)) {
            $exportModel->updateSquareCustomer($customer->getId(), $customer->getSquareupCustomerId());
            return $this;
        }

        /* Export new customer to SquareUp */
        $exportModel->exportNewCustomer($customer);

        return $this;
    }

    /**
     * Delete customer from SquareUp app on customer delete action
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws Varien_Exception
     */
    public function customerDeleteAfter(Varien_Event_Observer $observer)
    {
        /* If Sync functionality is disabled, return */
        if (!$this->_configHelper->getAllowCustomerSync()) {
            return $this;
        }

        $customer = $observer->getEvent()->getCustomer();
        if ($customer->getData('deleted_from_square')) {
            return $this;
        }

        $deleteModel = Mage::getModel('squareup_omni/customer_delete');
        $customerSquareId = $customer->getSquareupCustomerId();
        if (!empty($customerSquareId)) {
            $deleteModel->deleteSquareupCustomer($customer->getSquareupCustomerId());
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Varien_Exception
     */
    public function productSave(Varien_Event_Observer $observer)
    {
        if ($this->_configHelper->getSor() == Squareup_Omni_Model_System_Config_Source_Options_Records::SQUARE) {
            return $this;
        }

        if (false === $this->_configHelper->isCatalogEnabled()) {
            return $this;
        }

        $product = $observer->getEvent()->getProduct();
        $productSquareId = $product->getSquareId();
        $notInSquare = empty($productSquareId)? true :  false;

        Mage::register('square_product', $product->getId());

        if ($notInSquare === true) {
            Mage::getModel('squareup_omni/catalog_product')->createProduct($product);
        } else {
            Mage::getModel('squareup_omni/catalog_product')->updateProduct($product);
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws Varien_Exception
     */
    public function productDelete(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->clearInventory($product->getId());

        if ($this->_configHelper->getSor() == Squareup_Omni_Model_System_Config_Source_Options_Records::SQUARE) {
            return $this;
        }

        if (false === $this->_configHelper->isCatalogEnabled()) {
            return $this;
        }

        if (null === $product->getSquareId()) {
            return $this;
        }

        $apiClient = $this->_helper->getClientApi();
        $catalogApi = new \SquareConnect\Api\CatalogApi($apiClient);

        try {
            $apiResponse = $catalogApi->DeleteCatalogObject($product->getSquareId());
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                $childrenIdsJson = Mage::registry('delete_product_' . $product->getId());
                $childrenIds = json_decode($childrenIdsJson);
                if (!empty($childrenIds)) {
                    Mage::getResourceModel('squareup_omni/product')->resetProducts($childrenIds);
                    Mage::unregister('delete_product' . $product->getId());
                }
            }
        } catch (\SquareConnect\ApiException $e) {
            $this->_log->error($e->__toString());
            return $this;
        }

        if (null !== $apiResponse->getErrors()) {
            $this->_log->error(
                'There was an error in the response, when calling UpsertCatalogObject' . __FILE__ . __LINE__
            );
        }

        return $this;
    }

    /**
     * @param $productId
     */
    public function clearInventory($productId)
    {
        $inventory = Mage::getModel('squareup_omni/inventory')
            ->getCollection()
            ->addFieldToFilter('product_id', array('eq' => $productId));

        foreach ($inventory as $item) {
            $item->delete();
        }
    }

    /**
     * @return $this
     */
    public function beforeConfigSave()
    {
        try {
            Mage::register('before_app_mode', $this->_configHelper->getApplicationMode());
            Mage::register('square_application_id', $this->_configHelper->getApplicationId());
            Mage::register('square_application_secret', $this->_configHelper->getApplicationSecret());
            Mage::register('before_location_id', $this->_configHelper->getLocationId());
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
        }

        return $this;
    }

    /**
     * @return $this
     * @throws Varien_Exception
     */
    public function configSave()
    {
        $before = Mage::registry('before_app_mode');
        $after = $this->_configHelper->getApplicationMode();
        $beforeApplicationId = Mage::registry('square_application_id');
        $afterApplicationId = $this->_configHelper->getApplicationId();
        $beforeApplicationSecret = Mage::registry('square_application_secret');
        $afterApplicationSecret = $this->_configHelper->getApplicationSecret();
        if (($before != $after) ||
            ($beforeApplicationId != $afterApplicationId) ||
            ($beforeApplicationSecret != $afterApplicationSecret)) {
            if (($before == 'prod') && ($beforeApplicationId == $afterApplicationId)) {
                Mage::getConfig()->saveConfig(
                    'squareup_omni/general/location_id_old',
                    Mage::registry('before_location_id')
                );
            }

            Mage::getResourceModel('squareup_omni/inventory')->emptyInventory();
            Mage::getResourceModel('squareup_omni/location')->emptyLocations();

            /* Delete all square flag from customers */
            $this->_helper->resetSquareCustomerFlag();
            /* Delete all square transactions from magento */
            Mage::getResourceModel('squareup_omni/transaction')->emptyTransactions();
            /* Delete all square transactions from magento */
            Mage::getResourceModel('squareup_omni/refunds')->emptyRefunds();

            Mage::getResourceModel('squareup_omni/product')->resetProducts();

            $this->_configHelper->saveRanAt();
            $this->_configHelper->saveImagesRanAt();
            $this->_configHelper->saveTransactionsBeginTime();
            $this->_configHelper->saveRefundsBeginTime();

            if (($beforeApplicationId != $afterApplicationId) ||
                ($beforeApplicationSecret != $afterApplicationSecret)) {
                $this->_configHelper->saveOauthToken();
                $this->_configHelper->saveOauthExpire();
            } else {
                $oAuthToken = $this->_configHelper->getOAuthToken();
                if (!empty($oAuthToken)) {
                    Mage::getModel('squareup_omni/location_import')->updateLocations();
                    Mage::getConfig()->saveConfig(
                        'squareup_omni/general/location_id',
                        $this->_configHelper->getOldLocationId()
                    );
                    Mage::getConfig()->saveConfig('squareup_omni/general/location_id_old', '');
                }
            }

            Mage::app()->getConfig()->reinit();
            Mage::app()->getCacheInstance()->flush();
        }

        $oAuthToken2 = $this->_configHelper->getOAuthToken();
        if (!empty($oAuthToken2)) {
            $this->syncLocationInventory();
        }

        return $this;
    }

    /**
     * Join location field to product collection
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     * @throws Varien_Exception
     */
    public function joinLocationToProductCollection(Varien_Event_Observer $observer)
    {
        $collection = $observer->getCollection();
        $collection->getSelect()->joinLeft(
            array(
                'square_inventory' =>
                    Mage::getSingleton('core/resource')->getTableName('squareup_omni/square_inventory')
            ),
            'square_inventory.product_id = e.entity_id',
            array('location_id')
        )->group('e.entity_id');

        return $this;
    }

    /**
     * Add location column to product grid
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     * @throws Varien_Exception
     */
    public function addLocationToProductGrid(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid) {
            $block->addColumnAfter(
                'location_id',
                array(
                    'header' => Mage::helper('catalog')->__('Location'),
                    'index' => 'location_id',
                    'sortable' => false,
                    'type' => 'options',
                    'options' => Mage::getModel(
                        'squareup_omni/system_config_source_options_location'
                    )->getOptionArray(),
                    'renderer' => 'Squareup_Omni_Block_Adminhtml_Product_Widget_Grid_Column_Renderer_Location',
                    'filter_condition_callback' => array($this, 'filterLocationCallback')
                ),
                'sku'
            );
        }

        return $this;
    }

    /**
     * Filter location column
     *
     * @param $collection
     * @param $column
     *
     * @return $this
     * @throws Varien_Exception
     */
    public function filterLocationCallback($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        if (!$value) {
            return $this;
        }

        $collection->getSelect()->joinInner(
            array(
                'square_filter' => Mage::getSingleton('core/resource')->getTableName('squareup_omni/square_inventory')
            ),
            'square_filter.product_id = e.entity_id AND square_filter.location_id = "' . $value . '"',
            array('location_id')
        );

        return $this;
    }

    /**
     * Synchronize items inventory on order creation
     *
     * @param $observer
     *
     * @return $this
     * @throws Varien_Exception
     */
    public function synchronizeItems($observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $paymentMethodCode = $invoice->getOrder()->getPayment()->getMethodInstance()->getCode();
        $ids = array();

        if ($paymentMethodCode == 'squareup_payment') {
            return $this;
        }

        if (false === $this->_configHelper->isInventoryEnabled()
            || false === $this->_configHelper->isCatalogEnabled()) {
            return $this;
        }

        foreach ($invoice->getAllItems() as $item) {
            $ids[] = $item->getProductId();
        }

        Mage::getModel('squareup_omni/inventory_export')->start($ids);

        return $this;
    }

    /**
     * Synchronize inventory on location change
     */
    protected function syncLocationInventory()
    {
        if (Mage::registry('before_location_id') != ($locationId = $this->_configHelper->getLocationId())) {
            $inventory = Mage::getModel('squareup_omni/inventory')
                ->getCollection()
                ->addFieldToFilter('location_id', array('eq' => $locationId));

            $inventoryArr = array();

            foreach ($inventory as $item) {
                $inventoryArr[$item->getProductId()] = $item->getQuantity();
            }

            $stockItems = Mage::getModel('cataloginventory/stock_item')
                ->getCollection();

            foreach ($stockItems as $stockItem) {
                if (array_key_exists($stockItem->getProductId(), $inventoryArr)) {
                    $quantity = $inventoryArr[$stockItem->getProductId()];
                } else {
                    $quantity = 0;
                }

                $stockItem->getManageStock();
                $stockItem->setQty($quantity);
                $stockItem->setIsInStock((int)($quantity > 0));
                $stockItem->save();
            }
        }
    }

    /**
     * @param $observer
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Varien_Exception
     */
    public function productResetChildren($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return $this;
        }

        $ids = Mage::getModel('squareup_omni/catalog_product')->resetChildrenIds($product);
        Mage::register('delete_product_' . $product->getId(), json_encode($ids));

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @throws Varien_Exception
     */
    public function creditmemoRefund(Varien_Event_Observer $observer)
    {
        $creditmemo = $observer->getCreditmemo();
        $creditmemoItems = $creditmemo->getAllItems();
        $exportModel = Mage::getModel('squareup_omni/inventory_export');
        $locationId = $this->_configHelper->getLocationId();

        $items = array();

        foreach ($creditmemoItems as $creditmemoItem) {
            if ($creditmemoItem->getBackToStock()) {
                $items[$creditmemoItem->getProductId()] = $creditmemoItem->getQty();
            }
        }

        $inventory = Mage::getModel('cataloginventory/stock_item')->getCollection()
            ->addFieldToFilter('product_id', array('in' => array_keys($items)));

        foreach ($inventory as $item) {
            $exportModel->start(
                array($item->getProductId()), $locationId, $item->getQty() + $items[$item->getProductId()]
            );
        }
    }

    /**
     * @param $observer
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Varien_Exception
     */
    public function massProductUpdates($observer)
    {
        $productIds = Mage::helper('adminhtml/catalog_product_edit_action_attribute')->getProductIds();
        foreach ($productIds as $productId) {
            $product = Mage::getModel('catalog/product')->load($productId);
            $event = new Varien_Object();
            $event->setProduct($product);
            $observer = new Varien_Event_Observer();
            $observer->setEvent($event);
            $this->productSave($observer);
        }

        return $this;
    }

    public function massInventoryUpdates($observer)
    {
        $productIds = $observer->getEvent()->getProducts();
        foreach ($productIds as $productId) {
            $product = Mage::getModel('catalog/product')->load($productId);
            $event = new Varien_Object();
            $event->setProduct($product);
            $observer = new Varien_Event_Observer();
            $observer->setEvent($event);
            $this->productSave($observer);
        }

        return $this;
    }
}

/* Filename: Observer.php */
/* Location: app/code/community/Squareup/Omni/Model/Observer.php */
