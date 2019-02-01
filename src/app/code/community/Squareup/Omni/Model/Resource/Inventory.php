<?php
/**
 * SquareUp
 *
 * Inventory Resource Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Resource_Inventory extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('squareup_omni/square_inventory', 'entity_id');
    }

    public function loadByProductIdAndLocationId($productId, $locationId)
    {
        $inventory = Mage::getModel('squareup_omni/inventory');
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable())->where('product_id = ?', (int)$productId);
        $select->where('location_id = ?', $locationId);

        $data = $read->fetchRow($select);
        if (!$data) {
            return null;
        }

        $inventory->setData($data);

        return $inventory;
    }

    public function emptyInventory()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        return $connection->truncateTable(
            Mage::getSingleton('core/resource')->getTableName('squareup_omni/square_inventory')
        );
    }

}

/* Filename: Inventory.php */
/* Location: app/code/community/Squareup/Omni/Model/Resource/Inventory.php */