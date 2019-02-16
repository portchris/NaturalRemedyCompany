<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_Configuration_Changelog
    extends Ess_M2eProUpdater_Block_Abstract
{
    protected $_moduleCode;

    protected $_template = 'M2eProUpdater/configuration/changelog.phtml';

    //########################################

    public function __construct(array $args = array())
    {
        parent::__construct($args);

        if (empty($args['module_code'])) {
            throw new \Exception('Module code is not provided.');
        }

        $this->_moduleCode = $args['module_code'];
    }

    //########################################

    public function getAffectedReleases()
    {
        /** @var Ess_M2eProUpdater_Helper_Module|Ess_M2eProUpdater_Helper_M2ePro $helper */
        $helper = $this->_moduleCode == Ess_M2eProUpdater_Helper_Module::IDENTIFIER
            ? Mage::helper('M2eProUpdater/Module')
            : Mage::helper('M2eProUpdater/M2ePro');

        $currentVersion = $helper->getCurrentVersion();
        $latestVersion  = $helper->getLatestAvailableVersion();

        $affected = array();
        foreach ($helper->getReleasesInfo() as $version => $release) {

            if (version_compare($version, $currentVersion, '<=') ||
                version_compare($version, $latestVersion, '>'))
            {
                continue;
            }

            $affected[$version] = $release;
        }

        uksort($affected, function($a, $b) {
            return version_compare($b, $a);
        });

        return $affected;
    }

    public function hasChangeLogsRecords($release)
    {
        $result = array_filter($release['changelog'], function($elements){
            return !empty($elements);
        });

        return !empty($result);
    }

    //########################################

    protected function _beforeToHtml()
    {
        $buttonBlock = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'class'   => 'close_button',
            'label'   => Mage::helper('M2eProUpdater')->__('Close'),
            'onclick' => 'Windows.getFocusedWindow().close();',
        ));
        $this->setChild('close_button', $buttonBlock);

        return parent::_beforeToHtml();
    }

    //########################################
}