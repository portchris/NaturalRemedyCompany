<?php
/**
 * SquareUp
 *
 * Payment Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    // TODO add payment info in the order screen
    // TODO add error checks for responses from squareup
    // TODO need to change $idempotencyKey to something that is usable or i need to add it to db
    protected $_code = 'squareup_payment';
    const CODE = 'squareup_payment';
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canRefund = true;
    protected $_canVoid = true;
    protected $_canUseForMultishipping = false;

    protected $_formBlockType = 'squareup_omni/payment_form';
    protected $_infoBlockType = 'squareup_omni/payment_info';
    protected $_configHelper;
    protected $_logHelper;
    protected $_squareHelper;

    protected $_authToken = '';
    protected $_locationId = '';
    protected $_apiClient;

    /**
     * Assign the custom data to the payment
     * @param mixed $data
     * @return $this|Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        $info = $this->getInfoInstance();
        if ($data->getNonce()) {
            $info->setSquareupNonce($data->getNonce());
        }

        /* if customer selected to save payment card set save_square_card flag */
        $dataSaveSquareCard = $data->getSaveSquareCard();
        if (!empty($dataSaveSquareCard) && (int)$data->getSaveSquareCard() == 1) {
            $info->setSaveSquareCard(1);
        } else {
            $info->setSaveSquareCard(null);
        }

        return $this;
    }

    /**
     * Check if the custom data is valid
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Mage_Core_Exception
     */
    public function validate()
    {
        parent::validate();
        $info = $this->getInfoInstance();
        $errorMsg = false;

        if (!$info->getSquareupNonce()) {
            $errorMsg = $this->_getHelper()->__("Nonce is a required field.\n");
        }

        if ($errorMsg) {
            Mage::helper('squareup_omni/log')->error($errorMsg);
            Mage::throwException($errorMsg);
        }

        return $this;
    }

    /**
     * Authorize the amount with Square
     * @param Varien_Object $payment
     * @param float $amount
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Mage_Core_Exception
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        if (!$this->canAuthorize()) {
            Mage::throwException(Mage::helper('squareup_omni')->__('Authorize action is not available.'));
        }

        $this->initApi();
        if (Squareup_Omni_Model_Card::ALLOW_ONLY_CARD_ON_FILE == $this->_squareHelper->getCardOnFileOption()) {
            $this->cardSaveOnFile($payment);
            return $this;
        }

        $this->charge($payment, $amount);

        return $this;
    }

    /**
     * Capture the amount with Square after authorize or direct capture
     * @param Varien_Object $payment
     * @param float $amount
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Mage_Core_Exception
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if (!$this->canCapture()) {
            Mage::throwException(Mage::helper('squareup_omni')->__('Capture action is not available.'));
        }

        $this->initApi();
        if (self::ACTION_AUTHORIZE_CAPTURE === $this->_configHelper->getPaymentAction()) {
            $this->charge($payment, $amount, self::ACTION_AUTHORIZE_CAPTURE);
            return $this;
        }

        if (self::ACTION_AUTHORIZE === $this->_configHelper->getPaymentAction()
            && Squareup_Omni_Model_Card::ALLOW_ONLY_CARD_ON_FILE == $this->_squareHelper->getCardOnFileOption()) {
            $this->charge($payment, $amount, self::ACTION_AUTHORIZE_CAPTURE);
            return $this;
        }

        $transactionsId = $payment->getTransactionId();
        $transactionsId = str_replace('-capture', '', $transactionsId);

        try {
            $transactionsApi = new \SquareConnect\Api\TransactionsApi($this->_apiClient);
            $transactionsApi->captureTransaction($this->_locationId, $transactionsId);
        } catch (\SquareConnect\ApiException $e) {
            $this->_logHelper->error($e->__toString());
            Mage::throwException($e->getMessage());
        }

        return $this;
    }

    /**
     * Refunding the order to Square
     * @param Varien_Object $payment
     * @param float $amount
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Mage_Core_Exception
     */
    public function refund(Varien_Object $payment, $amount)
    {
        if (!$this->canRefund()) {
            Mage::throwException(Mage::helper('squareup_omni')->__('Refund action is not available.'));
        }

        $order = $payment->getOrder();
        $this->initApi();
        $transactionsApi = new \SquareConnect\Api\TransactionsApi($this->_apiClient);

        try {
            $transactionResponse = $transactionsApi->retrieveTransaction(
                $this->_locationId, $payment->getSquareupTransaction()
            );
            $transaction = $transactionResponse->getTransaction();
        } catch (\SquareConnect\ApiException $e) {
            $this->_logHelper->error($e->__toString());
            Mage::throwException($e->getMessage());
        }

        $sendAmount = Mage::helper('squareup_omni')->processAmount($amount);
        $idempotencyKey = uniqid();
        $data = array(
            'tender_id' => $transaction->getTenders()[0]->getId(),
            'amount_money' => array(
                'amount' => $sendAmount,
                'currency' => $order->getOrderCurrencyCode()
            ),
            'idempotency_key' => $idempotencyKey,
            'reason' => 'Refund order #' . $order->getIncrementId() . ' from location #' . $this->_locationId
        );
        $requestData = new \SquareConnect\Model\CreateRefundRequest($data);

        try {
            $this->_logHelper->info('refund transaction id# ' . $payment->getSquareupTransaction());
            $transactionsApi->createRefund($this->_locationId, $payment->getSquareupTransaction(), $requestData);
            $payment->setTransactionId($payment->getSquareupTransaction() . '-refund');
            $payment->addTransaction('refund');
        } catch (\SquareConnect\ApiException $e) {
            $this->_logHelper->error($e->__toString());
            Mage::throwException($e->getMessage());
        }

        return $this;
    }

    /**
     * Voiding the payment to Square
     * @param Varien_Object $payment
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Mage_Core_Exception
     */
    public function void(Varien_Object $payment)
    {
        if (!$this->canVoid($payment)) {
            Mage::throwException(Mage::helper('squareup_omni')->__('Void action is not available.'));
        }

        $this->initApi();
        $transactionId = str_replace('-void', '', $payment->getTransactionId());
        try {
            $transactionsApi = new \SquareConnect\Api\TransactionsApi($this->_apiClient);
            $transactionsApi->voidTransaction($this->_locationId, $transactionId);
        } catch (\SquareConnect\ApiException $e) {
            $this->_logHelper->error($e->__toString());
            Mage::throwException($e->getMessage());
        }

        return $this;
    }

    /**
     * Initialize required options
     * @return bool
     */
    public function initApi()
    {
        $this->_configHelper = Mage::helper('squareup_omni/config');
        $this->_logHelper = Mage::helper('squareup_omni/log');
        $this->_squareHelper = Mage::helper('squareup_omni');
        $this->_authToken = $this->_configHelper->getOAuthToken(true);
        $this->_locationId = $this->_configHelper->getLocationId();
        $apiConfig = new \SquareConnect\Configuration();
        $apiConfig->setAccessToken($this->_authToken);
        $this->_apiClient = new \SquareConnect\ApiClient($apiConfig);

        return true;
    }

    /**
     * Cancel order, voiding the payment to Square
     * @param Varien_Object $payment
     * @return Mage_Payment_Model_Abstract|Squareup_Omni_Model_Payment
     * @throws Mage_Core_Exception
     */
    public function cancel(Varien_Object $payment)
    {
        return $this->void($payment);
    }

    /**
     * The real call to Square for authorize or capture
     * @param Varien_Object $payment
     * @param $amount
     * @param string $type
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function charge(Varien_Object $payment, $amount, $type='authorize')
    {
        $order = $payment->getOrder();

        if (null === $order->getId()) {
            Mage::throwException(Mage::helper('squareup_omni')->__('Order not found.'));
        }

        $billingAddress = $order->getBillingAddress();

        if (null === $billingAddress->getId()) {
            Mage::throwException(Mage::helper('squareup_omni')->__('Billing address not found.'));
        }

        $shippingAddress = $order->getShippingAddress();

        if ($shippingAddress ===  false || null === $shippingAddress->getId()) {
            $shippingAddress = $billingAddress;
            $this->_logHelper->info('Shipping address not found, defaulting to billing address');
        }

        $txRequest = array();
        $txRequest['buyer_email_address'] = $order->getCustomerEmail();

        $txRequest['shipping_address'] = array(
            'address_line_1' => implode(",", $shippingAddress->getStreet()),
            'locality' => $shippingAddress->getCity(),
            'administrative_district_level_1' => Mage::helper('squareup_omni')
                ->getRegionCodeById($shippingAddress->getRegionId()),
            'postal_code' => $shippingAddress->getPostcode(),
            'country' => $shippingAddress->getCountryId()
        );
        $txRequest['billing_address'] = array(
            'address_line_1' => implode(",", $billingAddress->getStreet()),
            'address_line_2' => "",
            'administrative_district_level_1' => Mage::helper('squareup_omni')
                ->getRegionCodeById($billingAddress->getRegionId()),
            'locality' => $billingAddress->getCity(),
            'postal_code' => $billingAddress->getPostcode(),
            'country' => $billingAddress->getCountryId()
        );

        $cardNonce = $payment->getSquareupNonce();

        if (null === $cardNonce) {
            Mage::throwException(Mage::helper('squareup_omni')->__('Card nonce not found.'));
        }

        $sendAmount = Mage::helper('squareup_omni')->processAmount($amount);

        /* Save order to square */
        if ($this->_configHelper->getAllowOrdersSync()) {
            $response = Mage::getModel('squareup_omni/order_export')->processOrder($order->getId());

            if ($response->getId()) {
                $this->_logHelper->info('Order exported.');
                $txRequest['order_id'] = $response->getId();
            } else {
                if (Mage::helper('squareup_omni/config')->getApplicationMode() ===
                    Squareup_Omni_Model_System_Config_Source_Options_Mode::PRODUCTION_ENV) {
                    Mage::throwException(Mage::helper('squareup_omni')->__('Order was not exported to square.'));
                }
            }

            if ((int)$sendAmount != (int)$response->getTotalMoney()->getAmount()) {
                Mage::throwException('Order grand total is not equal with square order amount');
            }
        }

        /* save card on file */
        $customer = $order->getCustomer();
        $orderCustomerId = $order->getCustomerId();
        if (empty($customer) && !empty($orderCustomerId)) {
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        }

        $savedCardId = null;
        $customerSquareId = $customer->getSquareupCustomerId();
        if ($payment->getData('save_square_card') == 1 && !empty($customerSquareId)) {
            $this->_logHelper->info('Saving customer '. $customer->getId() . ' credit card');
            $cardRequest = array(
                'card_nonce' => $cardNonce,
                'billing_address' => $txRequest['billing_address'],
                'cardholder_name' => $customer->getFirstname() ." ".$customer->getLastname()
            );

            $savedCardId = Mage::getModel('squareup_omni/card')->sendSaveCard(
                $customer->getId(), $customer->getSquareSavedCards(), $customer->getSquareupCustomerId(), $cardRequest
            );
        }

        $idempotencyKey = uniqid();
        $txRequest['idempotency_key'] = $idempotencyKey;

        $txRequest['amount_money'] = array('amount' => $sendAmount, 'currency' => $order->getOrderCurrencyCode());
        if (!empty($savedCardId)) {
            $txRequest['customer_card_id'] = $savedCardId;
            $txRequest['customer_id'] = $customer->getSquareupCustomerId();
        } elseif ($this->_squareHelper->haveSavedCards() && $this->_squareHelper->payedWithSavedCard($cardNonce)) {
            $txRequest['customer_card_id'] = $cardNonce;
            $txRequest['customer_id'] = $customer->getSquareupCustomerId();
        } else {
            $txRequest['card_nonce'] = $cardNonce;
            $customerSquareCId = $customer->getSquareupCustomerId();
            if (!$order->getCustomerIsGuest() && !empty($customerSquareCId)) {
                $txRequest['customer_id'] = $customer->getSquareupCustomerId();
            }
        }

        $txRequest['reference_id'] = 'Confirmation #' . $order->getIncrementId();
        $txRequest['integration_id'] = "sqi_6cf03eb6ac24400ab1e21fbe9d8666b1";
        $txRequest['note'] = 'Magento Order Id #' . $order->getIncrementId();

        // because it is authorization we delay to capture
        $txRequest['delay_capture'] = (self::ACTION_AUTHORIZE_CAPTURE === $type)? false : true;
        $transactionsApi = new \SquareConnect\Api\TransactionsApi($this->_apiClient);
        $txRequestData = new \SquareConnect\Model\ChargeRequest($txRequest);

        try {
            $this->_logHelper->debug(json_encode($txRequestData));
            $apiResponse = $transactionsApi->charge($this->_locationId, $txRequestData);
            $transaction = $apiResponse->getTransaction();
            $this->_logHelper->info($type . ' transaction id# ' . $transaction->getId());

            $payment->setSquareupTransaction($transaction->getId());
            $payment->setTransactionId($transaction->getId());
            $isClosed = (self::ACTION_AUTHORIZE_CAPTURE == $type)? true : false;
            $payment->setIsTransactionClosed($isClosed);
        } catch (\SquareConnect\ApiException $e) {
            $this->_logHelper->error($e->__toString());
            Mage::throwException($e->getMessage());
        }

        return $this;
    }

    public function cardSaveOnFile($payment)
    {
        $cardNonce = $payment->getSquareupNonce();
        if (null === $cardNonce) {
            $this->_logHelper->info('Card nonce not found.');
            Mage::throwException(Mage::helper('squareup_omni')->__('Card nonce not found.'));
            return false;
        }

        $order = $payment->getOrder();
        $customer = $order->getCustomer();
        $customerId = $order->getCustomerId();
        if (empty($customer) && !empty($customerId)) {
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        }

        if (null === $customer->getSquareupCustomerId()) {
            Mage::throwException(Mage::helper('squareup_omni')->__('You must be a register customer in order to place an order.'));
            return false;
        }

        $address = $order->getBillingAddress();
        $billingAddress = array(
            'address_line_1' => implode(",", $address->getStreet()),
            'address_line_2' => "",
            'administrative_district_level_1' => Mage::helper('squareup_omni')
                ->getRegionCodeById($address->getRegionId()),
            'locality' => $address->getCity(),
            'postal_code' => $address->getPostcode(),
            'country' => $address->getCountryId()
        );

        $cardRequest = array(
            'card_nonce' => $cardNonce,
            'billing_address' => $billingAddress,
            'cardholder_name' => $customer->getFirstname() ." ".$customer->getLastname()
        );

        try {
            if ($customer->getSquareSavedCards()) {
                $savedCards = json_decode($customer->getSquareSavedCards(), true);

                if (isset($cardRequest['card_nonce']) && array_key_exists($cardRequest['card_nonce'], $savedCards)) {
                    return true;
                }
            }

            $savedCardId = Mage::getModel('squareup_omni/card')->sendSaveCard(
                $customer->getId(), $customer->getSquareSavedCards(), $customer->getSquareupCustomerId(), $cardRequest
            );
        } catch (Exception $e) {
            $this->_logHelper->error($e->__toString());
            Mage::throwException(Mage::helper('squareup_omni')->__('Error saving card on file.'));
            return false;
        }

        return $savedCardId;
    }
}

/* Filename: Payment.php */
/* Location: app/code/community/Squareup/Omni/Model/Payment.php */