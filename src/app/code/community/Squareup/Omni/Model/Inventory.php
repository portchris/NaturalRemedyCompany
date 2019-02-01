<?php
/**
 * SquareUp
 *
 * Inventory Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Inventory extends Squareup_Omni_Model_Square
{
    /**
     * Init class
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('squareup_omni/inventory');
    }

    public function start()
    {
        if (Squareup_Omni_Model_System_Config_Source_Options_Records::SQUARE == $this->_config->getSor()) {
            Mage::getModel('squareup_omni/inventory_import')->start();
        } else {
            Mage::getModel('squareup_omni/inventory_export')->start();
        }

        return true;
    }

}

/* Filename: Inventory.php */
/* Location: app/code/community/Squareup/Omni/Model/Inventory.php */