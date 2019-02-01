<?php
/**
 * SquareUp
 *
 * Records Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */

class Squareup_Omni_Model_System_Config_Source_Options_Records
{
    const SQUARE = 0;
    const Magento = 1;

    /**
     * Returning the available Square Up Source of Records
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::SQUARE,
                'label' => 'Square'
            ),
            array(
                'value' => self::Magento,
                'label' => 'Magento'
            )
        );
    }
}

/* Filename: Records.php */
/* Location: app/code/community/Squareup/Omni/Model/System/Config/Source/Options/Records.php */