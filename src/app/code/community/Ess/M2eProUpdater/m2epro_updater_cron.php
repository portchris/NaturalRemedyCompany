<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

/*
 *  If you moved this file in another place, then please set a correct path to the MAGENTO_ROOT
 */
define('MAGENTO_ROOT', realpath(__DIR__ . '/../../../../../'));
require_once MAGENTO_ROOT . '/app/Mage.php' ;

if (php_sapi_name() !== 'cli'){
    echo "You can run this from the command line only.";
    exit(1);
}

try {

    Mage::init();

    /** @var Ess_M2eProUpdater_Model_Cron_Runner $cronRunner */
    $cronRunner = Mage::getModel('M2eProUpdater/Cron_Runner');
    $cronRunner->process();

    exit('ok');

} catch (\Exception $e) {

    echo $e;
    exit('error');
}