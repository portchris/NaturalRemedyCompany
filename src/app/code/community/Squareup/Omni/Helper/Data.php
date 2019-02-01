<?php
/**
 * SquareUp
 *
 * Data Helper
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var Squareup_Omni_Helper_Config
     */
    protected $_config;
    protected $_log;
    public $mediaLocation = '/square';


    /**
     * Squareup_Omni_Helper_Data constructor.
     */
    public function __construct()
    {
        $this->_config = Mage::helper('squareup_omni/config');
        $this->_log = Mage::helper('squareup_omni/log');
    }

    public function processAmount($amount, $currency = "USD")
    {
        // TODO better use of currency
        /*
        if ("USD" == $currency) {
            return (float)$amount * 100;
        }
        */

        return (float)$amount * 100;
    }

    public function transformAmount($amount, $currency = "USD")
    {
        // TODO better use of currency
        /*
        if ("USD" == $currency) {
            return (float)$amount / 100;
        }
        */

        return (float)$amount / 100;
    }

    public function getClientApi()
    {
        $authToken = $this->_config->getOAuthToken();
        $apiConfig = new SquareConnect\Configuration();
        $apiConfig->setAccessToken($authToken);
        $apiClient = new SquareConnect\ApiClient($apiConfig);

        return $apiClient;
    }

    public function getRegionCodeById($id)
    {
        $region = Mage::getModel('directory/region')->load($id);
        return $region->getCode();
    }

    public function prepImageDir()
    {
        $folder = new Varien_Io_File();
        if (!$folder->fileExists(Mage::getBaseDir('media') . $this->mediaLocation, false)) {
            $folder->mkdir(Mage::getBaseDir('media') . $this->mediaLocation);
        }

        return true;
    }


    public function getLocationsOptionArray()
    {
        $collection = Mage::getModel('squareup_omni/location')
            ->getResourceCollection();
        $optionsArray = array();
        if (!empty($collection)) {
            foreach ($collection as $item) {
                $optionsArray[$item->getSquareId()] = $item->getName();
            }
        }

        return $optionsArray;
    }

    /**
     * Reset customer square_id values
     */
    public function resetSquareCustomerFlag()
    {
        $attributeSquareCustomerId = Mage::getSingleton('eav/config')->getAttribute('customer', 'squareup_customer_id');
        $attributeSquareUpdatedAt = Mage::getSingleton('eav/config')->getAttribute('customer', 'square_updated_at');
        $attributeSquareSavedCards = Mage::getSingleton('eav/config')->getAttribute('customer', 'square_saved_cards');
        /** @var $coreResource Mage_Core_Model_Resource */
        $coreResource = Mage::getSingleton('core/resource');

        /** @var $conn Varien_Db_Adapter_Pdo_Mysql */
        $conn = $coreResource->getConnection('core_write');

        $conn->beginTransaction();
        try {
            $conn->delete(
                $coreResource->getTableName($attributeSquareCustomerId->getBackendTable()),
                array('attribute_id = ?' => $attributeSquareCustomerId->getAttributeId())
            );
            $conn->delete(
                $coreResource->getTableName($attributeSquareUpdatedAt->getBackendTable()),
                array('attribute_id = ?' => $attributeSquareUpdatedAt->getAttributeId())
            );
            $conn->delete(
                $coreResource->getTableName($attributeSquareSavedCards->getBackendTable()),
                array('attribute_id = ?' => $attributeSquareSavedCards->getAttributeId())
            );
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
        }
    }

    /**
     * Check if customer is logged in
     * @return bool
     */
    public function isCustomerLogged()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /**
     * Check if customer have saved cards
     * @return bool
     */
    public function haveSavedCards()
    {

        if ($this->isCustomerLogged()) {
            $customer = $this->getCustomer();
            $customerSquareSavedCards = $customer->getSquareSavedCards();
            if ($customer && !empty($customerSquareSavedCards)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get customer
     * @return mixed
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    /**
     * Get customer saved cards
     * @return bool|mixed
     */
    public function getCustomerCards()
    {
        $cardSaved = $this->getCustomer()->getSquareSavedCards();
        if (!empty($cardSaved)) {
            return json_decode($cardSaved, true);
        }

        return false;
    }

    /**
     * Create card label
     *
     * @param $card
     * @return string
     */
    public function getCardInputTitle($card)
    {
        $title = $card['cardholder_name'] . ' | ' . $card['card_brand'] . ' | ' . $card['exp_month'] . '/'
            . $card['exp_year'] . ' | **** ' . $card['last_4'];

        return $title;
    }

    /**
     * Check if customer can save cards on file
     * @return bool
     */
    public function canSaveCards()
    {
        $squareCustomerId = $this->getCustomer()->getSquareupCustomerId();
        if ($this->isCustomerLogged() && !empty($squareCustomerId)) {
            return true;
        }

        return false;
    }

    /**
     * Check if customer payed with a saved card
     * @param $cardId
     * @return bool
     */
    public function payedWithSavedCard($cardId)
    {
        $customer = $this->getCustomer();
        $customerCards = $this->getCustomerCards();
        if (!empty($customer) && !empty($customerCards)) {
            if (array_key_exists($cardId, $customerCards)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get card on file select options
     * @return mixed
     */
    public function getCardOnFileOption()
    {
        return Mage::getStoreConfig('payment/squareup_payment/enable_save_card_on_file');
    }

    /**
     * Check if card on file is enabled
     * @return bool
     */
    public function isSaveOnFileEnabled()
    {
        $option = $this->getCardOnFileOption();
        if ($option == Squareup_Omni_Model_Card::DISALLOW_CARD_ON_FILE) {
            return false;
        }

        return true;
    }

    /**
     * Display Save card checkbox on frontend
     * @return bool
     */
    public function displaySaveCcCheckbox()
    {
        $option = $this->getCardOnFileOption();
        if ($option == Squareup_Omni_Model_Card::ALLOW_CARD_ON_FILE) {
            return true;
        }

        return false;

    }

    /**
     * Check if only card on file is enabled
     * @return bool
     */
    public function onlyCardOnFileEnabled()
    {
        $option = $this->getCardOnFileOption();
        if ($option == Squareup_Omni_Model_Card::ALLOW_ONLY_CARD_ON_FILE) {
            return true;
        }

        return false;
    }

    public function saveRanAt($ranAt)
    {
        Mage::getConfig()->saveConfig(
            'squareup_omni/general/cron_ran_at',
            $ranAt
        );

        Mage::app()->cleanCache();
    }

    public function getProductLocations($id)
    {
        $ids = Mage::getResourceModel('squareup_omni/product')->getProductLocations($id);
        if (empty($ids) || !in_array($this->_config->getLocationId(), $ids)) {
            $locationId = $this->_config->getLocationId();
            if (!empty($locationId)) {
                $ids[] = $this->_config->getLocationId();
            }
        }

        return $ids;
    }

    public function subscribeWebhook()
    {
        $token = Mage::helper('squareup_omni/config')->getOAuthToken();
        if (null === $token) {
            Mage::helper('squareup_omni/log')->error('Token not found on webhooks subscribe');
            return $this;
        }

        $locationIds = array();
        $collection = Mage::getModel('squareup_omni/location')
            ->getResourceCollection()
            ->addFieldToFilter('status', 1);
        foreach ($collection as $item) {
            $locationIds[] = $item->getSquareId();
        }

        $errors = array();
        foreach ($locationIds as $locationId) {
            $url = 'https://connect.squareup.com/v1/' . $locationId . '/webhooks';

            $config = array(
                'adapter'   => 'Zend_Http_Client_Adapter_Socket',
            );
            $oauthRequestHeaders = array (
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            );
            $oauthRequestBody = array("PAYMENT_UPDATED", "INVENTORY_UPDATED");

            try {
                $client = new Zend_Http_Client($url, $config);
                $client->setMethod(Zend_Http_Client::PUT);
                $client->setConfig(array('timeout' => 60));
                $client->setHeaders($oauthRequestHeaders);
                $client->setRawData(json_encode($oauthRequestBody));
                $response = $client->request();
            } catch (Exception $e) {
                Mage::helper('squareup_omni/log')->error($e->__toString());
                return $this;
            }

            $responseObject = json_decode($response->getBody());
            if (isset($responseObject->type)) {
                $errors[] = false;
            } else {
                return true;
            }
        }

        return (in_array(false, $errors))? false : true;
    }

    /**
     * Create a cron job for a manual start of the cron
     *
     * @param $type
     * @return bool|false|Mage_Core_Model_Abstract
     */
    public function createCronJob($type)
    {
        $ts = strftime('%Y-%m-%d %H:%M:%S', time());
        $job = Mage::getModel('cron/schedule');
        $job->setData(
            array(
                'job_code' => $type,
                'status' => 'running',
                'messages' => 'Started Manually',
                'created_at' => $ts,
                'scheduled_at' => $ts,
                'executed_at' => $ts
            )
        );

        try {
            $job = $job->save();
        } catch (Exception $e) {
            $this->_log->info($e->__toString());
            return false;
        }

        return $job;
    }

    /**
     * Finish a job that is started manually
     * @param $job
     * @return bool
     */
    public function finishJob($job)
    {
        try {
            $job->addData(
                array(
                    'status' => 'success',
                    'messages' => 'Manual job finished',
                    'finished_at' => strftime('%Y-%m-%d %H:%M:%S', time())
                )
            );

            $job->save();
        } catch (Exception $e) {
            $this->_log->info($e->__toString());
            return false;
        }

        return true;
    }

    /**
     * Checking if a cron process has a lock on the file to determine if process is actively running
     * @param $type
     * @return bool|resource
     */
    public function checkCronJobRunning($type)
    {
        $fh = fopen(Mage::getBaseDir('var') . DS . $type, 'w');
        if (!flock($fh, LOCK_EX | LOCK_NB)) {
            return true;
        }

        return $fh;
    }


    /**
     * Clean cron jobs that remained hanged
     *
     * @param $cronCollection
     * @param $currentId
     * @return bool
     */
    public function cleanCronJobs($cronCollection, $currentId)
    {
        foreach ($cronCollection as $job) {
            if ($currentId == $job->getId()) {
                continue;
            }

            try {
                $job->setStatus('failed');
                $job->setMessages('Old hanged job was cleared');
                $job->setFinishedAt(strftime('%Y-%m-%d %H:%M:%S', time()));
                $job->save();
                $this->_log->info('Old hanged job was cleared');
            } catch (Exception $e) {
                $this->_log->error($e->__toString());
            }
        }

        return true;
    }

}

/* Filename: Data.php */
/* Location: app/code/community/Squareup/Omni/Helper/Data.php */