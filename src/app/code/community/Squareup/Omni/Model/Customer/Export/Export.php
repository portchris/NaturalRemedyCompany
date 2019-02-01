<?php
/**
 * SquareUp
 *
 * Customer Export Mapping Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Customer_Export_Export extends Mage_Core_Model_Abstract
{
    /**
     * @var \SquareConnect\ApiClient
     */
    protected $_apiClient;

    /**
     * @var Squareup_Omni_Helper_Log
     */
    protected $_logHelper;

    /**
     * @var Squareup_Omni_Helper_Data
     */
    protected $_helper;

    /**
     * Squareup_Omni_Model_Customer_Export_Export constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_logHelper = Mage::helper('squareup_omni/log');
        $this->_helper = Mage::helper('squareup_omni');
        $this->_apiClient = $this->_helper->getClientApi();
    }

    /**
     * Export all customers to SquareUp app
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function exportCustomers()
    {
        $customersData = Mage::getModel('squareup_omni/customer_export_mapping')->getNotExportedCustomers();
        if (empty($customersData)) {
            $this->_logHelper->error($this->_helper->__('SquareUp Error: Customers Data for export cron is empty.'));
            return false;
        }

        try {
            foreach ($customersData as $customer) {
                if (is_null($customer)) {
                    continue;
                }
                /* Call SquareUp create customer method */
                $api = new SquareConnect\Api\CustomersApi($this->_apiClient);
                $response = $api->createCustomer($customer);
                $updatedAt = $response->getCustomer()->getUpdatedAt();
                if ($squareCustomerId = $response->getCustomer()->getId()) {
                    $customerObj = Mage::getModel('customer/customer')->load($customer['reference_id']);
                    /* Save squareup_customer_id attribute */
                    $this->saveSquareupIdAttribute($customerObj, $squareCustomerId, $updatedAt);
                }
            }
        } catch (\SquareConnect\ApiException $e) {
            $this->_logHelper->error($e->__toString());
            Mage::throwException($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Export new customer to SquareUp app
     * @param $customer
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function exportNewCustomer($customer)
    {
        $customerData = Mage::getModel('squareup_omni/customer_export_mapping')->mapNewCustomer($customer);
        if (empty($customerData) || is_null($customerData)) {
            $this->_logHelper->error($this->_helper->__('SquareUp Error: Customer Data for new customer is empty'));
            return false;
        }

        try {
            /* Call SquareUp create customer method */
            $api = new SquareConnect\Api\CustomersApi($this->_apiClient);
            $response = $api->createCustomer($customerData);
            $updatedAt = $response->getCustomer()->getUpdatedAt();
            if ($squareCustomerId = $response->getCustomer()->getId()) {
                /* Save squareup_customer_id attribute */
                $this->saveSquareupIdAttribute($customer, $squareCustomerId, $updatedAt);
            }
        } catch (\SquareConnect\ApiException $e) {
            $this->_logHelper->error($e->__toString());
            Mage::throwException($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Update customer on SquareUp app
     * @param $customerId
     * @param $squareId
     * @return bool
     */
    public function updateSquareCustomer($customerId, $squareId)
    {
        $customerData = Mage::getModel('squareup_omni/customer_export_mapping')->processCustomer($customerId);
        if (is_null($customerData) || (empty($customerData) && empty($squareId))) {
            $this->_logHelper->error(
                $this->_helper->__('SquareUp Error: Customer Data for update customer $s is empty', $customerId)
            );
            return false;
        }

        try {
            /* Call SquareUp update customer method */
            $api = new SquareConnect\Api\CustomersApi($this->_apiClient);
            $response = $api->updateCustomer($squareId, $customerData);
            $responseCustomerId = $response->getCustomer()->getId();
            if (empty($responseCustomerId)) {
                $this->_logHelper->error(
                    $this->_helper->__('SquareUp Error: Issue on updating customer %s in SquareUp app.', $customerId)
                );
                return false;
            }
            $this->_logHelper->info($this->_helper->__('Customer %s was updated in SquareUp app.', $customerId));
        } catch (\SquareConnect\ApiException $e) {
            $this->_logHelper->error($e->__toString());
            Mage::throwException($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Save SquareUp id to customer obj
     * @param $customer
     * @param $squareupId
     * @param $updatedAt
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function saveSquareupIdAttribute($customer, $squareupId, $updatedAt)
    {
        try {
            $customer->setData('squareup_customer_id', $squareupId);
            $customer->setData('square_updated_at', $updatedAt);
            $customer->getResource()->saveAttribute($customer, 'squareup_customer_id');
            $customer->getResource()->saveAttribute($customer, 'square_updated_at');
        } catch (Exception $e) {
            $this->_logHelper->error($e->__toString());
            Mage::throwException($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Send ReferenceId in SquareUp for new customers imported
     * @param $squareupId
     * @param $referenceId
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function sendSquareupCustomerReferenceId($squareupId, $referenceId)
    {
        $customerData = array(
            'reference_id' => $referenceId,
        );
        try {
            /* Call SquareUp update customer method */
            $api = new SquareConnect\Api\CustomersApi($this->_apiClient);
            $response = $api->updateCustomer($squareupId, $customerData);
            $responseCustomerId = $response->getCustomer()->getId();
            if (empty($responseCustomerId)) {
                $this->_logHelper->error(
                    $this->_helper->__('SquareUp Error: Issue on updating customer %s in SquareUp app.', $referenceId)
                );
                return false;
            }
        } catch (\SquareConnect\ApiException $e) {
            $this->_logHelper->error($e->__toString());
            Mage::throwException($e->getMessage());
            return false;
        }

        return true;
    }
}