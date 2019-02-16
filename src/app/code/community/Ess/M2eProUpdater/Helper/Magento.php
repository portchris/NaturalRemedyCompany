<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Helper_Magento extends Mage_Core_Helper_Abstract
{
    //########################################

    public function getName()
    {
        return 'magento';
    }

    public function getVersion($asArray = false)
    {
        $versionString = Mage::getVersion();
        return $asArray ? explode('.',$versionString) : $versionString;
    }

    public function getRevision()
    {
        return 'undefined';
    }

    //########################################

    public function getEditionName()
    {
        if ($this->isProfessionalEdition()) {
            return 'professional';
        }
        if ($this->isEnterpriseEdition()) {
            return 'enterprise';
        }
        if ($this->isCommunityEdition()) {
            return 'community';
        }

        return 'undefined';
    }

    // ---------------------------------------

    public function isProfessionalEdition()
    {
        return Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') &&
               !Mage::getConfig()->getModuleConfig('Enterprise_AdminGws') &&
               !Mage::getConfig()->getModuleConfig('Enterprise_Checkout') &&
               !Mage::getConfig()->getModuleConfig('Enterprise_Customer');
    }

    public function isEnterpriseEdition()
    {
        return Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') &&
               Mage::getConfig()->getModuleConfig('Enterprise_AdminGws') &&
               Mage::getConfig()->getModuleConfig('Enterprise_Checkout') &&
               Mage::getConfig()->getModuleConfig('Enterprise_Customer');
    }

    public function isCommunityEdition()
    {
        return !$this->isProfessionalEdition() &&
               !$this->isEnterpriseEdition();
    }

    //########################################
}