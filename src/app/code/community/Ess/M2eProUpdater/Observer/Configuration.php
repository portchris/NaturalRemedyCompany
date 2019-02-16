<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Observer_Configuration
{
    const M2EPRO_UPDATER_TAB_NAME      = 'm2epro_updater';
    const INSTALLATION_UPGRADE_SECTION = 'installation_upgrade';

    //########################################

    public function editAction(Varien_Event_Observer $observer)
    {
        Mage::helper('M2eProUpdater/M2ePro')->clearCachedReleasesInfo();
        Mage::helper('M2eProUpdater/Module')->clearCachedReleasesInfo();

        Mage::helper('M2eProUpdater/Support')->setPageHelpLink('x/BQA9AQ');
    }

    public function saveAction(Varien_Event_Observer $observer)
    {
        /** @var Ess_M2eProUpdater_Helper_Config $configHelper */
        $configHelper = Mage::helper('M2eProUpdater/Config');

        $savedData = Mage::app()->getRequest()->getPost('groups', array());

        if (isset($savedData['settings']['fields']['notifications']['value'])) {

            $configHelper->setNotificationType(
                (int)$savedData['settings']['fields']['notifications']['value']
            );

            unset($savedData['settings']['fields']['notifications']);
        }

        if (isset($savedData['settings']['fields']['m2ePro_minimum_stability']['value'])) {

            $configHelper->setM2eProMinimumStability(
                $savedData['settings']['fields']['m2ePro_minimum_stability']['value']
            );

            unset($savedData['settings']['fields']['m2ePro_minimum_stability']);
        }

        if (isset($savedData['settings']['fields']['updater_minimum_stability']['value'])) {

            $configHelper->setUpdaterMinimumStability(
                $savedData['settings']['fields']['updater_minimum_stability']['value']
            );

            unset($savedData['settings']['fields']['updater_minimum_stability']);
        }

        Mage::app()->getRequest()->setPost('groups', $savedData);

        return $this;
    }

    //########################################

    public function initSystemConfig(Varien_Event_Observer $observer)
    {
        /** @var Varien_Simplexml_Config $config */
        $config = $observer->getConfig();

        $section = $config->getNode('sections/' . self::INSTALLATION_UPGRADE_SECTION);
        $section->label = Mage::helper('M2eProUpdater')->__('Installation');

        if ($config->getNode('tabs/m2epro')) {

            $section->tab   = 'm2epro';
            $section->label = Mage::helper('M2eProUpdater')->__('Upgrade');

            $config->setNode('tabs/' . self::M2EPRO_UPDATER_TAB_NAME, NULL, true);
        }

        return $this;
    }

    //########################################
}