<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_Configuration_Messages
    extends Ess_M2eProUpdater_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    //########################################

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    //########################################

    protected function _toHtml()
    {
        /** @var Ess_M2eProUpdater_Block_Adminhtml_Messages $messagesBlock */
        $messagesBlock = $this->getLayout()->createBlock('M2eProUpdater/adminhtml_messages');

        $this->addOwnCronMessages($messagesBlock);
        $this->addLatestUpgradeWasCompletedUnsuccessfullyMessage($messagesBlock);

        return $messagesBlock->toHtml();
    }

    //########################################

    private function addOwnCronMessages(Ess_M2eProUpdater_Block_Adminhtml_Messages $messagesBlock)
    {
        /** @var Ess_M2eProUpdater_Helper_Support $supportHelper */
        $supportHelper = Mage::helper('M2eProUpdater/Support');

        if (!Mage::helper('M2eProUpdater/Cron')->isInstalled()) {

            $url = $supportHelper->getDocumentationUrl('x/BQA9AQ');
            $message = <<<HTML
Attention! The cron file m2epro_updater_cron.php has not been run.
It is required for the correct execution of Installation/Upgrade process.
Please check this <a target="_blank" href="{$url}">article</a> for the details on how to resolve the problem.
HTML;
            $messagesBlock->addError($supportHelper->__($message));
            return;
        }

        if (Mage::helper('M2eProUpdater/Cron')->isLastRunMoreThan(1, true)) {

            $url = $supportHelper->getDocumentationUrl('x/BQA9AQ');
            $message = <<<HTML
Attention! The cron file m2epro_updater_cron.php has not been run more than for a one hour.
It is required for the correct execution of Installation/Upgrade process.
Please check this <a target="_blank" href="{$url}">article</a> for the details on how to resolve the problem.
HTML;
            $messagesBlock->addWarning($supportHelper->__($message));
        }
    }

    //----------------------------------------

    private function addLatestUpgradeWasCompletedUnsuccessfullyMessage(
        Ess_M2eProUpdater_Block_Adminhtml_Messages $messagesBlock
    ){
        if ($this->isLatestUpgradeWasCompletedUnsuccessfully()) {

            $fileName = $this->getLatestUpgradeLogFileName();
            $downloadUrl = $this->getUrl('adminhtml/m2eProUpdater_log/get', array('file_name' => $fileName));
            $removeUrl   = $this->getUrl('adminhtml/m2eProUpdater_log/remove', array('file_name' => $fileName));

            $message = <<<HTML
The latest attempt to Install/Upgrade the Module was unsuccessful. 
M2E Pro version was not upgraded. 
You can find a detailed information about the failure in the 
<a href="{$downloadUrl}">Log</a> or <a href="{$removeUrl}">Ignore this message</a>.
HTML;
            $messagesBlock->addError(Mage::helper('M2eProUpdater')->__($message));
        }
    }

    private function isLatestUpgradeWasCompletedUnsuccessfully()
    {
        /** @var Ess_M2eProUpdater_Helper_M2ePro $m2eProHelper */
        $m2eProHelper = Mage::helper('M2eProUpdater/M2ePro');

        $currentVersion = $m2eProHelper->getCurrentVersion();
        $latestVersion  = $m2eProHelper->getLatestAvailableVersion();

        if (version_compare($currentVersion, $latestVersion, '>=')) {
            return false;
        }

        /** @var Ess_M2eProUpdater_Helper_Module $moduleHelper */
        $moduleHelper = Mage::helper('M2eProUpdater/Module');
        $filePath = $moduleHelper->getLogDirectoryPath() .DS. $this->getLatestUpgradeLogFileName();

        if (!is_file($filePath)) {
            return false;
        }

        if (file_get_contents($filePath) == '') {
            return false;
        }

        return true;
    }

    private function getLatestUpgradeLogFileName()
    {
        /** @var Ess_M2eProUpdater_Helper_M2ePro $m2eProHelper */
        $m2eProHelper = Mage::helper('M2eProUpdater/M2ePro');
        $latestVersion = $m2eProHelper->getLatestAvailableVersion();

        $fileName = Ess_M2eProUpdater_Model_Cron_Task_DoUpgrade::LOG_FILE_NAME_MASK;
        $latestVersion && $fileName = str_replace('%ver%', $latestVersion, $fileName);

        return $fileName;
    }

    //########################################
}