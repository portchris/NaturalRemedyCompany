<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Adminhtml_M2eProUpdater_ConfigurationController extends Mage_Adminhtml_Controller_Action
{
    //########################################

    public function saveAction()
    {
        $configPath  = $this->getRequest()->getParam('path');
        $configValue = $this->getRequest()->getParam('value');

        if (is_null($configPath) || is_null($configValue)) {
            $this->_getSession()->addError('Some required params are missing.');
        } else {

            /** @var Ess_M2eProUpdater_Helper_Magento_Config $configHelper */
            $configHelper = Mage::helper('M2eProUpdater/Magento_Config');
            $configHelper->setValue(base64_decode($configPath), base64_decode($configValue));

            $this->_getSession()->addSuccess('Saved.');
        }

        return $this->_redirect('adminhtml/system_config/edit', array(
            'section' => Ess_M2eProUpdater_Observer_Configuration::INSTALLATION_UPGRADE_SECTION
        ));
    }

    //########################################

    public function getChangeLogHtmlAction()
    {
        $this->loadLayout();

        /** @var Ess_M2eProUpdater_Block_Adminhtml_Configuration_Changelog $block */
        $block = $this->getLayout()->createBlock(
            'M2eProUpdater/adminhtml_configuration_changelog', '', array(
                'module_code' => $this->getRequest()->getParam('module')
            )
        );

        $this->getResponse()->setBody($block->toHtml());
    }

    //########################################
}