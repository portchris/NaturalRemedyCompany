<?php
/**
 * Main block class for Face & Figure Salon Onepage React Web App
 * 
 * @author      Chris Rogers
 * @package     rwd_faceandfigure_default
 * @since       2019-01-12
*/
class NaturalRemedyCo_FaceAndFigure_Block_Treatments extends NaturalRemedyCo_FaceAndFigure_Block_PageAbstract implements NaturalRemedyCo_FaceAndFigure_Block_PageInterface
{
	const REACT_COMPONENT = "treatments";

	protected $_treatments;

	public function __construct() 
	{
		// $this->_treatments = [
		// 	"" =>  $this->getLayout()->createBlock('cms/block')->setBlockId('')->toHtml()
		// ];
	}

	/**
	 * @return array
	 */
	public function getBlockConfig()
	{
		return [
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