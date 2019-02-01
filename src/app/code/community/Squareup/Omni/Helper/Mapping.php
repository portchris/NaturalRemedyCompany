<?php
/**
 * SquareUp
 *
 * Mapping Helper
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Helper_Mapping extends Mage_Core_Helper_Abstract
{
    protected $_log;
    protected $_config;
    protected $_helper;
    protected $_itemMapping = array(
        'name' => 'name',
        'description' => 'description',
        'abbreviation',
        'label_color',
        'available_online', // if not available online fi
        'available_electronically', // type download or virtual
        'tax_ids' => 'tax_class_id',
        'image_url' => 'image',
        'sku' => 'sku',

    );

    public function __construct()
    {
        $this->_log = Mage::helper('squareup_omni/log');
        $this->_config = Mage::helper('squareup_omni/config');
        $this->_helper = Mage::helper('squareup_omni');
    }

    public function getMapping($type)
    {
        return $this->{'_' . $type . 'Mapping'};
    }

    public function setCatalogObject($product, $receivedObj = null)
    {
        $version = (null !== $receivedObj)? $receivedObj->getObject()->getVersion() : null;
        $productSquareId = $product->getSquareId();
        $image = $product->getResource()->getAttributeRawValue($product->getId(), 'square_product_image', 0);
        $catalogObject = array(
            "type" => "ITEM",
            "id" => (empty($productSquareId))? '#' . $product->getId() : $product->getSquareId(),
            "version" => $version,
            // modified here
            "present_at_all_locations" => ($product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
                                        ? true : false,
            "present_at_location_ids" => $this->_helper->getProductLocations($product->getId()),
            "absent_at_location_ids" => array(),
            "item_data" => array(
                "name" => $product->getName(),
                "description" => $product->getShortDescription(),
                "abbreviation" => substr($product->getName(), 0, 2),
                "available_online" => true,
                "available_for_pickup" => false,
                "tax_ids" => array(),
                "modifier_list_info" => array(),
                "available_electronically" => (
                    $product->getTypeId() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
                )? true : false,
                "image_url" => $image ? $image : '',
                "variations" => $this->setItemVariation($product, $receivedObj)
            )
        );

        return $catalogObject;
    }

    public function setItemVariation($product, $receivedObj = null)
    {
        $variations = array();
        $versions = $this->getVersions($receivedObj);
        $productVariations = array();
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
            $productVariations[] = $product;
        }

        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL) {
            $productVariations[] = $product;
        }

        if ($product->getTypeId() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
            $productVariations[] = $product;
        }

        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $conf = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
            $collection = $conf->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();
            foreach ($collection as $aProduct) {
                $aProduct->setConfProductId($product->getId());
                $productVariations[] = $aProduct;
            }
        }

        foreach ($productVariations as $productVar) {
            $variationId = $productVar->getSquareVariationId();
            $version = (empty($variationId))?
                null :
                (array_key_exists($productVar->getSquareVariationId(), $versions))?
                    $versions[$productVar->getSquareVariationId()] :
                    null;
            $id = (empty($variationId))?
                '#' . $productVar->getId() . '::' :
                $productVar->getSquareVariationId();

            if ($productVar->getSquareVariationId() != $productVar->getSquareId()
                && null !== $productVar->getConfProductId()) {
                $version = null;
                $id = '#' . $productVar->getId() . '::';
            }

            $variation = array(
                "id" => $id,
                "type" => 'ITEM_VARIATION',
                "version" => $version,
                "present_at_all_locations" => false,
                "present_at_location_ids" => $this->_helper->getProductLocations($productVar->getId()),
                "absent_at_location_ids" => array(),
                "item_variation_data" => array(
                    "sku" => $productVar->getSku(),
                    "name" => $productVar->getName(),
                    "track_inventory" => true,
                    "pricing_type" => "FIXED_PRICING",
                    "price_money" => array(
                        "amount" => Mage::helper('squareup_omni')->processAmount($productVar->getPrice()),
                        "currency" => Mage::app()->getStore()->getCurrentCurrencyCode()
                    )
                )
            );
            $variations[] = $variation;
        }

        return $variations;
    }

    public function getVersions($obj)
    {
        $versions = array();
        if (null == $obj) {
            return $versions;
        }

        $obj = $obj->getObject();
        $versions[$obj->getId()] = $obj->getVersion();
        // modified here
        if (null !== $obj->getItemData() && null !== $obj->getItemData()->getVariations()) {
            foreach ($obj->getItemData()->getVariations() as $variation) {
                $versions[$variation->getId()] = $variation->getVersion();
            }
        }

        return $versions;
    }

    public function isChild($id)
    {
        $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($id);
        return (!empty($parentIds))? $parentIds : false;
    }

    public function saveSquareIdsInMagento($idMappings)
    {
        $itemIds = array();
        $varIds = array();
        foreach ($idMappings as $map) {
            if (stripos($map->getClientObjectId(), "::") !== false) {
                $idWithoutSharp = str_replace("#", "", $map->getClientObjectId());
                $mId = str_replace("::", "", $idWithoutSharp);
                $varIds[$map->getObjectId()] = $mId;
            } else {
                $mId = str_replace("#", "", $map->getClientObjectId());
                $itemIds[$map->getObjectId()] = $mId;
            }
        }

        foreach ($itemIds as $squareId => $mId) {
            $product = Mage::getModel('catalog/product');

            try {
                $product->setId($mId);
                $product->setStoreId(0);
                $product->setSquareId($map->getObjectId());
                $product->setSquareVariationId($map->getObjectId());
                $product->getResource()->saveAttribute($product, 'square_id');
                $product->getResource()->saveAttribute($product, 'square_variation_id');
            } catch (Exception $e) {
                $this->_log->error($e->__toString());
            }
        }

        foreach ($varIds as $squareId => $mId) {
            $isChild = $this->isChild($mId);
            $product = Mage::getModel('catalog/product');
            $product->setId($mId);

            try {
                $product->setStoreId(0);
                if ($isChild !==  false) {
                    $product->setSquareId($squareId);
                }

                $product->setSquareVariationId($squareId);
                if ($isChild !==  false) {
                    $product->getResource()->saveAttribute($product, 'square_id');
                }

                $product->getResource()->saveAttribute($product, 'square_variation_id');
            } catch (Exception $e) {
                $this->_log->error($e->__toString());
            }
        }

        return true;
    }
}

/* Filename: Mapping.php */
/* Location: app/code/community/Squareup/Omni/Helper/Mapping.php */