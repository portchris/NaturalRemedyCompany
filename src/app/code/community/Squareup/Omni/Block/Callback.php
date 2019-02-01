<?php
/**
 * SquareUp
 *
 * Callback Block
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Block_Callback extends Mage_Core_Block_Template
{
    public function getLocations()
    {
        $locations = Mage::getModel('squareup_omni/location')
            ->getCollection()
            ->addFieldToFilter('status', 1);
        return $locations;
    }
}

/* Filename: Callback.php */
/* Location: app/code/community/Squareup/Omni/Block/Callback.php */