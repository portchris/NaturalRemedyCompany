<?php
/**
 * Public store inforamtion in JSON format
 * 
 * @author 	Chris Rogers
 * @since 	2019-02-22
 */

class NaturalRemedyCo_NRCLayout_IndexController extends Mage_Core_Controller_Front_Action 
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
		header("Access-Control-Allow-Origin: *");
		$this->_store = Mage::app()->getStore();
	}

	/**
	 * Return store info as JSON using layout file <info_index_index>
	 * @return 	JSON 
	 * @see 	Useful debugging: "Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles()); exit;"
	 */
	public function indexAction() 
	{
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->loadLayout();
		$this->renderLayout();
	}

	public function formAction()
	{
		$this->getResponse()->setHeader('X-Frame-Options', 'ALLOWALL', true);
		$this->loadLayout();
		$this->renderLayout();
	}
}

?>