<?php
/**
 * Overwrite SquareUp Helper Data to return EXPECTED_INTEGER as API requests
 *
 * Data Helper
 *
 * @category    Squareup
 * @package     NaturalRemedyCo, Squareup_Omni
 * @copyright   2019
 * @author      Chris Rogers
 */
class NaturalRemedyCo_Squareup_Helper_Data extends Squareup_Omni_Helper_Data
{


    public function processAmount($amount, $currency = "USD")
    {
        return (int)ceil($amount * 100);
    }
}
