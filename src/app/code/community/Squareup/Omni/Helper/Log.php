<?php
/**
 * SquareUp
 *
 * Log Helper
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Helper_Log extends Mage_Core_Helper_Abstract
{
    protected $_logName = 'squareup_omni.log';

    public function info($msg)
    {
        Mage::log($msg, Zend_Log::INFO, $this->_logName);
    }

    public function error($msg)
    {
        Mage::log($msg, Zend_Log::ERR, $this->_logName);
    }

    public function warning($msg)
    {
        Mage::log($msg, Zend_Log::WARN, $this->_logName);
    }

    public function debug($msg)
    {
        Mage::log($msg, Zend_Log::DEBUG, $this->_logName);
    }
}

/* Filename: Log.php */
/* Location: app/code/community/Squareup/Omni/Helper/Log.php */