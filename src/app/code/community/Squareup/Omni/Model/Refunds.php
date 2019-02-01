<?php
/**
 * Refunds Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Refunds extends Mage_Core_Model_Abstract
{
    /**
     * Define refunds statuses
     */
    const STATUS_PENDING_VALUE = 0;
    const STATUS_PENDING_LABEL = 'PENDING';

    const STATUS_APPROVED_VALUE = 1;
    const STATUS_APPROVED_LABEL = 'APPROVED';

    const STATUS_REJECTED_VALUE = 2;
    const STATUS_REJECTED_LABEL = 'REJECTED';

    const STATUS_FAILED_VALUE = 3;
    const STATUS_FAILED_LABEL = 'FAILED';

    /**
     * Init class
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('squareup_omni/refunds');
    }
}