<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Helper_Magento_Config extends Mage_Core_Helper_Abstract
{
    private $cache = array();

    //########################################

    public function getValue($path, $default = false)
    {
        if (!isset($this->cache[$path])) {

            /** @var $connWrite Varien_Db_Adapter_Pdo_Mysql */
            $connWrite = Mage::getSingleton('core/resource')->getConnection('core_write');

            $select = $connWrite
                ->select()
                ->from(Mage::getSingleton('core/resource')->getTableName('core_config_data'), 'value')
                ->where('scope = ?', 'default')
                ->where('scope_id = ?', 0)
                ->where('path = ?', $path);

            $result = $connWrite->fetchOne($select);
            $result === false && $result = $default;

            $this->cache[$path] = $result;
        }

        return $this->cache[$path];
    }

    public function setValue($path, $value)
    {
        if (isset($this->cache[$path])) {
            unset($this->cache[$path]);
        }

        /** @var $connWrite Varien_Db_Adapter_Pdo_Mysql */
        $connWrite = Mage::getSingleton('core/resource')->getConnection('core_write');

        if ($this->getValue($path) === false) {

            $connWrite->insert(
                Mage::getSingleton('core/resource')->getTableName('core_config_data'),
                array(
                    'scope'    => 'default',
                    'scope_id' => 0,
                    'path'     => $path,
                    'value'    => $value
                )
            );
            return;
        }

        $connWrite->update(
            Mage::getSingleton('core/resource')->getTableName('core_config_data'),
            array('value' => $value),
            array(
                'scope = ?'    => 'default',
                'scope_id = ?' => 0,
                'path = ?'     => $path,
            )
        );
    }

    //########################################
}