<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_Configuration_Fieldset_AutomaticUpgrade
    extends Ess_M2eProUpdater_Block_Adminhtml_Configuration_Fieldset
{
    //########################################

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if (!$this->isVisible()) {
            return '';
        }

        return parent::render($element);
    }

    private function isVisible()
    {
        /** @var Ess_M2eProUpdater_Helper_Config $configHelper */
        $configHelper = Mage::helper('M2eProUpdater/Config');

        if ($configHelper->isUpgradeAllowed()) {
            return true;
        }

        /** @var Ess_M2eProUpdater_Helper_M2ePro $m2eProHelper */
        $m2eProHelper = Mage::helper('M2eProUpdater/M2ePro');

        $currentVersion = $m2eProHelper->getCurrentVersion();
        $latestVersion  = $m2eProHelper->getLatestAvailableVersion();

        if ($latestVersion && version_compare($latestVersion, $currentVersion, '>')) {
            return true;
        }

        return false;
    }

    //########################################

    protected function _getHeaderTitleHtml($element)
    {
        /** @var Ess_M2eProUpdater_Helper_M2ePro $m2eProHelper */
        $m2eProHelper = Mage::helper('M2eProUpdater/M2ePro');

        $legendPostfix = $m2eProHelper->isInstalled() ? 'Upgrade' : 'Installation';
        $element->setLegend(str_replace('%action%', $legendPostfix, $element->getLegend()));

        return parent::_getHeaderTitleHtml($element);
    }

    protected function _getHeaderCommentHtml($element)
    {
        /** @var Ess_M2eProUpdater_Helper_Config $helper */
        $helper = Mage::helper('M2eProUpdater/Config');

        if (!$helper->isUpgradeAllowed()) {
            return '';
        }

        /** @var Ess_M2eProUpdater_Block_Adminhtml_Messages $messagesBlock */
        $messagesBlock = $this->getLayout()->createBlock('M2eProUpdater/adminhtml_messages');
        $messagesBlock->addNotice($helper->__(
            'M2E Pro Extension will be %s within 5-15 minutes. Please wait.',
            Mage::helper('M2eProUpdater/M2ePro')->isInstalled() ? 'upgraded' : 'installed'
        ));

        return $messagesBlock->toHtml();
    }

    //########################################
}