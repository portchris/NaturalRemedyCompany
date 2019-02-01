<?php
/**
 * SquareUp
 *
 * Card on file Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */

class Squareup_Omni_Model_System_Config_Source_Options_Cardonfile
{
    /**
     * Returning the available Payment Action
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Squareup_Omni_Model_Card::DISALLOW_CARD_ON_FILE,
                'label' => 'Don\'t allow card on file payments'
            ),
            array(
                'value' => Squareup_Omni_Model_Card::ALLOW_CARD_ON_FILE,
                'label' => 'Allow credit card payments and card on file payments'
            ),
            array(
                'value' => Squareup_Omni_Model_Card::ALLOW_ONLY_CARD_ON_FILE,
                'label' => 'Allow only card on file payments'
            ),
        );
    }
}

/* Filename: Action.php */
/* Location: app/code/community/Squareup/Omni/Model/System/Config/Source/Options/Action.php */