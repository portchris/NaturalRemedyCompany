<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Helper_Data_GlobalData extends Mage_Core_Helper_Abstract
{
    //########################################

    public function getValue($key)
    {
        $globalKey = Ess_M2eProUpdater_Helper_Module::IDENTIFIER .'_'. $key;
        return Mage::registry($globalKey);
    }

    public function setValue($key, $value)
    {
        $globalKey = Ess_M2eProUpdater_Helper_Module::IDENTIFIER .'_'. $key;
        Mage::register($globalKey,$value,true);
    }

    //########################################

    public function unsetValue($key)
    {
        $globalKey = Ess_M2eProUpdater_Helper_Module::IDENTIFIER .'_'. $key;
        Mage::unregister($globalKey);
    }

    //########################################
}