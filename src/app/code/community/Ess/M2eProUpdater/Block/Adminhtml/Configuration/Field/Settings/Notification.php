<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_Configuration_Field_Settings_Notification
    extends Ess_M2eProUpdater_Block_Adminhtml_Configuration_Field_Abstract
{
    //########################################

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        /** @var Ess_M2eProUpdater_Helper_Config $helper */
        $helper = Mage::helper('M2eProUpdater/Config');

        $element->setValues($this->getOptionsArray());
        $element->setValue($helper->getNotificationType());

        $toolTipMessage = Mage::helper('M2ePro')->__('
<p>Set your preferences on how you should be notified about the new version released:
    <ul>
        <li>
            <strong>Do Not Notify</strong> - no notification required;
        </li>
        <li>
            <strong>On each Extension Page</strong> (default) - notification will be shown on each M2E Pro page;
        </li>
        <li>
            <strong>On each Magento Page</strong> - notification will be shown on each Magento page;
        </li>
        <li>
            <strong>As Magento System Notification</strong> - notification will be shown via Magento global 
            messages system.
        </li>
    </ul>
</p>
        ');
        $element->setData('after_element_html', $this->getTooltipHtml($toolTipMessage));

        return parent::_getElementHtml($element);
    }

    protected function getOptionsArray()
    {
        $helper = Mage::helper('M2eProUpdater');

        return array(
            array(
                'label' => $helper->__('Do Not Notify'),
                'value' => Ess_M2eProUpdater_Helper_Config::NOTIFICATIONS_DISABLED
            ),
            array(
                'label' => $helper->__('On each Extension Page'),
                'value' => Ess_M2eProUpdater_Helper_Config::NOTIFICATIONS_EXTENSION_PAGES
            ),
            array(
                'label' => $helper->__('On each Magento Page'),
                'value' => Ess_M2eProUpdater_Helper_Config::NOTIFICATIONS_MAGENTO_PAGES
            ),
            array(
                'label' => $helper->__('As Magento System Notification'),
                'value' => Ess_M2eProUpdater_Helper_Config::NOTIFICATIONS_MAGENTO_SYSTEM_NOTIFICATION
            )
        );
    }

    //########################################
}