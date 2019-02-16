<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

//########################################

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

//########################################

$connection = $installer->getConnection()->insert(
    $installer->getTable('core_config_data'),
    array(
        'scope'    => 'default',
        'scope_id' => '0',
        'path'     => 'm2epro_updater/minimum_stability/m2epro', // Ess_M2eProUpdater_Helper_Config::M2EPRO_MINIMUM_STABILITY_PATH
        'value'    => 'stable'                                   // Ess_M2eProUpdater_Helper_Config::MINIMUM_STABILITY_STABLE
    )
);

$connection = $installer->getConnection()->insert(
    $installer->getTable('core_config_data'),
    array(
        'scope'    => 'default',
        'scope_id' => '0',
        'path'     => 'm2epro_updater/minimum_stability/m2epro_updater', // Ess_M2eProUpdater_Helper_Config::UPDATER_MINIMUM_STABILITY_PATH
        'value'    => 'stable'                                           // Ess_M2eProUpdater_Helper_Config::MINIMUM_STABILITY_STABLE
    )
);

//########################################

$installer->endSetup();