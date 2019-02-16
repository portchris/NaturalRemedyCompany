<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Helper_Support extends Mage_Core_Helper_Abstract
{
    const KNOWLEDGE_BASE_URL = 'https://support.m2epro.com/knowledgebase';
    const DOCUMENTATION_URL  = 'https://docs.m2epro.com';

    //########################################

    public function getDocumentationUrl($articleUrl = NULL, $tinyLink = NULL)
    {
        $urlParts[] = self::DOCUMENTATION_URL;

        if ($articleUrl) {
            $urlParts[] = 'display';
        }

        if ($articleUrl) {
            $urlParts[] = trim($articleUrl, '/');
        }

        if ($tinyLink) {
            $urlParts[] = $tinyLink;
        }

        return implode('/', $urlParts);
    }

    public function getKnowledgeBaseUrl($articleUrl = NULL)
    {
        $urlParts[] = self::KNOWLEDGE_BASE_URL;

        if ($articleUrl) {
            $urlParts[] = trim($articleUrl, '/');
        }

        return implode('/', $urlParts);
    }

    //########################################

    public function setPageHelpLink($tinyLink = NULL)
    {
        Mage::helper('adminhtml')->setPageHelpUrl($this->getDocumentationUrl(NULL, $tinyLink));
    }

    //########################################
}