<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Helper_Config extends Mage_Core_Helper_Abstract
{
    const UPGRADE_ALLOWED_PATH   = 'm2epro_updater/upgrade_allowed';
    const NOTIFICATIONS_PATH     = 'm2epro_updater/notifications';
    const M2EPRO_MINIMUM_STABILITY_PATH  = 'm2epro_updater/minimum_stability/m2epro';
    const UPDATER_MINIMUM_STABILITY_PATH = 'm2epro_updater/minimum_stability/m2epro_updater';

    const NOTIFICATIONS_DISABLED                    = 0;
    const NOTIFICATIONS_EXTENSION_PAGES             = 1;
    const NOTIFICATIONS_MAGENTO_PAGES               = 2;
    const NOTIFICATIONS_MAGENTO_SYSTEM_NOTIFICATION = 3;

    const MINIMUM_STABILITY_STABLE = 'stable';
    const MINIMUM_STABILITY_BETA   = 'beta_testers';

    //########################################

    public function isUpgradeAllowed()
    {
        /** @var Ess_M2eProUpdater_Helper_Magento_Config $helper */
        $helper = Mage::helper('M2eProUpdater/Magento_Config');
        return (bool)(int)$helper->getValue(self::UPGRADE_ALLOWED_PATH);
    }

    public function setUpgradeAllowed($value)
    {
        /** @var Ess_M2eProUpdater_Helper_Magento_Config $helper */
        $helper = Mage::helper('M2eProUpdater/Magento_Config');
        $helper->setValue(self::UPGRADE_ALLOWED_PATH, (int)$value);
    }

    ////########################################

    public function getNotificationType()
    {
        /** @var Ess_M2eProUpdater_Helper_Magento_Config $helper */
        $helper = Mage::helper('M2eProUpdater/Magento_Config');
        return (int)$helper->getValue(self::NOTIFICATIONS_PATH, self::NOTIFICATIONS_EXTENSION_PAGES);
    }

    public function setNotificationType($value)
    {
        /** @var Ess_M2eProUpdater_Helper_Magento_Config $helper */
        $helper = Mage::helper('M2eProUpdater/Magento_Config');
        $helper->setValue(self::NOTIFICATIONS_PATH, (int)$value);
    }

    //----------------------------------------

    public function isNotificationDisabled()
    {
        return $this->getNotificationType() == self::NOTIFICATIONS_DISABLED;
    }

    public function isNotificationExtensionPages()
    {
        return $this->getNotificationType() == self::NOTIFICATIONS_EXTENSION_PAGES;
    }

    public function isNotificationMagentoPages()
    {
        return $this->getNotificationType() == self::NOTIFICATIONS_MAGENTO_PAGES;
    }

    public function isNotificationMagentoSystemNotification()
    {
        return $this->getNotificationType() == self::NOTIFICATIONS_MAGENTO_SYSTEM_NOTIFICATION;
    }

    //########################################

    public function getM2eProMinimumStability()
    {
        /** @var Ess_M2eProUpdater_Helper_Magento_Config $helper */
        $helper = Mage::helper('M2eProUpdater/Magento_Config');
        return $helper->getValue(self::M2EPRO_MINIMUM_STABILITY_PATH, self::MINIMUM_STABILITY_STABLE);
    }

    public function setM2eProMinimumStability($value)
    {
        /** @var Ess_M2eProUpdater_Helper_Magento_Config $helper */
        $helper = Mage::helper('M2eProUpdater/Magento_Config');
        $helper->setValue(self::M2EPRO_MINIMUM_STABILITY_PATH, $value);
    }

    //----------------------------------------

    public function isM2eProMinimumStabilityStable()
    {
        return $this->getM2eProMinimumStability() == self::MINIMUM_STABILITY_STABLE;
    }

    public function isM2eProMinimumStabilityBetaTesters()
    {
        return $this->getM2eProMinimumStability() == self::MINIMUM_STABILITY_BETA;
    }

    //########################################

    public function getUpdaterMinimumStability()
    {
        /** @var Ess_M2eProUpdater_Helper_Magento_Config $helper */
        $helper = Mage::helper('M2eProUpdater/Magento_Config');
        return $helper->getValue(self::UPDATER_MINIMUM_STABILITY_PATH, self::MINIMUM_STABILITY_STABLE);
    }

    public function setUpdaterMinimumStability($value)
    {
        /** @var Ess_M2eProUpdater_Helper_Magento_Config $helper */
        $helper = Mage::helper('M2eProUpdater/Magento_Config');
        $helper->setValue(self::UPDATER_MINIMUM_STABILITY_PATH, $value);
    }

    //----------------------------------------

    public function isUpdaterMinimumStabilityStable()
    {
        return $this->getUpdaterMinimumStability() == self::MINIMUM_STABILITY_STABLE;
    }

    public function isUpdaterMinimumStabilityBetaTesters()
    {
        return $this->getUpdaterMinimumStability() == self::MINIMUM_STABILITY_BETA;
    }

    //########################################
}