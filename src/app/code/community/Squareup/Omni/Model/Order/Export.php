<?php

/**
 * Order Export Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Order_Export extends Mage_Core_Model_Abstract
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
     * @var Squareup_Omni_Helper_Config
     */
    protected $_configHelper;

    protected $_lineItems;

    public $grouped;

    public $productData;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_logHelper = Mage::helper('squareup_omni/log');
        $this->_helper = Mage::helper('squareup_omni');
        $this->_apiClient = $this->_helper->getClientApi();
        $this->_configHelper = Mage::helper('squareup_omni/config');
    }

    /**
     * Process order and send it to square
     * @param $orderId
     * @param $magentoAmount
     * @return array|bool
     */
    public function processOrder($orderId)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        $orderId = $order->getId();
        if (empty($orderId)) {
            return false;
        }

        $this->_logHelper->info(
            $this->_helper->__(
                'Export %s order to Square App', $order->getIncrementId()
            )
        );
        $request = $this->createRequest($order);

        if (empty($request)) {
            return false;
        }

        $squareOrder = $this->createSquareOrder($request);
        $this->saveSquareOrderId($squareOrder->getId(), $order->getId());

        return $squareOrder;
    }

    /**
     * Create order
     * @param $request
     * @return bool|\SquareConnect\Model\Order
     */
    public function createSquareOrder($request)
    {
        try{
            $api = new SquareConnect\Api\OrdersApi($this->_apiClient);
            $request = new SquareConnect\Model\CreateOrderRequest($request);
            $response = $api->createOrder($this->_configHelper->getLocationId(), $request);
            $responseErrors = $response->getErrors();
            if (empty($responseErrors)) {
                return $response->getOrder();
            }
        } catch (\SquareConnect\ApiException $e) {
            $this->_logHelper->error($e->__toString());

            $errorMessage = $e->getMessage();
            if (!empty($e->getResponseBody()->errors) && is_array($e->getResponseBody()->errors)) {
                $errorMessage = '';
                foreach ($e->getResponseBody()->errors as $error) {
                    $errorMessage .= $error->detail . ' ';
                }
            }

            Mage::throwException(trim($errorMessage));
        }

        return false;
    }


    /**
     * Create request.
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return array
     *
     * @throws Exception
     */
    public function createRequest($order)
    {
        $storeId = Mage::app()->getStore();
        $this->_lineItems = array();

        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() != Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
                continue;
            }

            $this->productData = array();
            $squareVariationId = Mage::getResourceModel('catalog/product')
                ->getAttributeRawValue($item->getProductId(), 'square_variation_id', $storeId);

            if (!$squareVariationId) {
                Mage::throwException(
                    sprintf('Product %s is not synchronized. Order placement failed.', $item->getProductId())
                );
            }

            $this->prepareProductData($item);
            $tax = number_format(
                (($this->productData['tax'] / $item->getQtyOrdered()) * 100) / ($this->productData['price'] -
                    (abs($this->productData['discount'] / $item->getQtyOrdered()))), 5
            );

            if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
                $this->prepareSimpleProduct($order, $item, $squareVariationId, $tax);
            }
        }

        if ($order->getShippingAmount()) {
            $this->_lineItems[] = array(
                'name' => 'Shipping Amount',
                'quantity' => '1',
                'base_price_money' => array(
                    'amount' => (int)$this->_helper->processAmount($order->getShippingAmount()),
                    'currency' => $order->getOrderCurrencyCode()
                )
            );
        }

        return array(
            'idempotency_key' => uniqid(),
            'reference_id' => $order->getIncrementId(),
            'line_items' => $this->_lineItems
        );
    }

    /**
     * Prepare product data.
     *
     * @param Mage_Sales_Model_Order_Item $item
     *
     * @return void
     */
    protected function prepareProductData($item)
    {
        $quoteItem = Mage::getModel('sales/quote_item')->load($item->getQuoteItemId());

        if ($quoteItem->getParentItemId()) {
            $parentItem = Mage::getModel('sales/quote_item')->load($quoteItem->getParentItemId());
            $this->productData = array(
                'price' => $parentItem->getPrice(),
                'tax' => $parentItem->getTaxAmount(),
                'discount' => $parentItem->getDiscountAmount(),
            );
        } else {
            $this->productData = array(
                'price' => $quoteItem->getPrice(),
                'tax' => $quoteItem->getTaxAmount(),
                'discount' => $quoteItem->getDiscountAmount(),
            );
        }
    }

    /**
     * Prepare simple product.
     *
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Item $item
     * @param string $squareVariationId
     * @param float $tax
     *
     * @return void
     */
    public function prepareSimpleProduct($order, $item, $squareVariationId, $tax)
    {
        $this->_lineItems[] = array(
            'quantity' => (string)(int)$item->getQtyOrdered(),
            'catalog_object_id' => $squareVariationId,
            'taxes' => array(
                array(
                    'name' => sprintf('Product %s Tax', $item->getName()),
                    'percentage' => (string)$tax
                )
            ),
            'base_price_money' => array(
                'amount' => (int)$this->_helper->processAmount($this->productData['price']),
                'currency' => $order->getOrderCurrencyCode()
            ),
            'discounts' => array(
                array(
                    'name' => 'Product Discount',
                    'amount_money' => array(
                        'amount' => (int)abs($this->_helper->processAmount($this->productData['discount'])),
                        'currency' => $order->getOrderCurrencyCode()
                    )
                )
            )
        );
    }

    /**
     * Prepare grouped associated products.
     *
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Item $item
     * @param string $squareVariationId
     *
     * @return void
     */
    public function prepareGroupedAssociatedProduct($order, $item, $squareVariationId)
    {
        $this->_lineItems[] = array(
            'quantity' => (string)(int)$item->getQtyOrdered(),
            'catalog_object_id' => $squareVariationId,
            'base_price_money' => array(
                'amount' => 0,
                'currency' => $order->getOrderCurrencyCode()
            ),
            'taxes' => array(
                array(
                    'name' => sprintf('Product %s Tax', $item->getName()),
                    'percentage' => '0'
                )
            ),
            'discounts' => array(
                array(
                    'name' => 'Product Discount',
                    'amount_money' => array(
                        'amount' => 0,
                        'currency' => $order->getOrderCurrencyCode()
                    )
                )
            )
        );

        $grouped = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($item->getProductId());
        $this->grouped[$grouped[0]][] = array(
            'price' => $item->getPrice(),
            'qty' => $item->getQtyOrdered(),
            'tax_amount' => $item->getTaxAmount(),
            'discount' => $item->getDiscountAmount()
        );
    }

    /**
     * Prepare grouped product.
     *
     * @param int|string $groupedProduct
     * @param array $data
     *
     * @return void
     */
    public function prepareGroupedProduct($groupedProduct, $data)
    {
        $amount = $taxAmount = $discount = 0;
        $product = Mage::getModel('catalog/product')->load($groupedProduct);

        foreach ($data as $item) {
            $amount += $item['price'] * $item['qty'];
            $taxAmount += $item['tax_amount'];
            $discount += abs($item['discount']);
        }

        $taxPercent = number_format(($taxAmount * 100) / ($amount - $discount), 5);

        $this->_lineItems[] = array(
            'quantity' => '1',
            'catalog_object_id' => $product->getSquareVariationId(),
            'base_price_money' => array(
                'amount' => $this->_helper->processAmount($amount),
                'currency' => 'USD'
            ),
            'taxes' => array(
                array(
                    'name' => sprintf('Product %s Tax', $product->getName()),
                    'percentage' => (string)$taxPercent
                )
            ),
            'discounts' => array(
                array(
                    'name' => 'Product Discount',
                    'amount_money' => array(
                        'amount' => (int)$this->_helper->processAmount($discount),
                        'currency' => 'USD'
                    )
                )
            )
        );
    }

    /**
     * Save square_order_id value to magento order
     * @param $squareId
     * @param $orderId
     */
    public function saveSquareOrderId($squareId, $orderId)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        $oId = $order->getId();
        if (!empty($oId)) {
            try {
                $order->setSquareOrderId($squareId)->save();
            } catch (Exception $e) {
                $this->_logHelper->error($e->__toString());
            }
        }
    }
}