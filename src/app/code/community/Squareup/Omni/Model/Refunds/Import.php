<?php

/**
 * Refunds Import Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Refunds_Import extends Squareup_Omni_Model_Square
{
    /**
     * @var \SquareConnect\ApiClient
     */
    protected $_apiClient;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_apiClient = $this->_helper->getClientApi();
    }

    /**
     * Insert refunds in database
     * @return bool
     */
    public function importRefunds()
    {
        $this->_log->info('Start import refunds.');
        $beginTime = $this->_config->getRefundsBeginTime();
        $beginTimeToSave = date("c", strtotime(now()));
        $locations = Mage::getModel('squareup_omni/location')
            ->getResourceCollection()
            ->addFieldToFilter('status', 1);
        foreach ($locations as $location) {
            $this->processRefunds($location->getSquareId(), $beginTime, null);
        }

        Mage::getConfig()->saveConfig('squareup_omni/refunds/begin_time', $beginTimeToSave);
        Mage::app()->getConfig()->reinit();

        return true;
    }

    /**
     * Process refunds by beginTime and cursor
     * @param $beginTime
     * @param $cursor
     * @return bool
     */
    public function processRefunds($locationId, $beginTime = null, $cursor = null)
    {
        try {
            $api = new SquareConnect\Api\TransactionsApi($this->_apiClient);
            $response = $api->listRefunds($locationId, $beginTime, null, null, $cursor);
            $responseErrors = $response->getErrors();
            $refunds = $response->getRefunds();
            if (empty($responseErrors) && !empty($refunds)) {
                $refunds = $response->getRefunds();
                $this->_log->info('Location: ' . $locationId);
                $this->_log->info('Count: ' . count($refunds));
                if (!empty($refunds)) {
                    $importedRefundsIds = $this->getImportedRefunds();
                    foreach ($refunds as $refund) {
                        if (in_array($refund->getId(), $importedRefundsIds)) {
                            continue;
                        }

                        $this->saveRefund($refund);
                    }
                }

                $cursor = $response->getCursor();
                if (!empty($cursor)) {
                    $this->processRefunds($locationId, $beginTime, $cursor);
                }
            }
        } catch (\SquareConnect\ApiException $e) {
            $this->_log->error($e->__toString());
            Mage::throwException($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Save refund in database
     * @param $refund
     * @return bool
     */
    public function saveRefund($refund)
    {
        try {
            $amount = $this->_helper->transformAmount($refund->getAmountMoney()->getAmount());
            $processFee = $refund->getProcessingFeeMoney();
            if (!empty($processFee)) {
                $processingFee = $this->_helper->transformAmount($refund->getProcessingFeeMoney()->getAmount());
            }

            switch ($refund->getStatus()){
                case Squareup_Omni_Model_Refunds::STATUS_PENDING_LABEL :
                    $status = Squareup_Omni_Model_Refunds::STATUS_PENDING_VALUE;
                    break;
                case Squareup_Omni_Model_Refunds::STATUS_APPROVED_LABEL :
                    $status = Squareup_Omni_Model_Refunds::STATUS_APPROVED_VALUE;
                    break;
                case Squareup_Omni_Model_Refunds::STATUS_REJECTED_LABEL :
                    $status = Squareup_Omni_Model_Refunds::STATUS_REJECTED_VALUE;
                    break;
                case Squareup_Omni_Model_Refunds::STATUS_FAILED_LABEL :
                    $status = Squareup_Omni_Model_Refunds::STATUS_FAILED_VALUE;
                    break;
                default :
                    $status = 0;
                    break;
            }

            $model = Mage::getModel('squareup_omni/refunds');
            $model->setSquareId($refund->getId())
                ->setLocationId($refund->getLocationId())
                ->setTransactionId($refund->getTransactionId())
                ->setTenderId($refund->getTenderId())
                ->setCreatedAt($refund->getCreatedAt())
                ->setReason($refund->getReason())
                ->setAmount($amount)
                ->setStatus($status);
            if (!empty($processingFee)) {
                $model->setProcessingFeeAmount($processingFee);
            }

            $model->save();
            $this->_log->info($this->_helper->__('Refund %s was saved.', $refund->getId()));
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        return true;
    }

    /**
     * Get Square Id for all refunds from Magento
     *
     * @return array
     */
    public function getImportedRefunds()
    {
        $collection = Mage::getModel('squareup_omni/refunds')->getCollection()
            ->addFieldToSelect('square_id');
        $refundsSquareIds = array();
        foreach ($collection as $item) {
            $refundsSquareIds[] = $item->getData('square_id');
        }

        return $refundsSquareIds;
    }
}