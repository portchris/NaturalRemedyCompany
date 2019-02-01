<?php
/**
 * SquareUp
 *
 * Product Resource Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Resource_Product extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        // Required method
    }

    public function productExists($squareId)
    {
        $coreResource = Mage::getSingleton('core/resource');
        $conn = $coreResource->getConnection('core_read');
        $select = $conn->select('entity_id')
            ->from(
                array(
                    'p' => $coreResource->getTableName('catalog_product_entity_varchar')
                ),
                new Zend_Db_Expr('entity_id')
            )
            ->join(
                array(
                    'st' => $coreResource->getTableName('eav/attribute')),
                'st.attribute_id = p.attribute_id',
                array()
            )
            ->where('st.attribute_code = ?', 'square_id')
            ->where('st.entity_type_id = ?', 4)
            ->where('value = ?', $squareId);
        $id = $conn->fetchOne($select);

        return $id;
    }

    public function deleteProducts($ids)
    {
        $coreResource = Mage::getSingleton('core/resource');
        $conn = $coreResource->getConnection('core_write');
        $productEntityTable = $coreResource->getTableName('catalog/product');
        if ($ids) {
            $conn->query(
                $conn->quoteInto(
                    "DELETE FROM `{$productEntityTable}` WHERE `entity_id` IN (?)", $ids
                )
            );
        }

        return true;
    }

    public function resetProducts($ids = null)
    {
        $square = Squareup_Omni_Model_System_Config_Source_Options_Records::SQUARE;

        if (null === $ids) {
            $productCollection = Mage::getResourceModel('catalog/product_collection');
            $productIds = $productCollection->getAllIds();
        } else {
            $productIds = $ids;
        }

        try {
            Mage::getSingleton('catalog/product_action')->updateAttributes(
                $productIds,
                array('square_id' => null),
                Mage_Core_Model_App::ADMIN_STORE_ID
            );
            Mage::getSingleton('catalog/product_action')->updateAttributes(
                $productIds,
                array('square_variation_id' => null),
                Mage_Core_Model_App::ADMIN_STORE_ID
            );

            if (null === $ids && $square == Mage::helper('squareup_omni/config')->getSor()) {
                $this->resetAttributeOptions();
            }
        } catch (Exception $e) {
            Mage::helper('squareup_omni/log')->error($e->__toString());
        }


    }

    public function resetAttributeOptions()
    {
        try {
            $attr = Mage::getModel('eav/config')->getAttribute(
                'catalog_product',
                Squareup_Omni_Model_Square::SQUARE_VARIATION_ATTR
            );
        } catch (Exception $e) {
            Mage::helper('squareup_omni/log')->error($e->__toString());
        }

        $options = $attr->getSource()->getAllOptions();
        array_shift($options);

        foreach ($options as $option) {
            $options['delete'][$option['value']] = true;
            $options['value'][$option['value']] = true;
        }

        $setup = Mage::getModel('eav/entity_setup', 'core_setup');
        $setup->addAttributeOption($options);
    }

    public function skuExists($sku)
    {
        $coreResource = Mage::getSingleton('core/resource');
        $conn = $coreResource->getConnection('core_read');
        $select = $conn->select('entity_id')
            ->from(
                array(
                    'p' => $coreResource->getTableName('catalog_product_entity')
                ),
                new Zend_Db_Expr('entity_id')
            )
            ->where('sku = ?', $sku);
        $id = $conn->fetchOne($select);

        return $id;
    }


    public function getProductLocations($productId)
    {
        $coreResource = Mage::getSingleton('core/resource');
        $conn = $coreResource->getConnection('core_read');
        $select = $conn->select('location_id')
            ->from(
                array(
                    'p' => $coreResource->getTableName('squareup_omni/square_inventory')
                ),
                new Zend_Db_Expr('location_id')
            )
            ->where('product_id = ?', $productId);
        $ids = $conn->fetchCol($select);

        return $ids;
    }
}

/* Filename: Product.php */
/* Location: app/code/community/Squareup/Omni/Model/Resource/Product.php */