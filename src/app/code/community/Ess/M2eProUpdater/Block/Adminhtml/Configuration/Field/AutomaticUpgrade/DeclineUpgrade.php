<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_Configuration_Field_AutomaticUpgrade_DeclineUpgrade
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
        return $this->getButtonHtml();
    }

    //########################################

    private function isVisible()
    {
        /** @var Ess_M2eProUpdater_Helper_Config $configHelper */
        $configHelper = Mage::helper('M2eProUpdater/Config');
        return $configHelper->isUpgradeAllowed();
    }

    private function getButtonHtml()
    {
        $saveUrl = $this->getUrl('adminhtml/m2eProUpdater_configuration/save', array(
            'path'  => base64_encode(Ess_M2eProUpdater_Helper_Config::UPGRADE_ALLOWED_PATH),
            'value' => base64_encode('0')
        ));

        $button = $this->getLayout()->createBlock('adminhtml/widget_button');
        $button->setData(
            array(
                'id'      => 'decline_upgrade_button',
                'label'   => Mage::helper('M2eProUpdater')->__('Confirm'),
                'class'   => 'primary',
                'onclick' => "setLocation('" . $saveUrl . "')",
            )
        );

        return $button->toHtml();
    }

    //########################################
}