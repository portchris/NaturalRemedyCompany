<?php
/**
 * SquareUp
 *
 * Catalog Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Catalog extends Squareup_Omni_Model_Square
{
    public function start()
    {
        if (Squareup_Omni_Model_System_Config_Source_Options_Records::SQUARE == $this->_config->getSor()) {
            Mage::getModel('squareup_omni/catalog_import')->start();
        } else {
            Mage::getModel('squareup_omni/catalog_export')->start();
        }

        return true;
    }

    public function productExists($id)
    {
        return Mage::getResourceModel('squareup_omni/product')->productExists($id);
    }
}

/* Filename: Catalog.php */
/* Location: app/code/community/Squareup/Omni/Model/Catalog.php */