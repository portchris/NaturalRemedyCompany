<?php
/**
 * SquareUp
 *
 * Payment Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Transaction_Payment extends Mage_Payment_Model_Method_Abstract
{
    const CODE = 'squareup_transaction_payment';

    /**
     * Payment code name
     *
     * @var string
     */
    protected $_code = 'squareup_transaction_payment';

    protected $_canUseCheckout = false;

    protected $_canUseInternal = false;
}

/* Filename: Payment.php */
/* Location: app/code/community/Squareup/Omni/Model/Payment.php */