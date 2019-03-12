<?php
/**
 * Main block class for Face & Figure Salon Onepage React Web App
 * 
 * @author      Chris Rogers
 * @package     rwd_faceandfigure_default
 * @since       2019-01-12
*/
class NaturalRemedyCo_FaceAndFigure_Block_Main extends NaturalRemedyCo_FaceAndFigure_Block_PageAbstract implements NaturalRemedyCo_FaceAndFigure_Block_PageInterface
{
	const PAGE_KEY = "faceandfigure";
	const REACT_COMPONENT = "main";

	public function __construct() 
	{
		parent::__construct();	
		$this->setPage(self::PAGE_KEY, 'identifier');
	}

	/**
	 * @return array
	 */
	public function getBlockConfig()
	{
		return [
			"content" => $this->getMainContent(),
		];
	}

	/**
	 * @return string
	 */
	public function getReactComponent()
	{
		return $this->retrieveReactComponent(self::REACT_COMPONENT);
	}

	public function getMainContent() 
	{
		return $this->_page->getContent();
	}
}

?>