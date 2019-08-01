<?php
/**
 * Hero block class for Face & Figure Salon Onepage React Web App
 * 
 * @author      Chris Rogers
 * @package     rwd_faceandfigure_default
 * @since       2019-01-12
*/
class NaturalRemedyCo_FaceAndFigure_Block_Hero extends NaturalRemedyCo_FaceAndFigure_Block_PageAbstract implements NaturalRemedyCo_FaceAndFigure_Block_PageInterface
{
	const REACT_COMPONENT = "hero";

	/**
	 * @var 	Mage_Core_Model_Store
	 */
	public $_storeManager;

	public function __construct() 
	{
		$this->_storeManager = Mage::app()->getStore();
		parent::__construct();	
	}

	/**
	 * @return array
	 */
	public function getBlockConfig()
	{
		$title = Mage::getModel('cms/block')->setStoreId($this->_storeManager->getId())->load('faceandfigure_hero')->getTitle();
		$block = $this->getLayout()->createBlock('cms/block')->setBlockId('faceandfigure_hero');
		return [
			"data" => [
				'title' => $title,
				'content'=> $block->toHtml()
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
}

?>