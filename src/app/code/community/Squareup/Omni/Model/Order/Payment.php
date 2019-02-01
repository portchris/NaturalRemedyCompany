<?php

/**
 * Squareup_Omni_Model_Order_Payment
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Order_Payment extends Mage_Sales_Model_Order_Payment
{
    /**
     * Authorize payment either online or offline (process auth notification)
     * Updates transactions hierarchy, if required
     * Prevents transaction double processing
     * Updates payment totals, updates order status and adds proper comments
     *
     * @param bool $isOnline
     * @param float $amount
     * @return Mage_Sales_Model_Order_Payment
     */
    protected function _authorize($isOnline, $amount)
    {
        $squareConfigHelper = Mage::helper('squareup_omni/config');
        $squareHelper = Mage::helper('squareup_omni');
        // check for authorization amount to be equal to grand total

        $this->setShouldCloseParentTransaction(false);
        $isSameCurrency = !$this->getCurrencyCode() ||
            $this->getCurrencyCode() == $this->getOrder()->getBaseCurrencyCode();
        if (!$isSameCurrency || !$this->_isCaptureFinal($amount)) {
            $this->setIsFraudDetected(true);
        }

        // update totals
        $amount = $this->_formatAmount($amount, true);
        $this->setBaseAmountAuthorized($amount);

        // do authorization
        $order  = $this->getOrder();
        $state  = Mage_Sales_Model_Order::STATE_PROCESSING;
        $status = true;
        if ($isOnline) {
            // invoke authorization on gateway
            $this->getMethodInstance()->setStore($order->getStoreId())->authorize($this, $amount);
        }

        // similar logic of "payment review" order as in capturing
        if ($this->getIsTransactionPending()) {
            $message = Mage::helper('sales')->__(
                'Authorizing amount of %s is pending approval on gateway.',
                $this->_formatPrice($amount)
            );
            $state = Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW;
            if ($this->getIsFraudDetected()) {
                $status = Mage_Sales_Model_Order::STATUS_FRAUD;
            }
        } else {
            if ($this->getIsFraudDetected()) {
                $state = Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW;
                $message = Mage::helper('sales')->__(
                    'Order is suspended as its authorizing amount %s is suspected to be fraudulent.',
                    $this->_formatPrice($amount, $this->getCurrencyCode())
                );
                $status = Mage_Sales_Model_Order::STATUS_FRAUD;
            } else {
                $message = Mage::helper('sales')->__('Authorized amount of %s.', $this->_formatPrice($amount));

                //Remove message if is enabled: Allow only card on file and Authorize only.
                if (Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE == $squareConfigHelper->getPaymentAction()
                    && Squareup_Omni_Model_Card::ALLOW_ONLY_CARD_ON_FILE == $squareHelper->getCardOnFileOption()
                    && $this->getMethodInstance()->getCode() == Squareup_Omni_Model_Payment::CODE) {
                    $message = '';
                }
            }
        }

        // update transactions, order state and add comments
        $transaction = $this->_addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
        if ($order->isNominal()) {
            $message = $this->_prependMessage(Mage::helper('sales')->__('Nominal order registered.'));
        } else {
            $message = $this->_prependMessage($message);
            $message = $this->_appendTransactionToMessage($transaction, $message);
        }

        $order->setState($state, $status, $message);

        return $this;
    }
}