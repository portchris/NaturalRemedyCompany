<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2eProUpdater_Block_Adminhtml_Configuration_Field_Abstract
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    static protected $isPopupInitialized = false;

    //########################################

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setScopeLabel(false);
        return $element->getElementHtml();
    }

    //########################################

    protected function initPopup()
    {
        if (self::$isPopupInitialized) {
            return $this;
        }

        $themeFileName = 'prototype/windows/themes/magento.css';
        $themeLibFileName = 'lib/'.$themeFileName;
        $themeFileFound = false;
        $skinBaseDir = Mage::getDesign()->getSkinBaseDir(
            array(
                '_package' => Mage_Core_Model_Design_Package::DEFAULT_PACKAGE,
                '_theme' => Mage_Core_Model_Design_Package::DEFAULT_THEME,
            )
        );

        if (!$themeFileFound && is_file($skinBaseDir .'/'.$themeLibFileName)) {
            $themeFileFound = true;
            $this->getLayout()->getBlock('head')->addCss($themeLibFileName);
        }

        if (!$themeFileFound && is_file(Mage::getBaseDir().'/js/'.$themeFileName)) {
            $themeFileFound = true;
            $this->getLayout()->getBlock('head')->addItem('js_css', $themeFileName);
        }

        if (!$themeFileFound) {
            $this->getLayout()->getBlock('head')->addCss($themeLibFileName);
            $this->getLayout()->getBlock('head')->addItem('js_css', $themeFileName);
        }

        $this->getLayout()->getBlock('head')
            ->addJs('prototype/window.js')
            ->addItem('js_css', 'prototype/windows/themes/default.css');

        self::$isPopupInitialized = true;
        return $this;
    }

    //########################################

    protected function getTooltipHtml($toolTipMessage)
    {
        return <<<HTML
<td class="value">
    <img src="{$this->getSkinUrl('M2eProUpdater/images/tool-tip-icon.png')}" class="m2eproupdater-tool-tip-image">
    <span class="m2eproupdater-tool-tip-message" style="display: none;">
        <img src="{$this->getSkinUrl('M2eProUpdater/images/help.png')}">
        <span>{$toolTipMessage}</span>
    </span>
</td>
HTML;
    }

    //########################################
}