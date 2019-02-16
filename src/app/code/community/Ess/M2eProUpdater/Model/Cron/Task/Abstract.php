<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2eProUpdater_Model_Cron_Task_Abstract extends Ess_M2eProUpdater_Model_Abstract
{
    //########################################

    public function process()
    {
        if (!$this->isPossibleToRun()) {
            return true;
        }

        $tempResult = $this->performActions();

        if (!is_null($tempResult) && !$tempResult) {
            $tempResult = false;
        }

        return $tempResult;
    }

    // ---------------------------------------

    protected function isPossibleToRun()
    {
        return true;
    }

    // ---------------------------------------

    abstract protected function performActions();

    //########################################
}