<?php
/**
 * SquareUp
 *
 * Square Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Square extends Mage_Core_Model_Abstract
{
    /**
     * @var Squareup_Omni_Helper_Log
     */
    protected $_log;

    /**
     * @var Squareup_Omni_Helper_Config
     */
    protected $_config;

    /**
     * @var Squareup_Omni_Helper_Data
     */
    protected $_helper;

    /**
     * @var Squareup_Omni_Helper_Mapping
     */
    protected $_mapping;
    const SQUARE_VARIATION_ATTR = 'square_variation';

    public function _construct()
    {
        $this->_log = Mage::helper('squareup_omni/log');
        $this->_config = Mage::helper('squareup_omni/config');
        $this->_helper = Mage::helper('squareup_omni');
        $this->_mapping = Mage::helper('squareup_omni/mapping');
    }

    public function init()
    {
        $this->_log = Mage::helper('squareup_omni/log');
        $this->_config = Mage::helper('squareup_omni/config');
        $this->_helper = Mage::helper('squareup_omni');
        $this->_mapping = Mage::helper('squareup_omni/mapping');
        $this->_helper->prepImageDir();
    }

}

/* Filename: Square.php */
/* Location: app/code/community/Squareup/Omni/Model/Square.php */