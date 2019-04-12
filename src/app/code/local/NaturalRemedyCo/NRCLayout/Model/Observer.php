<?php
/**
 * Public store inforamtion in JSON format
 * 
 * @author 	Chris Rogers
 * @since 	2019-02-22
 */

class NaturalRemedyCo_NRCLayout_Model_Observer 
{
	/**
	 * @var Mage_Core_Model_Store
	 */
	protected $_store;

	/**
	 * Magento's specific construtor (notice the single underscore)
	 */
	public function _construct() 
	{
		$this->_store = Mage::app()->getStore();
	}

	public function addHeaders(
		Varien_Event_Observer $observer
	) {
		// $url = Mage::app()->getBaseUrl();
		$uri = Mage::helper('core/url')->getCurrentUrl();
		if (strpos($uri, "contact") !== false) {
			header("X-Frame-Options: *");
		}
	}
}
?>