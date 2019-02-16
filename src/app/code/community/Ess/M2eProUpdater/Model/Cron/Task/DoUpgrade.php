<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2eProUpdater_Model_Cron_Task_DoUpgrade extends Ess_M2eProUpdater_Model_Cron_Task_Abstract
{
    const NICK = 'doUpgrade';

    const LOG_FILE_NAME_MASK = 'upgrade-to-version-%ver%.log';

    /** @var Zend_Log */
    private $logger;
    protected $logFileName;

    //########################################

    protected function performActions()
    {
        try {

            $this->initializeLogger();

            /** @var Ess_M2eProUpdater_Model_Updater $updater */
            $updater = Mage::getModel('M2eProUpdater/Updater');

            if (!$updater->validate() ||
                !$updater->prepareNewPackage()) {

                $this->logger->log($updater->getException()->__toString(), Zend_Log::ERR);
                return true;
            }

            $this->setMaintenance();
            $updater->updatePackage();

            /** @var Ess_M2eProUpdater_Helper_Config $configHelper */
            $configHelper = Mage::helper('M2eProUpdater/Config');
            $configHelper->setUpgradeAllowed('0');

            $this->clearCache();
            $this->unsetMaintenance();

        } catch (\Exception $exception) {
            $this->logger->log($exception->__toString(), Zend_Log::ERR);
        }

        return true;
    }

    protected function isPossibleToRun()
    {
        if (!parent::isPossibleToRun()) {
            return false;
        }

        /** @var Ess_M2eProUpdater_Helper_Config $configHelper */
        $configHelper = Mage::helper('M2eProUpdater/Config');
        if (!$configHelper->isUpgradeAllowed()) {
            return false;
        }

        return true;
    }

    //########################################

    private function initializeLogger()
    {
        /** @var Ess_M2eProUpdater_Helper_M2ePro $m2eProHelper */
        $m2eProHelper = Mage::helper('M2eProUpdater/M2ePro');
        $latestVersion = $m2eProHelper->getLatestAvailableVersion();

        $fileName = self::LOG_FILE_NAME_MASK;
        $latestVersion && $fileName = str_replace('%ver%', $latestVersion, $fileName);

        $this->logFileName = $fileName;

        $this->clearPreviousLogs();

        $this->logger = Mage::getModel('M2eProUpdater/LoggerFactory')->create($this->logFileName);
    }

    //########################################

    private function clearPreviousLogs()
    {
        /** @var Ess_M2eProUpdater_Helper_Module $moduleHelper */
        $moduleHelper = Mage::helper('M2eProUpdater/Module');

        $path = $moduleHelper->getLogDirectoryPath() .DS. $this->logFileName;
        is_file($path) && unlink($path);
    }

    private function clearCache()
    {
        Mage::app()->cleanCache();
        Mage::app()->getConfig()->reinit();

        Mage_Core_Model_Resource_Setup::applyAllUpdates();
        Mage_Core_Model_Resource_Setup::applyAllDataUpdates();
    }

    private function setMaintenance()
    {
        $maintenanceFile = Mage::getBaseDir() .DS. 'maintenance.flag';
        !is_file($maintenanceFile) && file_put_contents($maintenanceFile, '1');
    }

    private function unsetMaintenance()
    {
        $maintenanceFile = Mage::getBaseDir() .DS. 'maintenance.flag';
        is_file($maintenanceFile) && unlink($maintenanceFile);
    }

    //########################################
}