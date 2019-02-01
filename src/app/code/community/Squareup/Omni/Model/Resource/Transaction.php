<?php
/**
 * SquareUp
 *
 * Transaction Resource Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Resource_Transaction extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('squareup_omni/transaction', 'id');
    }

    public function emptyTransactions()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        Mage::getConfig()->saveConfig('squareup_omni/transactions/begin_time', null);
        Mage::app()->getConfig()->reinit();
        return $connection->truncateTable(
            Mage::getSingleton('core/resource')->getTableName('squareup_omni/transaction')
        );
    }

    public function transactionExists($locationId, $transactionId)
    {
        $coreResource = Mage::getSingleton('core/resource');
        $conn = $coreResource->getConnection('core_read');
        $select = $conn->select('id')
            ->from(
                array(
                    'p' => Mage::getSingleton('core/resource')->getTableName('squareup_omni/transaction')
                ),
                new Zend_Db_Expr('id')
            )
            ->where('square_id = ?', $transactionId)
            ->where('location_id = ?', $locationId);
        $id = $conn->fetchOne($select);

        return $id;
    }

    public function loadNoteBySquareId($squareId)
    {
        $coreResource = Mage::getSingleton('core/resource');
        $conn = $coreResource->getConnection('core_read');
        $select = $conn->select('note')
            ->from(
                array(
                    'p' => Mage::getSingleton('core/resource')->getTableName('squareup_omni/transaction')
                ),
                new Zend_Db_Expr('note')
            )
            ->where('square_id = ?', $squareId);
        $id = $conn->fetchOne($select);

        return $id;
    }
}