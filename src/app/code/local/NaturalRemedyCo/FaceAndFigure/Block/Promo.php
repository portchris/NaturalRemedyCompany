<?php
/**
 * Promo block class for Face & Figure Salon Onepage React Web App
 * 
 * @author      Chris Rogers
 * @package     rwd_faceandfigure_default
 * @since       2019-01-12
*/
class NaturalRemedyCo_FaceAndFigure_Block_Promo extends NaturalRemedyCo_FaceAndFigure_Block_PageAbstract implements NaturalRemedyCo_FaceAndFigure_Block_PageInterface
{
	const REACT_COMPONENT = "promo";

	public function __construct() 
	{
		parent::__construct();	
	}

	/**
	 * @return array
	 */
	public function getBlockConfig()
	{
		return [
			"data" => $this->getLayout()->createBlock('cms/block')->setBlockId('faceandfigure_selling_points_hero_breakdown')->toHtml()
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