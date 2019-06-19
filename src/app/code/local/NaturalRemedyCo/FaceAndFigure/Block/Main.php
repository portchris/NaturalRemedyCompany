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
		$title1 = Mage::getModel('cms/block')->setStoreId(Mage::app()->getStore()->getId())->load('faceandfigure_desc_1')->getTitle();
		$title2 = Mage::getModel('cms/block')->setStoreId(Mage::app()->getStore()->getId())->load('faceandfigure_desc_2')->getTitle();
		$block1 = $this->getLayout()->createBlock('cms/block')->setBlockId('faceandfigure_desc_1');
		$block2 = $this->getLayout()->createBlock('cms/block')->setBlockId('faceandfigure_desc_2');
		return [
			"content" => $this->getMainContent(),
			"description" => [
				["title" => $title1, "content" => $block1->toHtml()],
				["title" => $title2, "content" => $block2->toHtml()]
			]
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