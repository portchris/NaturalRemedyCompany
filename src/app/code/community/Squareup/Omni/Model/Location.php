<?php
/**
 * Location Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Location extends Mage_Core_Model_Abstract
{
    /**
    * Init class
    */
    public function _construct()
    {
        parent::_construct();
        $this->_init('squareup_omni/location');
    }
}