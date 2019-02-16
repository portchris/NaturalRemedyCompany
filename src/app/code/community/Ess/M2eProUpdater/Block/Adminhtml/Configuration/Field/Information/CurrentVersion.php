<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_Configuration_Field_Information_CurrentVersion
    extends Ess_M2eProUpdater_Block_Adminhtml_Configuration_Field_Abstract
{
    //########################################

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        /** @var Ess_M2eProUpdater_Helper_M2ePro $helper */
        $helper = Mage::helper('M2eProUpdater/M2ePro');

        $currentVersion = $helper->getCurrentVersion();
        if (!$currentVersion) {
            $currentVersion = $helper->__('Not Installed');
            $element->setBold(true);
        }

        $element->setValue($currentVersion);
        return parent::_getElementHtml($element);
    }

    //########################################
}