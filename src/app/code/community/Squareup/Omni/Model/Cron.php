<?php
ini_set("memory_limit", "-1");
/**
 * SquareUp
 *
 * Cron Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Cron
{
    /**
     * @var Squareup_Omni_Helper_Config
     */
    protected $_config;

    /**
     * @var Squareup_Omni_Helper_Data
     */
    protected $_helper;

    /**
     * @var Squareup_Omni_Helper_Log
     */
    protected $_log;

    /**
     * @var int
     */
    protected $_subDaysRefresh = 23;

    /**
     * Squareup_Omni_Model_Observer constructor.
     */
    public function __construct()
    {
        $this->_config = Mage::helper('squareup_omni/config');
        $this->_helper = Mage::helper('squareup_omni');
        $this->_log = Mage::helper('squareup_omni/log');
    }

    /**
     * Main entry point for cron customers export from SquareUp app
     *
     * @param bool $isManual
     *
     * @return bool
     * @throws Varien_Exception
     */
    public function startCustomerExport($isManual = false)
    {
        if (!$this->_config->getAllowCustomerSync()) {
            return false;
        }

        $cronCollection = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', array('eq' => 'customer_export'))
            ->addFieldToFilter('status', array('eq' => 'running'));

        if ($isManual === true || count($cronCollection) === 1) {
            Mage::getModel('squareup_omni/customer_export_export')->exportCustomers();
        }

        return true;
    }

    /**
     * Main entry point for cron customers import from SquareUp app
     *
     * @param bool $isManual
     *
     * @return bool
     * @throws Varien_Exception
     */
    public function startCustomerImport($isManual = false)
    {
        if (!$this->_config->getAllowCustomerSync()) {
            return false;
        }

        $cronCollection = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', array('eq' => 'customer_import'))
            ->addFieldToFilter('status', array('eq' => 'running'));

        if ($isManual === true || count($cronCollection) === 1) {
            Mage::getModel('squareup_omni/customer_import')->getSquareupCustomers();
        }

        return true;
    }

    /**
     * Main entry point for cron locations import from Square app
     *
     * @return bool
     * @throws Varien_Exception
     */
    public function startLocationsImport()
    {
        $cronCollection = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', array('eq' => 'location_import'))
            ->addFieldToFilter('status', array('eq' => 'running'));

        if (count($cronCollection)) {
            Mage::getModel('squareup_omni/location_import')->updateLocations();
        }

        return true;
    }

    /**
     * Main entry point for cron transactions import from Square app
     *
     * @param bool $isManual
     *
     * @return bool
     * @throws Varien_Exception
     */
    public function startTransactionsImport($isManual = false)
    {
        if (!$this->_config->getAllowImportTrans()) {
            return false;
        }

        $cronCollection = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', array('eq' => 'square_transactions_import'))
            ->addFieldToFilter('status', array('eq' => 'running'));

        if ($isManual === true || count($cronCollection) === 1) {
            Mage::getModel('squareup_omni/transaction_import')->importTransactions();
        }

        return true;
    }

    /**
     * Main entry point for cron refunds import from Square app
     *
     * @param bool $isManual
     *
     * @return bool
     * @throws Varien_Exception
     */
    public function startRefundsImport($isManual = false)
    {
        if (!$this->_config->getAllowImportTrans()) {
            return false;
        }

        $cronCollection = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', array('eq' => 'square_refunds_import'))
            ->addFieldToFilter('status', array('eq' => 'running'));

        if ($isManual === true || count($cronCollection) === 1) {
            Mage::getModel('squareup_omni/refunds_import')->importRefunds();
        }

        return true;
    }

    /**
     * Main entry point for cron catalog import/export from Square app
     *
     * @param bool $isManual
     *
     * @return $this|bool
     * @throws Varien_Exception
     */
    public function startCatalog($isManual = false)
    {
        if (false === $this->_config->isCatalogEnabled()) {
            return $this;
        }

        $cronCollection = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', array('eq' => 'catalog_process'))
            ->addFieldToFilter('status', array('eq' => 'running'));

        $fh = $this->_helper->checkCronJobRunning('catalog');
        if (count($cronCollection) > 1) {
            Mage::helper('squareup_omni/log')->info('Catalog job are more than 1');
            if ($fh === true) {
                $msg = 'Catalog synchronization is already running by the automatic Magento cron';
                Mage::helper('squareup_omni/log')->info($msg);
                return $msg;
            }
        }

        if ($isManual === true && count($cronCollection) > 0) {
            Mage::helper('squareup_omni/log')->info('Manual catalog run detected job collection not empty');
            if ($fh === true) {
                $msg = 'Manual start detected that catalog synchronization is already running by the automatic 
                Magento cron';
                Mage::helper('squareup_omni/log')->info($msg);
                return $msg;
            }
        }

        if ($isManual === true) {
            $job = $this->_helper->createCronJob('catalog_process');
            if (false === $job) {
                return $this;
            }
        }

        try {
            Mage::getModel('squareup_omni/catalog')->start();
        } catch (Exception $e){
            $this->_log->info($e->__toString());
        }

        if ($isManual === true) {
            $this->_helper->finishJob($job);
        }

        $jobId = ($isManual === true)? $job->getId() : $isManual->getId();
        $this->_helper->cleanCronJobs($cronCollection, $jobId);
        flock($fh, LOCK_UN);
        fclose($fh);

        return 'Catalog Sync Executed';
    }

    /**
     * Main entry point for cron inventory import/export from Square app
     *
     * @param bool $isManual
     *
     * @return $this|bool
     * @throws Varien_Exception
     */
    public function startInventory($isManual = false)
    {
        if (false === $this->_config->isInventoryEnabled() || false === $this->_config->isCatalogEnabled()) {
            return $this;
        }

        $cronCollection = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', array('eq' => 'inventory_process'))
            ->addFieldToFilter('status', array('eq' => 'running'));
        $fh = $this->_helper->checkCronJobRunning('inventory');
        if (count($cronCollection) > 1) {
            Mage::helper('squareup_omni/log')->info('Inventory jobs are more than 1');
            if ($fh === true) {
                $msg = 'Inventory synchronization is already running by the automatic Magento cron';
                Mage::helper('squareup_omni/log')->info($msg);
                return $msg;
            }
        }

        if ($isManual === true && count($cronCollection) > 0) {
            Mage::helper('squareup_omni/log')->info('Manual inventory run detected job collection not empty');
            if ($fh === true) {
                $msg = 'Manual start detected that inventory synchronization is already running by the automatic 
                Magento cron';
                Mage::helper('squareup_omni/log')->info($msg);
                return $msg;
            }
        }

        if ($isManual === true) {
            $job = $this->_helper->createCronJob('inventory_process');
            if (false === $job) {
                return $this;
            }
        }

        try {
            Mage::getModel('squareup_omni/inventory')->start();
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
        }

        if ($isManual === true) {
            $this->_helper->finishJob($job);
        }

        $jobId = ($isManual === true)? $job->getId() : $isManual->getId();
        $this->_helper->cleanCronJobs($cronCollection, $jobId);
        flock($fh, LOCK_UN);
        fclose($fh);

        return 'Inventory Sync Executed';
    }

    /**
     * Refresh the OAuth Token
     *
     * @return bool
     */
    public function refreshOauthToken()
    {
        $this->_log->info('Refresh Oauth Start');
        $applicationId = Mage::helper('squareup_omni/config')->getApplicationId();
        $applicationSecret = Mage::helper('squareup_omni/config')->getApplicationSecret();
        $authToken = Mage::helper('squareup_omni/config')->getOAuthToken();

        if (empty($applicationId) || empty($applicationSecret) || empty($authToken)) {
            $this->_log->error('One of the required configuration is missing for oAuth refresh');
            return false;
        }

        $oAuthExpire = Mage::helper('squareup_omni/config')->getOAuthExpire();
        if (time() < ($oAuthExpire - ($this->_subDaysRefresh * (24 * 60 * 60)))) {
            $this->_log->info('OAuth refresh is not required because the expire date is more than '
                . $this->_subDaysRefresh . ' days away.');
            return true;
        }

        $cronCollection = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', array('eq' => 'oauth_refresh'))
            ->addFieldToFilter('status', array('eq' => 'running'));

        if (count($cronCollection) > 1) {
            $this->_log->info('There are more than one cron jobs for OAuth refresh  running');
            return false;
        }

        $url = 'https://connect.squareup.com/oauth2/clients/' . $applicationId . '/access-token/renew';
        $config = array(
            'adapter'   => 'Zend_Http_Client_Adapter_Socket',
        );
        $oauthRequestHeaders = array (
            'Authorization' => 'Client ' . $applicationSecret,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        );
        $oauthRequestBody = array(
            'access_token' => $authToken
        );

        try {
            $client = new Zend_Http_Client($url, $config);
            $client->setMethod(Zend_Http_Client::POST);
            $client->setHeaders($oauthRequestHeaders);
            $client->setConfig(array('timeout' => 60));
            $client->setRawData(json_encode($oauthRequestBody));
            $response = $client->request();
            $this->_log->info('Refresh Oauth Response: ' . $response->getBody());
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        if ($response->getStatus() != 200) {
            $this->_log->error($response->__toString());
            return false;
        }

        $responseObj = json_decode($response->getBody());
        Mage::getConfig()->saveConfig('squareup_omni/oauth_settings/oauth_token', $responseObj->access_token);
        Mage::getConfig()->saveConfig('squareup_omni/oauth_settings/oauth_expire', strtotime($responseObj->expires_at));

        $this->_log->info('Reinit and refreshing config');
        Mage::app()->getConfig()->reinit();
        Mage::app()->getCacheInstance()->flush();
        $this->_log->info('Refresh oAuth finished');

        return true;
    }

    /**
     * @return $this|bool
     * @throws Varien_Exception
     */
    public function startImages()
    {
        if (false === $this->_config->isImagesEnabled() || false === $this->_config->isCatalogEnabled()) {
            return $this;
        }

        Mage::getModel('squareup_omni/catalog_images')->start();
        return true;
    }

    public function jobsCleanUp()
    {
        $this->_log->info('Start cleanup job');
        $catalogRunning = $this->_helper->checkCronJobRunning('catalog');
        $inventoryRunning = $this->_helper->checkCronJobRunning('inventory');
        $cronCollection = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', array('in' => array('catalog_process', 'inventory_process')))
            ->addFieldToFilter('status', array('eq' => 'running'));

        foreach ($cronCollection as $job) {
            if ($job->getJobCode() == 'catalog_process' && $catalogRunning === true) {
                continue;
            }

            if ($job->getJobCode() == 'inventory_process' && $inventoryRunning === true) {
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

        // Make sure that we don't hold the crons with the lock check
        if ($catalogRunning !== true) {
            flock($catalogRunning, LOCK_UN);
            fclose($catalogRunning);
        }

        if ($inventoryRunning !== true) {
            flock($inventoryRunning, LOCK_UN);
            fclose($inventoryRunning);
        }

        $this->_log->info('End cleanup job');
    }
}

/* Filename: Cron.php */
/* Location: app/code/community/Squareup/Omni/Model/Cron.php */