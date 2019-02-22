<?php
/**
 * Main block class for Face & Figure Salon Onepage React Web App
 * 
 * @author      Chris Rogers
 * @package     rwd_faceandfigure_default
 * @since       2019-01-12
*/
class NaturalRemedyCo_FaceAndFigure_Block_Header extends Mage_Core_Block_Template 
{
	/**
	 * @var string
	 */
	const REACT_COMPONENT = "header";

	/**
	 * @var Mage_Core_Design_Package
	 */
	protected $_design;


	/**
	 * @var Mage_Core_Model_Store
	 */
	protected $_store;

	/**
	 * Magento contructor
	 */
	public function _construct()
	{
		$this->_design = Mage::getSingleton('core/design_package');
		$this->_store = Mage::app()->getStore();
	}

	/**
	 * @return array
	 */
	public function getBlockConfig()
	{
		$logoSrc = Mage::getStoreConfig('design/header/logo_src'); 
		return [
			"title" => $this->_store->getFrontendName(),
			"logo" => $this->_design->getSkinUrl($logoSrc)
		];
	}

	/**
	 * @return string
	 */
	public function getReactComponent()
	{
		return $this->_design->getSkinUrl()	. "js" . DS . "components" . DS . self::REACT_COMPONENT . ".js";
	}

	/**
	 * @return array
	 */
	public function getComponentsRegistry()
	{
		return (Mage::registry('react_components', $this->_store->getId())) ? Mage::registry('react_components', $this->_store->getId()) : [];

	}

	/**
	 * Override default get using getChildHtml, return JSON instead
	 * @return JSON
	 */
	public function _toHtml()
	{
		return $this->getBlockConfig();
	}
}
?>