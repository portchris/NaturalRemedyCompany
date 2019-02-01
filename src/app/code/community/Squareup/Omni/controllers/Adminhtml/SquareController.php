<?php
/**
 * Square Transactions controller.
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Adminhtml_SquareController extends Mage_Adminhtml_Controller_Action
{

    public function inventoryAction()
    {
        try {
            $msg = Mage::getModel('squareup_omni/cron')->startCatalog(true);
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);
            $msg = Mage::getModel('squareup_omni/cron')->startInventory(true);
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);
        } catch (\Exception $exception) {
            Mage::getSingleton('core/session')->addError($exception->getMessage());
        }

        return $this->_redirect('adminhtml/system_config/edit/section/squareup_omni');
    }

    public function transactionsAction()
    {
        try {
            Mage::getModel('squareup_omni/cron')->startTransactionsImport(true);
            Mage::getModel('squareup_omni/cron')->startRefundsImport(true);
            Mage::getSingleton('adminhtml/session')->addSuccess("Transactions and Refunds Sync Executed");
        } catch (\Exception $exception) {
            Mage::getSingleton('core/session')->addError($exception->getMessage());
        }

        return $this->_redirect('adminhtml/system_config/edit/section/squareup_omni');
    }

    public function catalogAction()
    {
        try {
            Mage::getModel('squareup_omni/location_import')->updateLocations();
            $msg = Mage::getModel('squareup_omni/cron')->startCatalog(true);
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);
        } catch (\Exception $exception) {
            Mage::getSingleton('core/session')->addError($exception->getMessage());
        }

        return $this->_redirect('adminhtml/system_config/edit/section/squareup_omni');
    }

    public function customerAction()
    {
        try {
            Mage::getModel('squareup_omni/cron')->startCustomerImport(true);
            Mage::getModel('squareup_omni/cron')->startCustomerExport(true);
            Mage::getSingleton('adminhtml/session')->addSuccess("Customer Sync Executed");
        } catch (\Exception $exception) {
            Mage::getSingleton('core/session')->addError($exception->getMessage());
        }

        return $this->_redirect('adminhtml/system_config/edit/section/squareup_omni');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/configuration/squareup_omni');
    }

    public function locationAction()
    {
        $params = $this->getRequest()->getParams();
        $data = array(
            'product_id' => $params['product_id'],
            'location_id' => $params['location'],
            'status' => '',
            'quantity' => $params['qty'],
            'calculated_at' => Mage::getModel('core/date')->gmtDate(),
            'received_at' => Mage::getModel('core/date')->gmtDate()
        );

        try {
            Mage::getModel('squareup_omni/inventory')
                ->setData($data)
                ->save();

            if (Mage::helper('squareup_omni/config')->getLocationId() == $params['location']) {
                $stockItem  = Mage::getModel('cataloginventory/stock_item')
                    ->loadByProduct($params['product_id']);

                $stockItem->setQty($params['qty']);
                $stockItem->setIsInStock((int)($params['qty'] > 0));
                $stockItem->save();
            }

            $product = Mage::getModel('catalog/product')->load($params['product_id']);
            $product->setName($product->getName())->save();
        } catch (Exception $exception) {
            Mage::helper('squareup_omni/log')->error($exception->getMessage());
        }

        Mage::getModel('squareup_omni/inventory_export')
            ->start(array($params['product_id']), $params['location'], $params['qty']);

        $this->_redirectReferer();
    }

    public function imagesAction()
    {
        try {
            Mage::getModel('squareup_omni/cron')->startImages();
            Mage::getSingleton('adminhtml/session')->addSuccess("Images Sync Executed");
        } catch (\Exception $exception) {
            Mage::getSingleton('core/session')->addError($exception->getMessage());
        }

        return $this->_redirect('adminhtml/system_config/edit/section/squareup_omni');
    }

    public function removeSquareInventoryAction()
    {
        if (!$this->getRequest()->isAjax() ||
            !Mage::helper('squareup_omni/config')->getSor()) { // is magento sor
            return false;
        }

        $params = $this->getRequest()->getParams();

        if (empty($params['locationId']) ||
            empty($params['productId'])) {
            return false;
        }

        $productId = $params['productId'];
        if (isset($params['childProductId']) && !empty($params['childProductId'])) {
            $productId = $params['childProductId'];
        }

        $inventory = Mage::getModel('squareup_omni/inventory')->getCollection()
            ->addFieldToFilter('product_id', array('eq' => $productId))
            ->addFieldToFilter('location_id', array('eq' => $params['locationId']))
            ->getFirstItem();
        $product = Mage::getModel('catalog/product')->load($params['productId']);

        if ($inventory->getId()) {
            $locationId = $inventory->getLocationId();
            if ($locationId == Mage::helper('squareup_omni/config')->getLocationId()) {
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
                $stockItem->setQty(0)
                    ->setIsInStock(0)
                    ->save();
            }

            $inventory->delete();
            $this->removeLocationFromSquare($product, $productId);

            return true;
        }

        return false;
    }

    protected function removeLocationFromSquare($product, $productId)
    {
        $helper = Mage::helper('squareup_omni/data');
        $log = Mage::helper('squareup_omni/log');
        $mapping = Mage::helper('squareup_omni/mapping');
        $apiClient = $helper->getClientApi();
        $catalogApi = new \SquareConnect\Api\CatalogApi($apiClient);

        try {
            // Retrieve the objects
            $receivedObj = $catalogApi->retrieveCatalogObject($product->getSquareId(), true);
        } catch (\SquareConnect\ApiException $e) {
            $log->error($e->__toString());
            return false;
        }

        $idemPotency = uniqid();
        $catalogObjectArr = array(
            "idempotency_key" => $idemPotency,
            "object" => $mapping->setCatalogObject($product, $receivedObj)
        );

        $productLocationIds = Mage::getModel('squareup_omni/inventory')
            ->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->getColumnValues('location_id');

        $catalogObjectArr['object']['present_at_location_ids'] = $productLocationIds;

        foreach ($catalogObjectArr['object']['item_data']['variations'] as &$variation) {
            if ($product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                if ($variation['item_variation_data']['sku'] === Mage::getModel('catalog/product')->load($productId)->getSku()) { // change only selected variation
                    $variation['present_at_location_ids'] = $productLocationIds;
                }
            } else {
                $variation['present_at_location_ids'] = $productLocationIds;
            }
        }

        $catalogObjectRequest = new \SquareConnect\Model\UpsertCatalogObjectRequest($catalogObjectArr);

        try {
            $apiResponse = $catalogApi->upsertCatalogObject($catalogObjectRequest);
        } catch (\SquareConnect\ApiException $e) {
            $log->error($e->__toString());
            return $this;
        }

        if (null !== $apiResponse->getErrors()) {
            $log->error(
                'There was an error in the response, when calling UpsertCatalogObject' . __FILE__ . __LINE__
            );
        }
    }

    public function revokeAction()
    {
        $applicationId = Mage::helper('squareup_omni/config')->getApplicationId();
        $applicationSecret = Mage::helper('squareup_omni/config')->getApplicationSecret();
        $url = 'https://connect.squareup.com/oauth2/revoke';
        $token = Mage::helper('squareup_omni/config')->getOAuthToken();

        if (null === $token) {
            return $this->getResponse()->clearHeaders()->setHttpResponseCode(500)->setBody('Error');
        }

        $config = array(
            'adapter'   => 'Zend_Http_Client_Adapter_Socket',
        );
        $oauthRequestHeaders = array (
            'Authorization' => 'Client ' . $applicationSecret,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        );
        $oauthRequestBody = array(
            'client_id' => $applicationId,
            'access_token' => $token,
        );

        try {
            $client = new Zend_Http_Client($url, $config);
            $client->setMethod(Zend_Http_Client::POST);
            $client->setConfig(array('timeout' => 60));
            $client->setHeaders($oauthRequestHeaders);
            $client->setRawData(json_encode($oauthRequestBody));
            $response = $client->request();
        } catch (Exception $e) {
            Mage::helper('squareup_omni/log')->error($e->__toString());
            return $this->getResponse()->clearHeaders()->setHttpResponseCode(500)->setBody('Error');
        }

        $responseObject = json_decode($response->getBody());
        if ($response->getStatus() != 200) {
            Mage::helper('squareup_omni/log')->error($responseObject);
            if ('not_found' === $responseObject->type) {
                Mage::helper('squareup_omni/config')->saveOauthToken();
                Mage::helper('squareup_omni/config')->saveOauthExpire();
                Mage::app()->getConfig()->reinit();
                return $this->getResponse()->setBody(json_encode(array('success' => true)));
            }

            return $this->getResponse()->clearHeaders()->setHttpResponseCode(500)->setBody('Error');
        }

        if ($responseObject->success == true) {
            Mage::helper('squareup_omni/config')->saveOauthToken();
            Mage::helper('squareup_omni/config')->saveOauthExpire();
            Mage::app()->getConfig()->reinit();
            return $this->getResponse()->setBody(json_encode(array('success' => true)));
        } else {
            Mage::helper('squareup_omni/log')->error($responseObject);
            return $this->getResponse()->clearHeaders()->setHttpResponseCode(500)->setBody('Error');
        }
    }

    public function subscribeAction()
    {
        $subscribe = Mage::helper('squareup_omni')->subscribeWebhook();
        if (true === $subscribe) {
            return $this->getResponse()->setBody(json_encode(array('success' => true)));
        } else {
            return $this->getResponse()->clearHeaders()->setHttpResponseCode(500)->setBody('Error');
        }
    }
}