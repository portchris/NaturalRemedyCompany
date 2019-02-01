<?php

/**
 * Location Import Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Location_Import extends Mage_Core_Model_Abstract
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
     * Insert / Update location in database
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function updateLocations()
    {
        try {
            $api = new SquareConnect\Api\LocationsApi($this->_apiClient);
            $response = $api->listLocations();
            $locations = $response->getLocations();
            if (!empty($locations)) {
                foreach ($locations as $location) {
                    $this->saveLocation($location);
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
     * Save location in database
     * @param $location
     * @return bool
     */
    public function saveLocation($location)
    {
        $isUpdate = true;
        $squareId = $location->getId();
        try {
            $bdLocation = Mage::getModel('squareup_omni/location')->load($squareId, 'square_id');
            $bdData = $bdLocation->getData();
            if (empty($bdData)) {
                $isUpdate = false;
                $bdLocation->setSquareId($squareId);
            }

            $status = 0;
            if ($location->getStatus() == 'ACTIVE') {
                $status = 1;
            }

            $bdLocation->setSquareId($squareId)->setName($location->getName())
                ->setPhoneNumber($location->getPhoneNumber())
                ->setStatus($status)
                ->setCurrency($location->getCurrency());

            $locationAddress = $location->getAddress();
            if (!empty($locationAddress)) {
                $bdLocation->setAddressLine1($locationAddress->getAddressLine1())
                    ->setLocality($locationAddress->getLocality())
                    ->setAdministrativeDistrictLevel1($locationAddress->getAdministrativeDistrictLevel1())
                    ->setPostalCode($locationAddress->getPostalCode())
                    ->setCountry($locationAddress->getCountry());
            }

            $bdLocation->save();
            $this->_logHelper->info(
                $isUpdate ? 'Location with ID %s was updated' : 'Location with ID %s was inserted', $squareId
            );
        } catch (Exception $e) {
            $this->_logHelper->error($e->__toString());
            return false;
        }

        return true;
    }
}