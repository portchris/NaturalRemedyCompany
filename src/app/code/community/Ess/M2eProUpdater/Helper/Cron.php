<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Helper_Cron extends Mage_Core_Helper_Abstract
{
    const STATUS_FILE_NAME = '.cron_status';
    const LOCK_FILE_NAME   = '.cron_lock';

    const LOCK_FILE_LIFETIME = 600;

    //########################################

    public function isInstalled()
    {
        return $this->getLastRun() !== null;
    }

    public function isLastRunMoreThan($interval, $isHours = false)
    {
        $isHours && $interval *= 3600;

        $lastRun = $this->getLastRun();
        if (is_null($lastRun)) {
            return false;
        }

        $date = new \DateTime('now', new \DateTimeZone('UTC'));
        return $date->getTimestamp() > strtotime($lastRun) + $interval;
    }

    //########################################

    public function getLastRun()
    {
        $path = $this->getLockDirectoryPath() .DS. self::STATUS_FILE_NAME;

        if (is_file($path)) {
            return file_get_contents($path);
        }

        return null;
    }

    public function setLastRun($value = null)
    {
        if (is_null($value)) {
            $value = new \DateTime('now', new \DateTimeZone('UTC'));
            $value = $value->format('Y-m-d H:i:s');
        }

        $path = $this->getLockDirectoryPath() .DS. self::STATUS_FILE_NAME;
        file_put_contents($path, $value);
    }

    //########################################

    public function lock()
    {
        $path = $this->getLockDirectoryPath() .DS. self::LOCK_FILE_NAME;
        file_put_contents($path, getmygid());
    }

    public function unlock()
    {
        $path = $this->getLockDirectoryPath() .DS. self::LOCK_FILE_NAME;
        is_file($path) && unlink($path);
    }

    public function isLocked()
    {
        $path = $this->getLockDirectoryPath() .DS. self::LOCK_FILE_NAME;

        if (!is_file($path)) {
            return false;
        }

        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $mTime = filemtime($path);

        if ($now->getTimestamp() > $mTime + self::LOCK_FILE_LIFETIME) {
            unlink($path);
            return false;
        }

        return true;
    }

    //########################################

    protected function getLockDirectoryPath()
    {
        $path = Mage::getBaseDir('var') .DS. Ess_M2eProUpdater_Helper_Module::IDENTIFIER;

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    //########################################
}