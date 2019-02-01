<?php
/**
 * SquareUp
 *
 * Collection Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Resource_Inventory_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('squareup_omni/inventory');
    }
}

/* Filename: Inventory.php */
/* Location: app/code/community/Squareup/Omni/Model/Resource/Inventory/Collection.php */