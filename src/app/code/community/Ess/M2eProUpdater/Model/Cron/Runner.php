<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Model_Cron_Runner extends Ess_M2eProUpdater_Model_Abstract
{
    /** @var Zend_Log */
    private $logger;

    //########################################

    public function __construct()
    {
        parent::__construct();
        $this->logger = Mage::getModel('M2eProUpdater/LoggerFactory')->create();
    }

    //########################################

    public function process()
    {
        /** @var Ess_M2eProUpdater_Helper_Cron $helper */
        $helper = Mage::helper('M2eProUpdater/Cron');

        if ($helper->isLocked()) {
            return false;
        }

        $this->initialize();
        $this->updateLastRun();

        try {

            $result = $this->processTasks();

        } catch (\Exception $exception) {

            $result = false;
            $this->logger->log($exception->__toString(), Zend_Log::ERR);
        }

        $this->deInitialize();

        return $result;
    }

    //########################################

    protected function initialize()
    {
        /** @var Ess_M2eProUpdater_Helper_Cron $helper */
        $helper = Mage::helper('M2eProUpdater/Cron');
        $helper->lock();
    }

    protected function deInitialize()
    {
        /** @var Ess_M2eProUpdater_Helper_Cron $helper */
        $helper = Mage::helper('M2eProUpdater/Cron');
        $helper->unlock();
    }

    protected function updateLastRun()
    {
        /** @var Ess_M2eProUpdater_Helper_Cron $helper */
        $helper = Mage::helper('M2eProUpdater/Cron');
        $helper->setLastRun();
    }

    //########################################

    protected function processTasks()
    {
        $result = true;

        foreach ($this->getTasksList() as $taskNick) {

            try {

                /** @var Ess_M2eProUpdater_Model_Cron_Task_Abstract $task */
                $task = $this->getTaskObject($taskNick);
                $tempResult = $task->process();

                if (!is_null($tempResult) && !$tempResult) {
                    $result = false;
                }

            } catch (\Exception $exception) {

                $result = false;
                $this->logger->log($exception->__toString(), Zend_Log::ERR);
            }
        }

        return $result;
    }

    //########################################

    protected function getTasksList()
    {
        return array(
            Ess_M2eProUpdater_Model_Cron_Task_DoUpgrade::NICK,
        );
    }

    protected function getTaskObject($taskNick)
    {
        $taskNick = str_replace('_', ' ', $taskNick);
        $taskNick = str_replace(' ', '', ucwords($taskNick));

        /** @var $task Ess_M2eProUpdater_Model_Cron_Task_Abstract **/
        $task = Mage::getModel('M2eProUpdater/Cron_Task_'.trim($taskNick));

        return $task;
    }

    //########################################
}