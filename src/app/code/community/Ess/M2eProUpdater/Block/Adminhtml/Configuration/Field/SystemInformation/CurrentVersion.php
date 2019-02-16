<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_Configuration_Field_SystemInformation_CurrentVersion
    extends Ess_M2eProUpdater_Block_Adminhtml_Configuration_Field_Abstract
{
    //########################################

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        /** @var Ess_M2eProUpdater_Helper_Module $helper */
        $helper = Mage::helper('M2eProUpdater/Module');
        $element->setValue($helper->getCurrentVersion());

        return parent::_getElementHtml($element);
    }

    //########################################
}