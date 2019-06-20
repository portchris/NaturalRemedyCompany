<?php
/**
 * Contact block class for Face & Figure Salon Onepage React Web App
 * 
 * @author      Chris Rogers
 * @package     rwd_faceandfigure_default
 * @since       2019-01-12
*/
class NaturalRemedyCo_FaceAndFigure_Block_Contact extends NaturalRemedyCo_FaceAndFigure_Block_PageAbstract implements NaturalRemedyCo_FaceAndFigure_Block_PageInterface
{
	const PAGE_KEY = "contact-face-and-figure-salon";
	const REACT_COMPONENT = "contact";

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
		$title1 = Mage::getModel('cms/block')->setStoreId(Mage::app()->getStore()->getId())->load('contact_us_why_faceandfigure')->getTitle();
		$title2 = Mage::getModel('cms/block')->setStoreId(Mage::app()->getStore()->getId())->load('contact_us_tech_faceandfigure')->getTitle();
		$title3 = Mage::getModel('cms/block')->setStoreId(Mage::app()->getStore()->getId())->load('contact_us_private_faceandfigure')->getTitle();
		$block1 = $this->getLayout()->createBlock('cms/block')->setBlockId('contact_us_why_faceandfigure')->toHtml();
		$block2 = $this->getLayout()->createBlock('cms/block')->setBlockId('contact_us_tech_faceandfigure')->toHtml();
		$block3 = $this->getLayout()->createBlock('cms/block')->setBlockId('contact_us_private_faceandfigure')->toHtml();		 
		return [
			"title" => $this->getContactTitle(), 
			"content" => $this->getContactContent(),
			"info" => [
				["title" => $title1, "content" => $block1],
				["title" => $title2, "content" => $block2],
				["title" => $title3, "content" => $block3]
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

	public function geContactContent() 
	{
		return $this->_page->getContent();
	}

	public function getContactTitle()
	{
		return $this->_page->getTitle();
	}
}
?>