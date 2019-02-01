<?php
/**
 * SquareUp
 *
 * Action Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */

class Squareup_Omni_Model_System_Config_Source_Options_Action
{
    /**
     * Returning the available Payment Action
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE,
                'label' => 'Authorize Only'
            ),
            array(
                'value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => 'Authorize and Capture'
            )
        );
    }
}

/* Filename: Action.php */
/* Location: app/code/community/Squareup/Omni/Model/System/Config/Source/Options/Action.php */