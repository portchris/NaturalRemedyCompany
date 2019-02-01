<?php
/**
 * SquareUp
 *
 * Customer Delete Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Customer_Delete extends Mage_Core_Model_Abstract
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
     * Delete Customer from SquareUp app on Magento Customer Delete
     * @param $squareupCustomerId
     * @return bool
     */
    public function deleteSquareupCustomer($squareupCustomerId)
    {
        try {
            /* Call SquareUp delete customer method */
            $api = new SquareConnect\Api\CustomersApi($this->_apiClient);
            $api->deleteCustomer($squareupCustomerId);
            $this->_logHelper->info(
                $this->_helper->__('Customer with SquareUp id:%s was deleted.', $squareupCustomerId)
            );
        } catch (\SquareConnect\ApiException $e) {
            $this->_logHelper->error($e->__toString());
            return false;
        }

        return true;
    }

    /**
     * Delete Customers from Magento
     * @param $customers
     * @return bool
     */
    public function deleteMagentoCustomers($customers)
    {
        try {
            foreach ($customers as $id => $squareId) {
                Mage::register('isSecureArea', true);
                $customer = Mage::getModel('customer/customer')->load($id);
                $customer->setData('deleted_from_square', 1);
                $customer->delete();
                $this->_logHelper->info('Customer %s was deleted from Magento by Square sync', $id);
                Mage::unregister('isSecureArea');
            }
        } catch (Exception $e) {
            $this->_logHelper->error($e->__toString());
            return false;
        }

        return true;
    }
}