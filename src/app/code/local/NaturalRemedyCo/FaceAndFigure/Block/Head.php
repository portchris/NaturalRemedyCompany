<?php
/**
 * HTML Head block class for Face & Figure Salon Onepage React Web App
 * 
 * @author      Chris Rogers
 * @package     rwd_faceandfigure_default
 * @since       2019-01-12
*/
class NaturalRemedyCo_FaceAndFigure_Block_Head extends NaturalRemedyCo_FaceAndFigure_Block_PageAbstract implements NaturalRemedyCo_FaceAndFigure_Block_PageInterface
{
	const REACT_COMPONENT = "head";

	/**
	 * @var string
	 */
	public $_html;

	public function __construct()
	{
		$this->_html = "";
		parent::__construct();
	}

	/**
	 * @return array
	 */
	public function getBlockConfig()
	{
		return [
			"data" => $this->_html
		];
	}

	/**
	 * @return string
	 */
	public function getReactComponent()
	{
		return $this->retrieveReactComponent(self::REACT_COMPONENT);
	}

	/**
	 * Override parent plass here to return more custom response
	 * @return array Ready for JSON encode
	 */
	public function _toHtml()
	{
		if (!$this->getTemplate()) {
			return '';
		}
		$this->_html = $this->renderView();
		return $this->getBlockConfig();
	}
}
