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
	 * @var Mage_Core_Model_Store
	 */
	protected $_store;

	public function __construct() 
	{
		$this->_store = Mage::app()->getStore();
	}

	/**
	 * @return array
	 */
	public function getBlockConfig()
	{
		return json_encode([
			"title" => $this->_store->getFrontendName() 
		], true);
	}

	public function getReactComponent()
	{
		return Mage::getSingleton('core/design_package')->getSkinBaseDir() . DS . "js" . DS . "components" . DS . self::REACT_COMPONENT . ".js";
	}
}
?>