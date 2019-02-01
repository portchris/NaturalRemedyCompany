<?php
/**
 * SquareUp
 *
 * Config Helper
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Helper_Config extends Mage_Core_Helper_Abstract
{
    public function getApplicationId($sandbox = null)
    {
        if ($sandbox) {
            return Mage::getStoreConfig('squareup_omni/general/sandbox_application_id');
        }

        return Mage::getStoreConfig('squareup_omni/general/application_id');
    }

    public function getApplicationSecret($sandbox = null)
    {
        if ($sandbox) {
            return Mage::getStoreConfig('squareup_omni/general/sandbox_application_secret');
        }

        return Mage::getStoreConfig('squareup_omni/general/application_secret');
    }

    public function getLocationId()
    {
        if ($this->isSandbox()) {
            return Mage::getStoreConfig('squareup_omni/general/sandbox_application_location');
        }

        return Mage::getStoreConfig('squareup_omni/general/location_id');
    }

    public function getLocationCurrency()
    {
        $locationId = $this->getLocationId();
        $location = Mage::getModel('squareup_omni/location')->load($locationId, 'square_id');
        $currency = null;
        if ($location && $location->getId()) {
            $currency = $location->getCurrency();
        }

        return $currency;
    }

    public function getPaymentAction()
    {
        return Mage::getStoreConfig('payment/squareup_payment/payment_action');
    }

    public function getOAuthToken($trans = null)
    {
        if ($trans) {
            return (!$this->isSandbox())?
                                        Mage::getStoreConfig('squareup_omni/oauth_settings/oauth_token') :
                                        $this->getApplicationSecret(true);
        }

        return (!$this->isSandbox()) ?
                                    Mage::getStoreConfig('squareup_omni/oauth_settings/oauth_token') :
                                    $this->getApplicationSecret(true);
    }

    public function getOAuthExpire()
    {
        return Mage::getStoreConfig('squareup_omni/oauth_settings/oauth_expire');
    }

    /**
     * @return bool
     */
    public function getAllowCustomerSync()
    {
        return Mage::getStoreConfigFlag('squareup_omni/customer/customer_sync');
    }

    public function getAllowImportTrans()
    {
        return Mage::getStoreConfigFlag('squareup_omni/orders/import_trans');
    }

    public function getAllowOrdersSync()
    {
        return Mage::getStoreConfigFlag('squareup_omni/orders/create_order');
    }

    public function isSandbox()
    {
        return (Mage::getStoreConfig('squareup_omni/general/application_mode') === 'sandbox')? true : false;
    }

    public function getSor()
    {
        return Mage::getStoreConfig('squareup_omni/catalog/sor');
    }

    public function isCatalogEnabled()
    {
        return (Mage::getStoreConfig('squareup_omni/catalog/enable_catalog') === '1')? true : false;
    }

    public function isInventoryEnabled()
    {
        return (Mage::getStoreConfig('squareup_omni/catalog/enable_inventory') === '1')? true : false;
    }

    public function getApplicationMode()
    {
        return Mage::getStoreConfig('squareup_omni/general/application_mode');
    }

    public function getOldLocationId()
    {
        return Mage::getStoreConfig('squareup_omni/general/location_id_old');
    }

    public function cronRanAt()
    {
        return Mage::getStoreConfig('squareup_omni/general/cron_ran_at');
    }

    public function getTransactionsBeginTime()
    {
        return Mage::getStoreConfig('squareup_omni/transactions/begin_time');
    }
    
    public function getRefundsBeginTime()
    {
        return Mage::getStoreConfig('squareup_omni/refunds/begin_time');
    }

    public function saveTransactionsBeginTime($date = null)
    {
        return Mage::getConfig()->saveConfig('squareup_omni/transactions/begin_time', $date);
    }

    public function saveRefundsBeginTime($date = null)
    {
        return Mage::getConfig()->saveConfig('squareup_omni/refunds/begin_time', $date);
    }

    public function saveRanAt($ranAt = null)
    {
        return Mage::getConfig()->saveConfig('squareup_omni/general/cron_ran_at', $ranAt);
    }

    public function saveOauthToken($token = null)
    {
        return Mage::getConfig()->saveConfig('squareup_omni/oauth_settings/oauth_token', $token);
    }

    public function saveOauthExpire($expire = null)
    {
        return Mage::getConfig()->saveConfig('squareup_omni/oauth_settings/oauth_expire', $expire);
    }

    public function isImagesEnabled()
    {
        return (Mage::getStoreConfig('squareup_omni/catalog/enable_images') === '1')? true : false;
    }

    public function saveImagesRanAt($ranAt = null)
    {
        return Mage::getConfig()->saveConfig('squareup_omni/general/images_ran_at', $ranAt);
    }

    public function getImagesRanAt()
    {
        return Mage::getStoreConfig('squareup_omni/general/images_ran_at');
    }

    public function syncLocationInventory()
    {
        if ($locationId = $this->getLocationId()) {
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
     * Check if order conversion is enabled.
     *
     * @return bool
     */
    public function isConvertTransactionsEnabled()
    {
        return Mage::getStoreConfig('squareup_omni/orders/convert_transactions');
    }
    
    public function getWebhookSignature()
    {
        return Mage::getStoreConfig('squareup_omni/webhooks_settings/webhook_signature');
    }
}

/* Filename: Config.php */
/* Location: app/code/community/Squareup/Omni/Helper/Config.php */