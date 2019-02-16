<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_Configuration_Fieldset_ManualUpgrade
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
        /** @var Ess_M2eProUpdater_Helper_M2ePro $m2ePro */
        $m2ePro = Mage::helper('M2eProUpdater/M2ePro');

        /** @var Ess_M2eProUpdater_Block_Adminhtml_Messages $messagesBlock */
        $messagesBlock = $this->getLayout()->createBlock('M2eProUpdater/adminhtml_messages');
        $messagesBlock->addNotice($m2ePro->__(<<<HTML
Note, the Alternative ways of Extension installation/upgrade require technical skills to 
perform the procedure manually. Use these methods only in case of extreme necessity when the automatic 
installation/upgrade cannot be executed for some reasons.<br>
Below you can find the latest M2E Pro release.
HTML
        ));

        /** @var Ess_M2eProUpdater_Model_Downloader $downloader */
        $downloader = Mage::getModel('M2eProUpdater/Downloader', array(
            'visibility'     => $m2ePro->getPackageVisibility(),
            'extension_code' => Ess_M2eProUpdater_Helper_M2ePro::IDENTIFIER
        ));

        $latestRelease = $m2ePro->getLatestAvailableReleaseInfo();

        $zipFile = $latestRelease['files']['zip'];
        $zipLink = $downloader->getPackageUrl();

        $connectFile = $latestRelease['files']['connect']['1.0'];
        $connectLink = $downloader->getPackageUrl(NULL, $downloader::BUILD_FORMAT_CONNECT_1);
        $connectVersion = '1.0';

        $minVersion = Mage::helper('M2eProUpdater/Magento')->isCommunityEdition() ? '1.5.0.0' : '1.10';
        if (version_compare(Mage::helper('M2eProUpdater/Magento')->getVersion(), $minVersion, '>=')) {

            $connectFile = $latestRelease['files']['connect']['2.0'];
            $connectLink = $downloader->getPackageUrl(NULL, $downloader::BUILD_FORMAT_CONNECT_2);
            $connectVersion = '2.0';
        }

        /** @var Ess_M2eProUpdater_Helper_Support $supportHelper */
        $supportHelper = Mage::helper('M2eProUpdater/Support');

        return <<<HTML
{$messagesBlock->toHtml()}
<ul>
    <li>
        Download Magento Connect Package (for v{$connectVersion}): <a href="{$connectLink}">{$connectFile}</a>
        (the instructions can be found 
        <a target="_blank" href="{$supportHelper->getDocumentationUrl('x/XgM0AQ')}">here</a>)
    </li>
    <li>
        Download M2E Pro Extension Source Code: <a href="{$zipLink}">{$zipFile}</a>
        (the instructions can be found 
        <a target="_blank" href="{$supportHelper->getDocumentationUrl('x/XgM0AQ')}">here</a>)
    </li>
</ul>
HTML;
    }

    //########################################
}