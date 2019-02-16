<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_System_Message extends Ess_M2eProUpdater_Block_Abstract
{
    //########################################

    public $currentVersion;
    public $latestVersion;

    protected $_template = 'M2eProUpdater/system/message.phtml';

    //########################################

    public function __construct()
    {
        parent::__construct();

        /** @var Ess_M2eProUpdater_Helper_M2ePro $helper */
        $helper = Mage::helper('M2eProUpdater/M2ePro');

        $this->currentVersion = $helper->getCurrentVersion();
        $this->latestVersion  = $helper->getLatestAvailableVersion();
    }

    //########################################

    public function _toHtml()
    {
        if (!$this->isDisplayed()) {
            return '';
        }
        return parent::_toHtml();
    }

    public function getText()
    {
        $link = $this->getUrl('adminhtml/system_config/edit', array(
            'section' => Ess_M2eProUpdater_Observer_Configuration::INSTALLATION_UPGRADE_SECTION
        ));

        $message = <<<HTML
The new version <strong>{$this->latestVersion}</strong> of Multi-Channels Integration (M2E Pro Module) 
is available for upgrade. 
To run the Module upgrade, please, follow this <a target="_blank" href="{$link}">link</a>
HTML;

        return Mage::helper('M2eProUpdater')->__($message);
    }

    //########################################

    private function isDisplayed()
    {
        /** @var Mage_Adminhtml_Model_Session $session */
        $session = Mage::getSingleton('admin/session');

        if (!$session->isLoggedIn()) {
            return false;
        }

        /** @var Ess_M2eProUpdater_Helper_Config $helper */
        $helper = Mage::helper('M2eProUpdater/Config');

        if (!$helper->isNotificationMagentoSystemNotification()) {
            return false;
        }

        if (!$this->currentVersion || version_compare($this->currentVersion, $this->latestVersion, '>=')) {
            return false;
        }

        return true;
    }

    //########################################
}