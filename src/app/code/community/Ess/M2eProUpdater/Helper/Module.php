<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Helper_Module extends Mage_Core_Helper_Abstract
{
    const IDENTIFIER = 'Ess_M2eProUpdater';

    const RELEASES_INFO_CACHE_KEY       = '_m2epro_updater_releases_info_cache_';
    const LATEST_RELEASE_INFO_CACHE_KEY = '_m2epro_updater_latest_release_info_cache_';

    //########################################

    public function getCurrentVersion()
    {
        $version = (string)Mage::getConfig()->getNode('modules/' .self::IDENTIFIER. '/version');
        return strtolower($version);
    }

    public function getLatestAvailableVersion($useCache = true)
    {
        $releaseInfo = $this->getLatestAvailableReleaseInfo($useCache);
        return isset($releaseInfo['version']) ? $releaseInfo['version'] : NULL;
    }

    //----------------------------------------

    public function getCurrentReleaseInfo($useCache = true)
    {
        foreach ($this->getReleasesInfo($useCache) as $version => $releaseInfo) {
            if ($this->getCurrentVersion() == $releaseInfo['version']) {
                return $releaseInfo;
            }
        }

        return NULL;
    }

    public function getLatestAvailableReleaseInfo($useCache = true)
    {
        $visibility = $this->getPackageVisibility();
        $cacheKey = self::LATEST_RELEASE_INFO_CACHE_KEY . $visibility;

        /** @var Ess_M2eProUpdater_Helper_Data_Cache_Permanent $helper */
        $helper = Mage::helper('M2eProUpdater/Data_Cache_Permanent');

        $info = NULL;
        if ($useCache && ($info = $helper->getValue($cacheKey))) {
            return $info;
        }

        /** @var Ess_M2eProUpdater_Model_Downloader $downloader */
        $downloader = Mage::getModel('M2eProUpdater/Downloader', array(
            'visibility'     => $visibility,
            'extension_code' => self::IDENTIFIER
        ));

        if ($info = $downloader->getReleaseInfo(NULL)) {
            $helper->setValue($cacheKey, $info, array(), 60*60);
        }

        return $info;
    }

    //----------------------------------------

    public function getReleasesInfo($useCache = true)
    {
        /** @var Ess_M2eProUpdater_Helper_Data_Cache_Permanent $helper */
        $helper = Mage::helper('M2eProUpdater/Data_Cache_Permanent');

        $releasesInfo = NULL;
        if ($useCache && ($releasesInfo = $helper->getValue(self::RELEASES_INFO_CACHE_KEY))) {
            return $releasesInfo;
        }

        /** @var Ess_M2eProUpdater_Model_Downloader $downloader */
        $downloader = Mage::getModel('M2eProUpdater/Downloader', array(
            'visibility'     => NULL,
            'extension_code' => self::IDENTIFIER
        ));

        if ($releasesInfo = $downloader->getReleaseInfo('*')) {
            $helper->setValue(self::RELEASES_INFO_CACHE_KEY, $releasesInfo, array(), 60*60);
        }

        return $releasesInfo;
    }

    public function getPackageVisibility()
    {
        $currentRelease = $this->getCurrentReleaseInfo();
        if (isset($currentRelease['visibility']) &&
            $currentRelease['visibility'] != Ess_M2eProUpdater_Model_Downloader::VISIBILITY_PUBLIC)
        {
            return $currentRelease['visibility'];
        }

        /** @var Ess_M2eProUpdater_Helper_Config $config */
        $config = Mage::helper('M2eProUpdater/Config');

        return $config->isUpdaterMinimumStabilityBetaTesters() ? Ess_M2eProUpdater_Model_Downloader::VISIBILITY_BETA
                                                               : Ess_M2eProUpdater_Model_Downloader::VISIBILITY_PUBLIC;
    }

    //########################################

    public function clearCachedReleasesInfo()
    {
        /** @var Ess_M2eProUpdater_Helper_Data_Cache_Permanent $helper */
        $helper = Mage::helper('M2eProUpdater/Data_Cache_Permanent');

        $helper->removeValue(self::RELEASES_INFO_CACHE_KEY);
        $helper->removeValue(self::LATEST_RELEASE_INFO_CACHE_KEY);
    }

    //########################################

    public function getTemporaryDirectoryPath()
    {
        $path = Mage::getBaseDir('var') .DS. self::IDENTIFIER .DS. 'tmp';

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    public function getLogDirectoryPath()
    {
        $path = Mage::getBaseDir('var') .DS. self::IDENTIFIER .DS. 'log';

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    //########################################
}