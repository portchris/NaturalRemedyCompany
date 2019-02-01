<?php
/**
 * SquareUp
 *
 * Index Controller
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_IndexController extends Mage_Core_Controller_Front_Action
{
    public function testAction()
    {
        return $this->getResponse()->setBody(json_encode('Cron executed'));
    }

    public function callbackAction()
    {
        $authorizationCode = $this->getRequest()->getParam('code');
        $state = $this->getRequest()->getParam('state');
        $fh = new SplFileObject(Mage::getBaseDir('var') . '/onlytoken.txt', 'r');
        $storedState = $fh->fgets();
        $fh = null;
        if ($storedState !== $state) {
            return $this->getResponse()
                ->setBody('There was an error with state, please try again!');
        }

        $applicationId = Mage::helper('squareup_omni/config')->getApplicationId();
        $applicationSecret = Mage::helper('squareup_omni/config')->getApplicationSecret();
        $url = 'https://connect.squareup.com/oauth2/token';

        if (null === $authorizationCode) {
            return $this->getResponse()
                ->setBody('There was an error please check Application Key and Application Secret and try again!');
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
            'client_secret' => $applicationSecret,
            'code' => $authorizationCode
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
            return $this->getResponse()
                ->setBody(
                    'There was an error in retrieving the access token, please check Application Key 
                    and Application Secret and try again!'
                );
        }

        if ($response->getStatus() != 200) {
            Mage::helper('squareup_omni/log')->error($response->__toString());
            return $this->getResponse()
                ->setBody(
                    'There was an error in retrieving the access token, please check Application Key 
                    and Application Secret and try again!'
                );
        }

        $responseObj = json_decode($response->getBody());
        Mage::getConfig()->saveConfig('squareup_omni/oauth_settings/oauth_token', $responseObj->access_token);
        Mage::getConfig()->saveConfig('squareup_omni/oauth_settings/oauth_expire', strtotime($responseObj->expires_at));
        /* force config reload in order to have access to the token */
        Mage::app()->getConfig()->reinit();

        /* Update application location */
        Mage::getModel('squareup_omni/location_import')->updateLocations();

        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveLocationAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*');
            return;
        }

        $config = Mage::helper('squareup_omni/config');
        $cookies = Mage::getModel('core/cookie')->get();
        $cookiesNames = array_keys($cookies);
        if (!in_array('adminhtml', $cookiesNames)) {
            $this->_redirect('*/*');
            return;
        }

        $params = $this->getRequest()->getParams();

        try {
            Mage::getConfig()->saveConfig('squareup_omni/general/location_id', $params['location']);
            Mage::app()->getConfig()->reinit();
            $oauthToken = $config->getOAuthToken();
            if (!empty($oauthToken)) {
//                $config->syncLocationInventory();
            }
        } catch (Exception $e) {
            Mage::helper('squareup_omni/log')->error($e->__toString());
            return $this->getResponse()
                ->setBody("There was an error saving the location please do it manually in admin configuration screen");
        }

        return $this->getResponse()->setBody("<h1>You can close this window</h1>");

    }

    public function saveNonceAction()
    {
        // need to save nonce?
        return $this->getResponse()->setBody('nonce');
    }

    /**
     * Update location inventory
     */
    public function updateInventoryAction()
    {
        if (!Mage::helper('squareup_omni/config')->getSor()) {
            return null;
        }

        $params = $this->getRequest()->getParams();

        try {
            $product = Mage::getModel('catalog/product')
                ->load($params['productId']);

            if (!$product->getWebsiteIds()) {
                $this->getResponse()->setHttpResponseCode(400);
                return;
            }

            $location = Mage::getModel('squareup_omni/location')->getCollection()
                ->addFieldToFilter('square_id', array('eq' => $params['location_id']))
                ->addFieldToSelect('square_id');

            $locationId = $location->getFirstItem()->getSquareId();
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($params['productId']);
            $stockItem->setQty($params['quantity']);
            $stockItem->setIsInStock((int)($params['quantity'] > 0));
            $stockItem->save();

            $inventory = Mage::getModel('squareup_omni/inventory')
                ->getCollection()
                ->addFieldToFilter('location_id', array('eq' => $locationId))
                ->addFieldToFilter('product_id', array('eq' => $params['productId']));


            $inventory->getFirstItem()->setQuantity($params['quantity']);
            $inventory->save();


            Mage::getModel('squareup_omni/inventory_export')->start(
                array($params['productId']), $locationId, $params['quantity']
            );
            $this->getResponse()->setHttpResponseCode(200);
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(500);
        }
    }
}

/* Filename: IndexController.php */
/* Location: app/code/community/Squareup/Omni/controllers/IndexController.php */