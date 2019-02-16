<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Model_LoggerFactory extends Ess_M2eProUpdater_Model_Abstract
{
    const LOGFILE_NAME = 'cron-error.log';

    //########################################

    public function create($fileName = self::LOGFILE_NAME)
    {
        /** @var Ess_M2eProUpdater_Helper_Module $helper */
        $helper = Mage::helper('M2eProUpdater/Module');
        $logFilePath = $helper->getLogDirectoryPath() .DS. $fileName;

        $format = '%timestamp% %priorityName% (%priority%): %message%' . PHP_EOL;
        $formatter = new Zend_Log_Formatter_Simple($format);

        $writer = new Zend_Log_Writer_Stream($logFilePath);
        $writer->setFormatter($formatter);

        return new Zend_Log($writer);
    }

    //########################################
}