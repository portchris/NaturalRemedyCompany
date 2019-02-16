<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Adminhtml_M2eProUpdater_LogController extends Mage_Adminhtml_Controller_Action
{
    //########################################

    public function getAction()
    {
        /** @var Ess_M2eProUpdater_Helper_Module $moduleHelper */
        $moduleHelper = Mage::helper('M2eProUpdater/Module');

        $fileName = $this->getRequest()->getParam('file_name');
        $filePath = $moduleHelper->getLogDirectoryPath() .DS. $fileName;

        if (!is_file($filePath)) {

            $this->_getSession()->addError("Log is not exists [{$fileName}]");
            return $this->_redirect('adminhtml/system_config/edit', array(
                'section' => Ess_M2eProUpdater_Observer_Configuration::INSTALLATION_UPGRADE_SECTION
            ));
        }

        $this->getResponse()->setHeader('Content-type', 'text/plain; charset=UTF-8');
        $this->getResponse()->setHeader('Content-length', filesize($filePath));
        $this->getResponse()->setHeader('Content-Disposition', 'attachment' . '; filename=' .basename($filePath));

        $this->getResponse()->sendHeaders();

        readfile($filePath);
        die;
    }

    public function removeAction()
    {
        /** @var Ess_M2eProUpdater_Helper_Module $moduleHelper */
        $moduleHelper = Mage::helper('M2eProUpdater/Module');

        $fileName = $this->getRequest()->getParam('file_name');
        $filePath = $moduleHelper->getLogDirectoryPath() .DS. $fileName;

        if (!is_file($filePath)) {
            $this->_getSession()->addError("Log is not exists [{$fileName}]");
        } else {
            $this->_getSession()->addSuccess('Log has been removed.');
        }

        unlink($filePath);

        return $this->_redirect('adminhtml/system_config/edit', array(
            'section' => Ess_M2eProUpdater_Observer_Configuration::INSTALLATION_UPGRADE_SECTION
        ));
    }

    //########################################
}