<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_Configuration_Fieldset_SystemInformation
    extends Ess_M2eProUpdater_Block_Adminhtml_Configuration_Fieldset
{
    //########################################

    protected function _getHeaderTitleHtml($element)
    {
        $legendPostfix = Mage::helper('M2eProUpdater/M2ePro')->isInstalled() ? 'Upgrade' : 'Installation';
        $element->setLegend(str_replace('%action%', $legendPostfix, $element->getLegend()));

        return parent::_getHeaderTitleHtml($element);
    }

    protected function _getFooterCommentHtml($element)
    {
        /** @var Ess_M2eProUpdater_Helper_Module $helper */
        $helper = Mage::helper('M2eProUpdater/Module');

        $currentVersion = $helper->getCurrentVersion();
        $latestVersion  = $helper->getLatestAvailableVersion();

        if (!$latestVersion || version_compare($latestVersion, $currentVersion, '<=')) {
            return '';
        }

        /** @var Ess_M2eProUpdater_Model_Downloader $downloader */
        $downloader = Mage::getModel('M2eProUpdater/Downloader', array(
            'visibility'     => $helper->getPackageVisibility(),
            'extension_code' => Ess_M2eProUpdater_Helper_Module::IDENTIFIER
        ));
        $latestReleaseData = $helper->getLatestAvailableReleaseInfo();

        /** @var Ess_M2eProUpdater_Helper_Support $supportHelper */
        $supportHelper = Mage::helper('M2eProUpdater/Support');

        $block = $this->getLayout()->createBlock('M2eProUpdater/Adminhtml_Configuration_Help', '', array(
            'style' => 'margin-top: 5px; margin-bottom: 5px !important;',
            'text' => <<<HTML
Download the latest version of Installation/Upgrade Module:
<a href="{$downloader->getPackageUrl()}">{$latestReleaseData['files']['zip']}</a>.<br>
Use the Installation/Upgrade Module strictly in accordance with these 
<a target="_blank" href="{$supportHelper->getDocumentationUrl('x/XgM0AQ')}">instructions</a>.
HTML
        ));

        return $block->toHtml();
    }

    //########################################
}