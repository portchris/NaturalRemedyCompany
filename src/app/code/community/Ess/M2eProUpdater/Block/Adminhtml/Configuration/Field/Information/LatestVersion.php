<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_Configuration_Field_Information_LatestVersion
    extends Ess_M2eProUpdater_Block_Adminhtml_Configuration_Field_Abstract
{
    //########################################

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->initPopup();

        $this->getLayout()->getBlock('head')
            ->addJs('M2eProUpdater/ConfigurationHandler.js');
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $urls = json_encode(array(
            'adminhtml/configuration/getChangeLogHtml' => $this->getUrl(
                'adminhtml/m2eProUpdater_configuration/getChangeLogHtml'
            ),
        ));

        $translations = json_encode(array(
            'Changelog' => $this->__('Changelog')
        ));

        $javascript = <<<HTML
<script type="text/javascript">

    UrlHandlerObj.add({$urls});
    TranslatorHandlerObj.add({$translations});
    
    if (typeof ConfigurationHandlerObj == 'undefined') {
        ConfigurationHandlerObj = new ConfigurationHandler();
    }
    
</script>
HTML;
        return $javascript . parent::render($element);
    }

    //########################################

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        /** @var Ess_M2eProUpdater_Helper_M2ePro $helper */
        $helper = Mage::helper('M2eProUpdater/M2ePro');

        $currentVersion = $helper->getCurrentVersion();
        $latestVersion  = $helper->getLatestAvailableVersion();

        if ($latestVersion && (version_compare($latestVersion, $currentVersion, '>'))) {

            $moduleCode = Ess_M2eProUpdater_Helper_M2ePro::IDENTIFIER;
            $latestVersion .= '  ' .$helper->__('[New]');
            $changelogWorld = $helper->__('[changelog]');

            $afterHtml = <<<HTML
<a href="javascript:void(0)" 
   onclick="ConfigurationHandlerObj.showModuleChangelogPopup('{$moduleCode}');">{$changelogWorld}</a>
HTML;

            $element->setBold(true);
            $element->setData('after_element_html', $afterHtml);

        } else if (!$latestVersion) {
            $latestVersion = $helper->__('N/A');
        }

        $element->setValue($latestVersion);
        return parent::_getElementHtml($element);
    }

    //########################################
}