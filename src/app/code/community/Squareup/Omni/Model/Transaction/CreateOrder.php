<?php

/**
 * Transaction CreateOrder Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Transaction_CreateOrder extends Squareup_Omni_Model_Square
{
    /**
     * @var mixed
     */
    public $websiteId;

    /**
     * @var Mage_Core_Model_Store
     */
    public $store;

    /**
     * @var \SquareConnect\Api\CustomersApi
     */
    protected $_customerApi;

    /**
     * @var \SquareConnect\Api\V1TransactionsApi
     */
    protected $_transactionApi;

    /**
     * @var \SquareConnect\Model\Customer
     */
    public $squareCustomer;

    /**
     * @var \SquareConnect\Model\Transaction
     */
    public $transaction;

    /**
     * @var \SquareConnect\Model\V1Payment
     */
    public $payment;

    /**
     * @var array
     */
    public $products = array();

    /**
     * @var Mage_Customer_Model_Customer
     */
    public $customer;

    /**
     * @var float
     */
    public $taxAmount;

    /**
     * @var float
     */
    public $shippingAmount;

    /**
     * @var float
     */
    public $grandTotal;

    /**
     * @var float
     */
    public $subtotal;

    /**
     * @var array
     */
    protected $updatedStocks;

    /**
     * Squareup_Omni_Model_Transaction_CreateOrder constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->websiteId = Mage::app()->getWebsite()->getId();
        $this->store = Mage::app()->getStore();
        $this->_customerApi = new \SquareConnect\Api\CustomersApi($this->_helper->getClientApi());
        $this->_transactionApi = new \SquareConnect\Api\V1TransactionsApi($this->_helper->getClientApi());
    }

    /**
     * @param SquareConnect\Model\Transaction $transaction
     * @param string $locationId
     *
     * @return void
     */
    public function processTransaction($transaction, $locationId)
    {
        if (!$this->_config->isConvertTransactionsEnabled()) {
            return;
        }

        try {
            foreach ($transaction->getTenders() as $tender) {
                $this->products = array();
                $this->transaction = $transaction;

                if ($this->checkCustomer($tender->getCustomerId()) &&
                    $this->checkItems($tender->getId(), $locationId) &&
                    $this->checkOrder($transaction->getId())) {
                    $this->createOrder();
                }
            }
        } catch (\Exception $exception) {
            $this->_log->error($exception->__toString());
        }
    }

    /**
     * Create new customer.
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function createCustomer()
    {
        $squareupAddress = $this->squareCustomer->getAddress();

        if (!$squareupAddress) {
            $this->_log->error('Customer address not found.');
            return false;
        }

        $firstName = $squareupAddress->getFirstName() ? $squareupAddress->getFirstName() : $this->squareCustomer->getFamilyName();
        $lastName = $squareupAddress->getLastName() ? $squareupAddress->getLastName() : $this->squareCustomer->getGivenName();

        $this->customer = Mage::getModel('customer/customer')
            ->setWebsiteId($this->websiteId)
            ->setStore($this->store)
            ->setFirstname($firstName)
            ->setLastname($lastName)
            ->setEmail($this->squareCustomer->getEmailAddress())
            ->setSquareupJustImported(2);

        try {
            $this->customer->save();
        } catch (\Exception $exception) {
            $this->_log->error($exception->__toString());
        }

        $squareupAddress = $this->squareCustomer->getAddress();
        if (count(array_filter((array)$squareupAddress)) > 2) {
            $addressId = $this->customer->getData('default_billing');
            $address = Mage::getModel("customer/address");
            if (!empty($addressId)) {
                $address->load($addressId);
            }

            $address->setCustomerId($this->customer->getId())
                ->setFirstname($this->customer->getFirstname())
                ->setMiddleName($this->customer->getMiddlename())
                ->setLastname($this->customer->getLastname())
                ->setCountryId($squareupAddress->getCountry())
                ->setPostcode($squareupAddress->getPostalCode())
                ->setCity($squareupAddress->getLocality())
                ->setTelephone($this->squareCustomer->getPhoneNumber())
                ->setStreet($squareupAddress->getAddressLine1())
                ->setIsDefaultBilling('1')
                ->setIsDefaultShipping('1')
                ->setSaveInAddressBook('1');

            $squareAdministrativeDistrictLevel1 = $squareupAddress->getAdministrativeDistrictlevel1();
            if (!empty($squareAdministrativeDistrictLevel1)) {
                $region = Mage::getModel('directory/region')
                    ->loadByCode(
                        $squareupAddress->getAdministrativeDistrictlevel1(),
                        $squareupAddress->getCountry()
                    );
                $stateId = $region->getId();
                $address->setRegionId($stateId);
            }

            try {
                $address->save();
                return true;
            } catch (\Exception $exception) {
                $this->_log->error($exception->__toString());
            }
        }

        return false;
    }

    /**
     * Check transaction customer.
     *
     * @param string $customerId
     *
     * @return bool
     * @throws Varien_Exception
     * @throws \SquareConnect\ApiException
     */
    protected function checkCustomer($customerId)
    {
        if (!$customerId) {
            $this->_log->error('Customer id not found.');
            return false;
        }

        $customer = $this->_customerApi->retrieveCustomer($customerId);

        if (!$customer->getErrors()) {
            $this->squareCustomer = $customer->getCustomer();
            $this->customer = Mage::getModel('customer/customer');
            $this->customer->setWebsiteId($this->websiteId);
            $this->customer->loadByEmail($this->squareCustomer->getEmailAddress());

            if ($this->customer->getId()) {
                return true;
            }

            if ($this->createCustomer()) {
                return true;
            }

            return false;
        }

        $this->_log->error($this->customer->getErrors()->getDetail());

        return false;
    }

    /**
     * Check transaction items.
     *
     * @param string $paymentId
     * @param string $locationId
     *
     * @return bool
     */
    protected function checkItems($paymentId, $locationId)
    {
        if (!$paymentId) {
            return false;
        }

        try {
            $this->payment = $this->_transactionApi->retrievePayment($locationId, $paymentId);
            $itemizations = $this->payment->getItemizations();
            $counter = $this->shippingAmount = 0;

            foreach ($itemizations as $itemization) {
                if ($sku = $itemization->getItemDetail()->getSku()) {
                    $product = Mage::getModel('catalog/product');
                    if (!$productId = $product->getIdBySku($sku)) {
                        return false;
                    }

                    $product = $product->load($productId);
                    $price = number_format(
                        ($itemization->getGrossSalesMoney()->getAmount() / $itemization->getQuantity()) / 100, 4
                    );
                    $product->setPrice((double)$price);
                    $this->products[$product->getId()] = array(
                        'quantity' => $itemization->getQuantity(),
                        'model' => $product,
                    );

                    $this->prepareProductTax($product->getId(), $itemization);
                } else {
                    $this->shippingAmount = $itemization->getTotalMoney()->getAmount() / 100;
                    $counter++;
                }
            }

            if ($counter == count($itemizations)) {
                $this->_log->info(
                    'Order was not created because there were missing items inside transaction ' . $paymentId
                );

                return false;
            }

            return true;
        } catch (\Exception $exception) {
            $this->_log->error($exception->__toString());
        }
    }

    /**
     * Prepare product tax.
     *
     * @param int|string $productId
     * @param $itemization
     *
     * @return void
     */
    protected function prepareProductTax($productId, $itemization)
    {
        $percent = $amount = 0;

        foreach ($itemization->getTaxes() as $tax) {
            $percent += $tax->getRate();
            $amount += $tax->getAppliedMoney()->getAmount() / 100;
        }

        $this->products[$productId]['tax'] = array(
            'percent' => $percent,
            'amount' => $amount,
        );
    }

    /**
     * Check if order with transaction id exists.
     *
     * @param string $transactionId
     *
     * @return bool
     */
    protected function checkOrder($transactionId)
    {
        $order = Mage::getModel('sales/order')
            ->load($transactionId, 'square_order_id');

        if ($order->getEntityId()) {
            sprintf('Transaction %s was already converted. Order Id %s', $transactionId, $order->getEntityId());

            return false;
        }

        return true;
    }

    /**
     * Create order.
     *
     * @return void
     */
    protected function createOrder()
    {
        $this->updatedStocks = array();

        try {
            $billingAddress = array();

            if ($this->customer) {
                $this->customer->setGroupId(null);
            }

            $shippingMethod = 'square_shipping_square_shipping';
            $paymentMethod = 'squareup_transaction_payment';
            $address = $this->squareCustomer->getAddress();
            $quote = Mage::getModel('sales/quote')
                ->setStoreId($this->store->getId());

            if ($address) {
                $billingAddress = array(
                    'firstname' => $address->getFirstName() ?
                        $address->getFirstName() : $this->squareCustomer->getFamilyName(),
                    'lastname' => $address->getLastName() ?
                        $address->getLastName() : $this->squareCustomer->getGivenName(),
                    'company' => $address->getOrganization(),
                    'street' => $address->getAddressLine1() . ' ' . $address->getAddressLine2(),
                    'city' => $address->getLocality(),
                    'country_id' => $address->getCountry(),
                    'postcode' => $address->getPostalCode(),
                    'telephone' => $this->squareCustomer->getPhoneNumber(),
                    'region_id' => $address->getAdministrativeDistrictLevel1(),
                );
            }

            $billingAddressData = $quote->getBillingAddress()->addData($billingAddress);
            $shippingAddressData = $quote->getShippingAddress()->addData($billingAddress);

            $quote->setCurrency(Mage::app()->getStore()->getBaseCurrencyCode());

            if ($this->customer) {
                $quote->assignCustomer($this->customer);
            }

            foreach ($this->products as $productData) {
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productData['model']->getId());
                $qty = $stockItem->getQty();
                $productData['model']->setStockData(array(
                    'use_config_manage_stock' => 1,
                    'is_in_stock' => 1,
                    'qty' => $qty + $productData['quantity'],
                    'manage_stock' => 1
                ));
                $productData['model']->save();
                $this->updatedStocks[] = array(
                    'model' => $productData['model'],
                    'quantity' => $qty
                );
                $quote->addProduct($productData['model'], $productData['quantity']);
            }

            $shippingAddressData->setCollectShippingRates(true)
                ->collectShippingRates();

            $shippingAddressData->setShippingMethod($shippingMethod)
                ->setPaymentMethod($paymentMethod);

            foreach ($quote->getAddressesCollection() as $address) {
                if ($rates = $address->getShippingRatesCollection()) {
                    $items = $rates->getItems();
                    foreach ($items as $item) {
                        $item->setPrice($this->shippingAmount);
                    }
                }
            }

            $quote->getPayment()->importData(array('method' => $paymentMethod));
            $quote->collectTotals();
            $quote->save();

            $this->updateQuote($quote);

            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();

            $order = $service->getOrder();
            $order->setSquareOrderId($this->transaction->getId())->save();
            $this->updateOrder($order);

            $this->createInvoice($order);
        } catch (\Exception $exception) {
            foreach ($this->updatedStocks as $updatedStock) {
                $updatedStock['model']->setStockData(array(
                    'use_config_manage_stock' => 1,
                    'is_in_stock' => 1,
                    'qty' => $updatedStock['quantity'],
                    'manage_stock' => 1
                ));
                $updatedStock['model']->save();
            }
            $this->_log->error($exception->__toString());
        }
    }

    /**
     * Update quote.
     *
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return void
     */
    protected function updateQuote($quote)
    {
        $this->grandTotal = $this->taxAmount = $this->subtotal = 0;

        foreach ($quote->getItemsCollection() as $item) {
            $tax = $this->products[$item->getProductId()]['tax'];

            $item->addData(
                array(
                    'tax_percent' => $tax['percent'],
                    'tax_amount' => $tax['amount'],
                    'base_tax_amount' => $tax['amount'],
                    'price_incl_tax' => $item->getPrice() + $tax['amount'],
                    'base_price_incl_tax' => $item->getPrice() + $tax['amount'],
                    'row_total_incl_tax' => ($item->getPrice() * $item->getQty()) + $tax['amount'],
                    'base_row_total_incl_tax' => ($item->getPrice() * $item->getQty()) + $tax['amount']
                )
            )->save();

            $this->grandTotal += $item->getRowTotalInclTax();
            $this->taxAmount += $tax['amount'];
        }

        $this->subtotal = $this->grandTotal - $this->taxAmount;

        $quote->addData(array(
            'grand_total' => $this->grandTotal,
            'base_grand_total' => $this->grandTotal,
            'subtotal' => $this->subtotal,
            'base_subtotal' => $this->subtotal,
            'subtotal_with_discount' => $this->subtotal,
            'base_subtotal_with_discount' => $this->subtotal
        ))->save();
    }

    protected function updateOrder($order)
    {
        $order->addData(array(
            'base_grand_total' => $this->grandTotal,
            'grand_total' => $this->grandTotal,
            'base_subtotal' => $this->subtotal,
            'subtotal' => $this->subtotal,
            'base_tax_amount' => $this->taxAmount,
            'tax_amount' => $this->taxAmount,
            'base_subtotal_incl_tax' => $this->taxAmount + $this->subtotal,
            'subtotal_incl_tax' => $this->taxAmount + $this->subtotal
        ));

        if ($this->transaction->getProduct() == 'REGISTER') {
            $order->addStatusHistoryComment('Transaction from register');
        }

        $order->save();

        foreach ($order->getAllItems() as $item) {
            $product = $this->products[$item->getProductId()]['model'];

            $item->addData(
                array(
                    'price' => $product->getPrice(),
                    'base_price' => $product->getPrice(),
                    'original_price' => $product->getPrice(),
                    'base_original_price' => $product->getPrice(),
                    'row_total' => $item->getQtyOrdered() * $product->getPrice(),
                    'base_row_total' => $item->getQtyOrdered() * $product->getPrice(),
                    'price_incl_tax' => (double)$product->getPrice() +
                        (double)$this->products[$item->getProductId()]['tax'],
                    'base_price_incl_tax' => (double)$product->getPrice() +
                        (double)$this->products[$item->getProductId()]['tax']
                )
            )->save();
        }
    }

    /**
     * Create invoice.
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return void
     */
    protected function createInvoice($order)
    {
        if (!$order->canInvoice()) {
            Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
        }

        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();

        if (!$invoice->getTotalQty()) {
            Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
        }

        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
        $invoice->register();
        $order = $invoice->getOrder()->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
        $order->addStatusHistoryComment(sprintf('Square POS - Magento Order Id %s', $this->transaction->getId()));

        Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($order)
            ->save();

        if ($this->transaction->getRefunds()) {
            $this->createCreditMemo($invoice);
        }
    }

    /**
     * Create credit memo.
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     *
     * @return void
     */
    protected function createCreditMemo($invoice)
    {
        $service = Mage::getModel('sales/service_order', $invoice->getOrder());

        $creditmemo = $service->prepareInvoiceCreditmemo($invoice);
        $creditmemo->setRefundRequested(true)
            ->setOfflineRequested(true)
            ->register();

        Mage::getModel('core/resource_transaction')
            ->addObject($creditmemo)
            ->addObject($creditmemo->getOrder())
            ->addObject($creditmemo->getInvoice())
            ->save();
    }
}
