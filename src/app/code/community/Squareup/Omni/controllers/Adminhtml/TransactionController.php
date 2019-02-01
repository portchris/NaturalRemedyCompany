<?php
/**
 * Square Transactions controller.
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Adminhtml_TransactionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Square Api Client
     *
     * @var \SquareConnect\ApiClient
     */
    protected $_apiClient;

    /**
     * Log Helper
     *
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
    public function _construct()
    {
        $this->_logHelper = Mage::helper('squareup_omni/log');
        $this->_helper = Mage::helper('squareup_omni');
        $this->_apiClient = $this->_helper->getClientApi();
    }

    /**
     * Display transactions.
     */
    public function indexAction()
    {
        $this->_title($this->__('Transactions'))->_title($this->__('Transactions'));
        $this->loadLayout();
        $this->_setActiveMenu('sales');
        $this->_addContent($this->getLayout()->createBlock('squareup_omni/adminhtml_transaction'));
        $this->renderLayout();
    }

    /**
     * Grid Action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('squareup_omni/adminhtml_transaction_grid')->toHtml()
        );
    }


    /**
     * Customer square transactions ajax action
     *
     */
    public function customerGridAction()
    {
        $this->_initCustomer();
        $this->loadLayout(false)
            ->renderLayout();
    }

    /**
     * Initialize customer by ID specified in request
     *
     * @return Squareup_Omni_Adminhtml_TransactionController
     */
    protected function _initCustomer()
    {
        $customerId = (int) $this->getRequest()->getParam('id');
        $customer = Mage::getModel('customer/customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);
        return $this;
    }

    /**
    * Export transactions grid to csv
    */
    public function exportCsvAction()
    {
        $fileName = 'square_transaction.csv';
        $grid = $this->getLayout()->createBlock('squareup_omni/adminhtml_transaction_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
    * Export transactions grid to excel
    */
    public function exportExcelAction()
    {
        $fileName = 'square_transaction.xml';
        $grid = $this->getLayout()->createBlock('squareup_omni/adminhtml_transaction_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/squareup_omni/transaction');
    }

    public function refundTransactionAction()
    {
        $transactionId = $this->getRequest()->getParam('id');
        $api = new SquareConnect\Api\TransactionsApi($this->_apiClient);
        $transaction = Mage::getModel('squareup_omni/transaction')->load($transactionId);
        $transId = $transaction->getId();
        if (!empty($transId)) {
            try {
                $idempotencyKey = uniqid();
                $body = array(
                    'tender_id' => $transaction->getTenderId(),
                    'amount_money' => array(
                        'amount' => $this->_helper->processAmount($transaction->getAmount()),
                        'currency' => 'USD'
                    ),
                    'idempotency_key' => $idempotencyKey,
                    'reason' => $this->_helper->__('Cancelled order from Magento')
                );
                $requestData = new \SquareConnect\Model\CreateRefundRequest($body);
                $this->_logHelper->info('refund transaction id# ' . $transaction->getSquareId());
                $response = $api->createRefund($transaction->getLocationId(), $transaction->getSquareId(), $requestData);
                $responseErrors = $response->getErrors();
                $refunds = $response->getRefund();
                if (empty($responseErrors) && !empty($refunds)) {
                    Mage::getModel('squareup_omni/refunds_import')->saveRefund($response->getRefund());
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->_helper->__('The transaction was successfully refunded.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (\SquareConnect\ApiException $e) {
                $this->_logHelper->error($e->__toString());
                $errors = $e->getResponseBody()->errors;
                $detail = '';
                foreach ($errors as $error) {
                    $detail = $error->detail;
                }

                Mage::getSingleton('adminhtml/session')->addError($detail);
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->_helper->__('There was an error refunding this transaction.')
                );
                $this->_redirect('*/*/');
                $this->_logHelper->error($e->__toString());
                return;
            }
        }

        $this->_redirect('*/*/');
    }

    public function refundTransactionCustomerAction()
    {
        $transactionId = $this->getRequest()->getParam('trans_id');
        $customerId = $this->getRequest()->getParam('customer_id');
        $api = new SquareConnect\Api\TransactionsApi($this->_apiClient);
        $transaction = Mage::getModel('squareup_omni/transaction')->load($transactionId);
        $transId = $transaction->getId();
        if (!empty($transId)) {
            try {
                $idempotencyKey = uniqid();
                $body = array(
                    'tender_id' => $transaction->getTenderId(),
                    'amount_money' => array(
                        'amount' => $this->_helper->processAmount($transaction->getAmount()),
                        'currency' => 'USD'
                    ),
                    'idempotency_key' => $idempotencyKey,
                    'reason' => $this->_helper->__('Cancelled order from Magento')
                );
                $requestData = new \SquareConnect\Model\CreateRefundRequest($body);
                $this->_logHelper->info('refund transaction id# ' . $transaction->getSquareId());
                $response= $api->createRefund($transaction->getLocationId(), $transaction->getSquareId(), $requestData);
                $responseErrors = $response->getErrors();
                $refunds = $response->getRefund();
                if (empty($responseErrors) && !empty($refunds)) {
                    Mage::getModel('squareup_omni/refunds_import')->saveRefund($response->getRefund());
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->_helper->__('The transaction was successfully refunded.')
                );
                $this->_redirect(
                    '*/customer/edit',
                    array(
                        'id' => $customerId,
                        '_query' => array(
                            'active_tab' => 'customer_edit_tab_square_transactions'
                        )
                    )
                );
                return;
            } catch (\SquareConnect\ApiException $e) {
                $this->_logHelper->error($e->__toString());
                $errors = $e->getResponseBody()->errors;
                $detail = '';
                foreach ($errors as $error) {
                    $detail = $error->detail;
                }

                Mage::getSingleton('adminhtml/session')->addError($detail);
                $this->_redirect(
                    '*/customer/edit',
                    array(
                        'id' => $customerId,
                        '_query' => array(
                            'active_tab' => 'customer_edit_tab_square_transactions'
                        )
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->_helper->__('There was an error refunding this transaction.')
                );
                $this->_redirect(
                    '*/customer/edit',
                    array(
                        'id' => $customerId,
                        '_query' => array(
                            'active_tab' => 'customer_edit_tab_square_transactions'
                        )
                    )
                );
                $this->_logHelper->error($e->__toString());
                return;
            }
        }

        $this->_redirect(
            '*/customer/edit',
            array(
                'id' => $customerId,
                '_query' => array(
                    'active_tab' => 'customer_edit_tab_square_transactions'
                )
            )
        );
    }
}