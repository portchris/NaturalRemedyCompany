<?php
/**
 * Abstract Face and Figure Salon Page block class for Onepage React Web App
 * 
 * @author      Chris Rogers
 * @package     rwd_faceandfigure_default
 * @since       2019-01-12
*/
abstract class NaturalRemedyCo_FaceAndFigure_Block_PageAbstract extends Mage_Core_Block_Template 
{
	/**
	 * @var int
	 */
	protected $_storeId;

	/**
	 * @var Mage_Core_Cms_Model_Page
	 */
	protected $_page;

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
		$this->_storeId = $this->_store->getId();	
	}

	/**
	 * @return Mage_Core_Cms_Model_Page
	 */
	public function getPage()
	{
		return $this->_page;
	}

	/**
	 * @param int $pageId
	 * @param string $field
	 */
	public function setPage($pageId, $field = null)
	{
		$page = Mage::getModel('cms/page');
		$page->setStoreId($this->getStoreId());
		$this->_page = $page->load($pageId, $field);
	}

	/**
	 * @return 	int
	 */
	public function getStoreId()
	{
		return $this->_storeId;
	}

	/**
	 * @param string
	 * @return string
	 */
	public function retrieveReactComponent($component)
	{
		return $this->_design->getSkinUrl()	. "js" . DS . "components" . DS . $component . ".js";
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