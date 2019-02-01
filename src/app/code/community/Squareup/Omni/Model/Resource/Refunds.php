<?php
/**
 * SquareUp
 *
 * Refunds Resource Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Resource_Refunds extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('squareup_omni/refunds', 'id');
    }

    public function emptyRefunds()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        Mage::getConfig()->saveConfig('squareup_omni/refunds/begin_time', null);
        Mage::app()->getConfig()->reinit();
        return $connection->truncateTable(Mage::getSingleton('core/resource')->getTableName('squareup_omni/refunds'));
    }
}