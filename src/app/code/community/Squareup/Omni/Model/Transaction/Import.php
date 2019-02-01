<?php

/**
 * Transaction Import Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Transaction_Import extends Squareup_Omni_Model_Square
{
    /**
     * @var \SquareConnect\ApiClient
     */
    protected $_apiClient;

    /**
     * @var Squareup_Omni_Model_Transaction_CreateOrder
     */
    protected $_createOrder;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_apiClient = $this->_helper->getClientApi();
        $this->_createOrder = Mage::getModel('squareup_omni/transaction_createOrder');
    }

    /**
     * Insert transactions in database
     * @return bool
     */
    public function importTransactions()
    {
        $this->_log->info('Start import transactions.');
        $beginTime = $this->_config->getTransactionsBeginTime();
        $beginTimeToSave = date("c", strtotime(now()));
        $locations = Mage::getModel('squareup_omni/location')
            ->getResourceCollection()
            ->addFieldToFilter('status', 1);
        foreach ($locations as $location) {
            $this->_log->info('Start import transaction on location #' . $location->getSquareId());
            $this->processTransactions($location->getSquareId(), $beginTime, null);
        }

        Mage::getConfig()->saveConfig('squareup_omni/transactions/begin_time', $beginTimeToSave);
        Mage::app()->getConfig()->reinit();

        return true;
    }

    /**
     * Process transactions by beginTime and cursor
     * @param $beginTime
     * @param $cursor
     * @return bool
     */
    public function processTransactions($locationId, $beginTime = null, $cursor = null)
    {

        try {
            $api = new SquareConnect\Api\TransactionsApi($this->_apiClient);
            $response = $api->listTransactions($locationId, $beginTime, null, null, $cursor);
            $responseErrors = $response->getErrors();
            $responseTransactions = $response->getTransactions();
            if (empty($responseErrors) && !empty($responseTransactions)) {
                $transactions = $response->getTransactions();
                $this->_log->info('Location: ' . $locationId);
                $this->_log->info('Count: ' . count($transactions));
                if (!empty($transactions)) {
                    foreach ($transactions as $transaction) {
                        $this->saveTransaction($transaction);
                        $this->_createOrder->processTransaction($transaction, $locationId);
                    }
                }

                $cursor = $response->getCursor();
                if (!empty($cursor)) {
                    $this->processTransactions($locationId, $beginTime, $cursor);
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
     * Save transaction in database
     * @param $transaction
     * @return bool
     */
    public function saveTransaction($transaction)
    {
        try {
            $tenders = $transaction->getTenders();
            $importedTenders = $this->getImportedTenders();
            foreach ($tenders as $tender) {
                if (in_array($tender->getId(), $importedTenders)) {
                    return true;
                }

                $model = Mage::getModel('squareup_omni/transaction');
                $model->setSquareId($transaction->getId())
                    ->setTenderId($tender->getId())
                    ->setLocationId($transaction->getLocationId())
                    ->setCreatedAt($transaction->getCreatedAt())
                    ->setCustomerSquareId($tender->getCustomerId());

                $amount = $this->_helper->transformAmount($tender->getAmountMoney()->getAmount());
                $processingFee = $this->_helper->transformAmount($tender->getProcessingFeeMoney()->getAmount());
                $model->setAmount($amount)
                    ->setProcessingFeeAmount($processingFee);
                if (null !== $transaction->getProduct() && $transaction->getProduct() == 'REGISTER') {
                    $note = 'Transaction from ' . $transaction->getProduct();
                } else {
                    $note = $tender->getNote();
                }

                $model->setNote($note);

                if ($tender->getType() == Squareup_Omni_Model_Transaction::TYPE_CARD_LABEL) {
                    $model->setType(Squareup_Omni_Model_Transaction::TYPE_CARD_VALUE)
                        ->setCardBrand($tender->getCardDetails()->getCard()->getCardBrand());
                } elseif ($tender->getType() == Squareup_Omni_Model_Transaction::TYPE_CASH_LABEL) {
                    $model->setType(Squareup_Omni_Model_Transaction::TYPE_CASH_VALUE);
                }

                $model->save();
            }

            $this->_log->info($this->_helper->__('Transaction %s was saved.', $transaction->getId()));
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        return true;
    }

    /**
     * Get tenders Id for all transactions from Magento
     *
     * @return array
     */
    public function getImportedTenders()
    {
        $collection = Mage::getModel('squareup_omni/transaction')->getCollection()
            ->addFieldToSelect('tender_id');
        $transactionsTendersIds = array();
        foreach ($collection as $item) {
            $transactionsTendersIds[] = $item->getData('tender_id');
        }

        return $transactionsTendersIds;
    }

    public function singleTransaction($locationId, $transactionId)
    {
        try {
            $api = new SquareConnect\Api\TransactionsApi($this->_apiClient);
            $response = $api->retrieveTransaction($locationId, $transactionId);
            $errors = $response->getErrors();
            $transaction = $response->getTransaction();
            if (empty($errors) && !empty($transaction)) {
                $transactionExists = Mage::getResourceModel('squareup_omni/transaction')
                    ->transactionExists($locationId, $transactionId);
                if (false !== $transactionExists) {
                    $this->saveTransaction($transaction);
                    $this->_createOrder->processTransaction($transaction, $locationId);
                }
            }
        } catch (\SquareConnect\ApiException $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        return true;
    }
}
