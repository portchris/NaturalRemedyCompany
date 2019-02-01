<?php
/**
 * SquareUp
 *
 * Export Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Catalog_Images extends Squareup_Omni_Model_Square
{
    public $squareIds = array();

    public function start()
    {
        if ($this->_config->getSor() == Squareup_Omni_Model_System_Config_Source_Options_Records::SQUARE) {
            $ranAt = Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s');
            $this->imagesImport();
            $this->_config->saveImagesRanAt($ranAt);
        } else {
            $ranAt = Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s');
            $this->imagesExport();
            $this->_config->saveImagesRanAt($ranAt);
        }

        return true;
    }

    public function imagesExport()
    {
        $this->processProducts();
    }

    public function imagesImport()
    {
        $this->getRequiredImages();
        $this->retrieveSquareImages();
    }

    public function getRequiredImages($count = null)
    {
        $size = 0;
        $from = $this->_config->getImagesRanAt();
        if (null === $from) {
            $from = Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s', 1);
        }

        $to = Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s');

        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(
                array(
                    'entity_id',
                    'square_id',
                    'image',
                    'visibility'
                ),
                'left'
            )
            ->addAttributeToFilter('square_id', array('notnull' => true))
            ->addAttributeToFilter('visibility', array('neq' => '1'))
            ->addAttributeToFilter('image', array('null' => true));
        if (null === $count) {
            Mage::getSingleton('core/resource_iterator')->walk(
                $collection->getSelect(), array(array($this, 'getProductSquareId'))
            );
        } else {
            $size += $collection->getSize();
        }

        $collectionUpdated = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(
                array(
                    'entity_id',
                    'square_id',
                    'image',
                    'visibility'
                ),
                'left'
            )
            ->addAttributeToFilter('updated_at', array('from' => $from, 'to' => $to))
            ->addAttributeToFilter('square_id', array('notnull' => true))
            ->addAttributeToFilter('visibility', array('neq' => '1'))
            ->addAttributeToFilter('image', array('notnull' => true));
        if (null === $count) {
            Mage::getSingleton('core/resource_iterator')->walk(
                $collectionUpdated->getSelect(), array(array($this, 'getProductSquareId'))
            );
        } else {
            $size += $collectionUpdated->getSize();
        }

        return $size;
    }

    public function getProductSquareId($row)
    {
        $this->squareIds[] = $row['row']['square_id'];
    }

    public function retrieveSquareImages()
    {
        $client = $this->_helper->getClientApi();
        $catalogApi = new \SquareConnect\Api\CatalogApi($client);
        if (empty($this->squareIds)) {
            $this->_log->info('No products for image download found');
            return true;
        }

        $objectList = array(
            "object_ids" => $this->squareIds,
            'include_related_objects' => false
        );
        $objectIds = new SquareConnect\Model\BatchRetrieveCatalogObjectsRequest($objectList);

        try {
            // Retrieve the objects
            $apiResponse = $catalogApi->BatchRetrieveCatalogObjects($objectIds);
        } catch (\SquareConnect\ApiException $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        if (null !== $apiResponse->getErrors()) {
            $this->_log->error('There was an error in the response from Square, to catalog items');
            return false;
        }

        if (null === $apiResponse->getObjects()) {
            $this->_log->error('There are no items square to import');
            return false;
        }

        foreach ($apiResponse->getObjects() as $item) {
            if ('ITEM' !== $item->getType()) {
                continue;
            }

            $this->_log->info('Start Item ');
            if (null === $item->getItemData()) {
                continue;
            }

            $image = null;
            $image = $item->getItemData()->getImageUrl();
            $this->_log->info('Image: ' . $image);
            if (empty($image)) {
                $this->_log->info('Image empty');
                continue;
            }

            $id = $item->getId();
            $imagePath = null;
            $imagePath = $this->downloadImage($image, $id);
            $mId = Mage::getResourceModel('squareup_omni/product')->productExists($id);
            $product = Mage::getModel('catalog/product')->setId($mId);
            if (false !== $imagePath) {
                try {
                    $gallery = Mage::getModel('catalog/product_attribute_media_api')->items($product->getId());
                    foreach ($gallery as $gImage) {
                        $mediaCatalog = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product';
                        // remove file from Dir
                        $io = new Varien_Io_File();
                        $io->rm($mediaCatalog . $gImage['file']);
                        Mage::getModel('catalog/product_attribute_media_api')->remove(
                            $product->getId(),
                            $gImage['file']
                        );
                    }

                    $product->addImageToMediaGallery(
                        $imagePath,
                        array('image', 'small_image', 'thumbnail'),
                        true,
                        false
                    );
                    $product->save();
                    $this->_log->info('Image was added to product: #' . $mId);
                } catch (Exception $e) {
                    $this->_log->error('Error while image was added to product: #' . $mId);
                    $this->_log->error($e->__toString());
                }
            } else {
                $this->_log->info('Product: #' . $mId . ' does not have an image in square');
            }
        }

        return true;
    }

    public function processProducts()
    {
        $collection = $this->getRequiredCollection();
        $ranAt = $this->_config->getImagesRanAt();
        Mage::getSingleton('core/resource_iterator')->walk(
            $collection->getSelect(), array(array($this, 'processProduct')), array('ranAt'=>$ranAt)
        );
    }

    public function processProduct($args)
    {
        try {
            if ($args['row']['square_id'] == $args['row']['square_variation_id']) {
                $this->_config->saveImagesRanAt($args['row']['updated_at']);
                return array();
            }

            $model = Mage::getResourceModel('catalog/product');
            $image = $model->getAttributeRawValue($args['row']['entity_id'], 'image', 0);
            if (false === $image) {
                $this->_config->saveImagesRanAt($args['row']['updated_at']);
                return array();
            }

            if ('no_selection' === $image) {
                return array();
            }

            $fullImage = Mage::getBaseDir('media') . '/catalog/product' . $image;
            $fileInfo = new SplFileInfo($fullImage);
            if ($args['ranAt'] < $fileInfo->getMTime()) {
                $this->callSquare($args['row']['entity_id'], $args['row']['square_id'], $fullImage);
            }
        } catch (\Exception $exception) {
            Mage::helper('squareup_omni/log')->error($exception->__toString());
        }

        $this->_config->saveImagesRanAt($args['row']['updated_at']);
    }

    public function callSquare($productId, $itemId, $image)
    {
        $locationId = $this->_config->getLocationId();
        $authToken = $this->_config->getOAuthToken();
        $url = 'https://connect.squareup.com/v1/' . $locationId . '/items/' . $itemId . '/image';
        $config = array(
            'adapter'   => 'Zend_Http_Client_Adapter_Socket',
        );
        $oauthRequestHeaders = array (
            'Authorization' => 'Bearer ' . $authToken,
            'Accept' => 'application/json',
            'Content-Type' => 'multipart/form-data'
        );

        try {
            $client = new Zend_Http_Client($url, $config);
            $client->setMethod(Zend_Http_Client::POST);
            $client->setHeaders($oauthRequestHeaders);
            $client->setConfig(array('timeout' => 60));
            $client->setFileUpload($image, 'image_data');
            $response = $client->request();

            if ($response->getStatus() == 200) {
                $body = json_decode($response->getBody());
                $product = Mage::getModel('catalog/product');
                $product->setId($productId);
                $url = explode('=', $body->url);
                $product->setSquareProductImage(ltrim($url[1], '/'));
                $product->getResource()->saveAttribute($product, 'square_product_image');
            }
        } catch (Exception $e) {
            Mage::helper('squareup_omni/log')->error($e->__toString());
            return false;
        }

        if ($response->getStatus() != 200) {
            Mage::helper('squareup_omni/log')->error($response->__toString());
            return false;
        }

        return true;
    }

    public function getRequiredCollection()
    {
        $ranAt = $this->_config->getImagesRanAt();

        if (null === $ranAt) {
            $ranAt = Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s', 1);
        }

        $toDate = Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s');

        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(
                array(
                    'name',
                    'square_id',
                    'sku',
                    'image',
                    'square_variation_id'
                ),
                'left'
            )
            ->addAttributeToFilter('updated_at', array('from' => $ranAt, 'to' => $toDate))
            ->addAttributeToFilter('square_id', array('notnull' => true));
        $collection->getSelect()->reset(Zend_Db_Select::ORDER);
        $collection->getSelect()->order('e.updated_at asc');

        return $collection;
    }

    public function getCollectionSize()
    {
        $size = 0;
        if ($this->_config->getSor() == Squareup_Omni_Model_System_Config_Source_Options_Records::SQUARE) {
            $size = $this->getRequiredImages(true);
        } else {
            $collection = $this->getRequiredCollection();
            $size = $collection->getSize();
        }


        return $size;
    }

    public function downloadImage($image, $id)
    {
        $config = array(
            'adapter'   => 'Zend_Http_Client_Adapter_Curl',
            'curloptions' => array(CURLOPT_SSL_VERIFYPEER => false),
        );

        try {
            $client = new Zend_Http_Client($image, $config);
            $client->setMethod(Zend_Http_Client::GET);
            $client->setConfig(array('timeout' => 60));
            $response = $client->request();
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
            return false;
        }

        if ($response->getStatus() != 200) {
            $this->_log->error('There was an error when trying to download image: ' . $image);
            return false;
        }

        $fileArr = explode('/', $image);
        $fileName = array_pop($fileArr);
        $extArr = explode('.', $fileName);
        $ext = array_pop($extArr);
        $fullPath = Mage::getBaseDir('media') . '/square/' . $id . '.' . $ext;
        $file = new SplFileObject($fullPath, 'w+');
        $file->fwrite($response->getBody());
        $file = null;

        return $fullPath;
    }

}

/* Filename: Export.php */
/* Location: app/code/community/Squareup/Omni/Model/Catalog/Export.php */