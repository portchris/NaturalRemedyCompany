<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_Configuration_Field_AutomaticUpgrade_ScheduleUpgrade
    extends Ess_M2eProUpdater_Block_Adminhtml_Configuration_Field_Abstract
{
    //########################################

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if (!$this->isVisible()) {
            return '';
        }

        /** @var Ess_M2eProUpdater_Helper_M2ePro $m2eProHelper */
        $m2eProHelper = Mage::helper('M2eProUpdater/M2ePro');

        $legendPostfix = $m2eProHelper->isInstalled() ? 'Upgrade' : 'Installation';
        $element->setLabel(str_replace('%action%', $legendPostfix, $element->getLabel()));

        return parent::render($element);
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setScopeLabel(false);
        
        $toolTipMessage = Mage::helper('M2ePro')->__('
<p>
    The automatic installation/upgrade process cannot be executed immediately due to the technical reasons.
    By applying the ‘Confirm’ button, you consent to install/upgrade M2E Pro Extension within nearest 5-15 minutes.
</p>
<p>
    <strong>Note:</strong> your Magento Instance will be in Maintenance Mode until M2E Pro 
    installation/upgrade process is completed.
</p>
        ');

        return $this->getButtonHtml() . $this->getTooltipHtml($toolTipMessage);
    }

    //########################################

    private function isVisible()
    {
        /** @var Ess_M2eProUpdater_Helper_Config $configHelper */
        $configHelper = Mage::helper('M2eProUpdater/Config');

        if ($configHelper->isUpgradeAllowed()) {
            return false;
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

    private function getButtonHtml()
    {
        $saveUrl = $this->getUrl('adminhtml/m2eProUpdater_configuration/save', array(
            'path'  => base64_encode(Ess_M2eProUpdater_Helper_Config::UPGRADE_ALLOWED_PATH),
            'value' => base64_encode('1')
        ));

        $button = $this->getLayout()->createBlock('adminhtml/widget_button');
        $button->setData(
            array(
                'id'      => 'schedule_upgrade_button',
                'label'   => Mage::helper('M2eProUpdater')->__('Confirm'),
                'class'   => 'primary',
                'onclick' => "setLocation('" . $saveUrl . "')",
            )
        );

        return $button->toHtml();
    }

    //########################################
}