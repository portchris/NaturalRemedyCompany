<?php
/**
 * SquareUp
 *
 * Mode Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */

class Squareup_Omni_Model_System_Config_Source_Options_Mode
{
    const PRODUCTION_ENV = 'prod';
    const SANDBOX_ENV = 'sandbox';

    /**
     * Returning the available Payment Mode
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::PRODUCTION_ENV,
                'label' => 'Production'
            ),
            array(
                'value' => self::SANDBOX_ENV,
                'label' => 'Sandbox'
            )
        );
    }
}

/* Filename: Mode.php */
/* Location: app/code/community/Squareup/Omni/Model/System/Config/Source/Options/Mode.php */