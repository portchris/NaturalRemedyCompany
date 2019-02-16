<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Observer_Notification
{
    const NOTIFICATION_MESSAGE_IDENTIFIER = 'm2epro_updater_message';

    private $isProcessed = false;

    //########################################

    public function add(Varien_Event_Observer $observer)
    {
        if ($this->shouldBeSkipped()) {
            return $this;
        }

        $this->isProcessed = true;

        /** @var Ess_M2eProUpdater_Helper_Config $configHelper */
        $configHelper = Mage::helper('M2eProUpdater/Config');
        $notificationsSetting = $configHelper->getNotificationType();

        if ($notificationsSetting == Ess_M2eProUpdater_Helper_Config::NOTIFICATIONS_DISABLED ||
            $notificationsSetting == Ess_M2eProUpdater_Helper_Config::NOTIFICATIONS_MAGENTO_SYSTEM_NOTIFICATION) {

            return $this;
        }

        /** @var Ess_M2eProUpdater_Helper_M2ePro $m2eProHelper */
        $m2eProHelper = Mage::helper('M2eProUpdater/M2ePro');

        $currentVersion = $m2eProHelper->getCurrentVersion();
        $latestVersion  = $m2eProHelper->getLatestAvailableVersion();

        if ($currentVersion && $latestVersion && version_compare($latestVersion, $currentVersion, '>')) {

            $link = Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit', array(
                'section' => Ess_M2eProUpdater_Observer_Configuration::INSTALLATION_UPGRADE_SECTION
            ));

            $message = <<<HTML
The new version {$latestVersion} of Multi-Channels Integration (M2E Pro Module) is available for upgrade. 
To run the Module upgrade, please, follow this <a target="_blank" href="{$link}">link</a>
HTML;
            /** @var Mage_Adminhtml_Model_Session $session */
            $session = Mage::getSingleton('adminhtml/session');

            $session->addMessage(
                Mage::getSingleton('core/message')->warning($m2eProHelper->__($message))
                    ->setIdentifier(self::NOTIFICATION_MESSAGE_IDENTIFIER)
            );
        }

        return $this;
    }

    //########################################

    private function shouldBeSkipped()
    {
        if ($this->isProcessed) {
            return true;
        }

        /** @var Mage_Adminhtml_Model_Session $session */
        $session = Mage::getSingleton('admin/session');
        $request = Mage::app()->getRequest();

        if (!$session->isLoggedIn() || $request->isPost() || $request->isAjax()) {
            return true;
        }

        if (Mage::app()->getResponse()->isRedirect()) {
            return true;
        }

        /** @var Ess_M2eProUpdater_Helper_Config $configHelper */
        $configHelper = Mage::helper('M2eProUpdater/Config');

        if ($configHelper->isNotificationExtensionPages() && $request->getModuleName() != 'M2ePro') {
            return true;
        }

        // do not show on configuration page
        if (strtolower($request->getControllerName()) == 'system_config') {
            return true;
        }

        // do not show on own controllers
        if ($request->getControllerModule() == 'Ess_M2eProUpdater_Adminhtml') {
            return true;
        }

        // after redirect message can be added twice
        foreach ($session->getMessages()->getItems() as $message) {
            if ($message->getIdentifier() == self::NOTIFICATION_MESSAGE_IDENTIFIER) {
                return true;
            }
        }

        return false;
    }

    //########################################
}