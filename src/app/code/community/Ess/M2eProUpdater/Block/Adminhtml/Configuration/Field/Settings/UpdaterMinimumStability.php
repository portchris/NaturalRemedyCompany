<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_Configuration_Field_Settings_UpdaterMinimumStability
    extends Ess_M2eProUpdater_Block_Adminhtml_Configuration_Field_Abstract
{
    //########################################

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        /** @var Ess_M2eProUpdater_Helper_Config $helper */
        $helper = Mage::helper('M2eProUpdater/Config');

        $element->setValues($this->getOptionsArray());
        $element->setValue($helper->getUpdaterMinimumStability());

        return parent::_getElementHtml($element);
    }

    protected function getOptionsArray()
    {
        $helper = Mage::helper('M2eProUpdater');

        return array(
            array(
                'label' => $helper->__('Stable'),
                'value' => Ess_M2eProUpdater_Helper_Config::MINIMUM_STABILITY_STABLE
            ),
            array(
                'label' => $helper->__('Beta Testers'),
                'value' => Ess_M2eProUpdater_Helper_Config::MINIMUM_STABILITY_BETA
            )
        );
    }

    //########################################

    protected function _decorateRowHtml($element, $html)
    {
        return '<tr id="row_' . $element->getHtmlId() . '" style="display: none;">' . $html . '</tr>';
    }

    //########################################
}