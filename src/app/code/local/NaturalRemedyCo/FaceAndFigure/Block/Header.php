<?php
/**
 * Main block class for Face & Figure Salon Onepage React Web App
 * 
 * @author      Chris Rogers
 * @package     rwd_faceandfigure_default
 * @since       2019-01-12
*/
class NaturalRemedyCo_FaceAndFigure_Block_Header extends NaturalRemedyCo_FaceAndFigure_Block_PageAbstract implements NaturalRemedyCo_FaceAndFigure_Block_PageInterface
{
	/**
	 * @var string
	 */
	const REACT_COMPONENT = "header";

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
		return $this->retrieveReactComponent(self::REACT_COMPONENT);
	}
}
?>