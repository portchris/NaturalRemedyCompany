<?php
/**
 * SquareUp
 *
 * Images Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Images extends Squareup_Omni_Model_Square
{
    public function start()
    {

        if (Squareup_Omni_Model_System_Config_Source_Options_Records::SQUARE == $this->_config->getSor()) {
            return false;
        }

        if (false === $this->_config->getEnableImages() || false === $this->_config->isCatalogEnabled()) {
            return false;
        }

        Mage::getModel('squareup_omni/catalog_images')->start();

        return true;
    }
}

/* Filename: Images.php */
/* Location: app/code/community/Squareup/Omni/Model/Images.php */