<?php
/**
 * SquareUp
 *
 * Location Resource Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Resource_Location extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('squareup_omni/location', 'id');
    }

    public function emptyLocations()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        return $connection->truncateTable(Mage::getSingleton('core/resource')->getTableName('squareup_omni/location'));
    }
}