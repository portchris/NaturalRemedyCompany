<?php
/**
 * Transaction Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Transaction extends Mage_Core_Model_Abstract
{
    /**
     * Define transaction types
     */
    const TYPE_CARD_VALUE = 0;
    const TYPE_CARD_LABEL = 'CARD';
    const TYPE_CASH_VALUE = 1;
    const TYPE_CASH_LABEL = 'CASH';

    /**
     * Init class
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('squareup_omni/transaction');
    }
}