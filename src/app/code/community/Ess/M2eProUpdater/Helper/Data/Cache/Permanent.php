<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Helper_Data_Cache_Permanent extends Mage_Core_Helper_Abstract
{
    //########################################

    public function getValue($key)
    {
        $cacheKey = Ess_M2eProUpdater_Helper_Module::IDENTIFIER .'_'. $key;
        $value = Mage::app()->getCache()->load($cacheKey);
        return $value === false ? NULL : unserialize($value);
    }

    public function setValue($key, $value, array $tags = array(), $lifeTime = NULL)
    {
        if ($value === NULL) {
            throw new \Exception('Can\'t store NULL value');
        }

        if (is_null($lifeTime) || (int)$lifeTime <= 0) {
            $lifeTime = 60*60*24;
        }

        $cacheKey = Ess_M2eProUpdater_Helper_Module::IDENTIFIER .'_'. $key;

        $preparedTags = array(Ess_M2eProUpdater_Helper_Module::IDENTIFIER .'_main');
        foreach ($tags as $tag) {
            $preparedTags[] = Ess_M2eProUpdater_Helper_Module::IDENTIFIER .'_'. $tag;
        }

        Mage::app()->getCache()->save(serialize($value), $cacheKey, $preparedTags, (int)$lifeTime);
    }

    //########################################

    public function removeValue($key)
    {
        $cacheKey = Ess_M2eProUpdater_Helper_Module::IDENTIFIER .'_'. $key;
        Mage::app()->getCache()->remove($cacheKey);
    }

    public function removeTagValues($tag)
    {
        $tags = array(Ess_M2eProUpdater_Helper_Module::IDENTIFIER .'_'. $tag);
        Mage::app()->getCache()->clean($tags);
    }

    public function removeAllValues()
    {
        $this->removeTagValues('main');
    }

    //########################################
}