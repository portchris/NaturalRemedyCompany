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

	public $_categoryCollection;

	public function __construct()
	{
		$this->_categoryCollection = Mage::getModel('catalog/category');
		parent::__construct();
	}

	/**
	 * @return array
	 */
	public function getBlockConfig()
	{
		return $this->getTreatments();
	}

	/**
	 * @return string
	 */
	public function getReactComponent()
	{
		return $this->retrieveReactComponent(self::REACT_COMPONENT);
	}

	/**
	 * Get Treatments from categories
	 * @return array $t
	 */
	private function getTreatments()
	{
		$d = 0;
		$treatments = [];
		$collection = $this->_categoryCollection->getCollection()
			->addNameToResult()
			->addUrlRewriteToResult()
			->addAttributeToFilter('url_key', self::REACT_COMPONENT)
			->getFirstItem();
		$categoryId = $collection->getEntityId();
		do {
			$t = [];
			$category = $this->_categoryCollection->load($categoryId);
			$children = ($category->getChildren()) ? explode(",", $category->getChildren()) : [];
			$parent = ($d === 0) ? $d : $category->getParentCategory()->getEntityId();
			$t[] = [
				"depth" => $d,
				"data" => $category->getData(),
				"children" => $children,
				"parent" => $parent
			];
			$treatments = array_merge($treatments, $t);
			if ($children) {
				$t = $this->_categoryCollection->load($children[0]);
				$d++;
			} else {
				$t = $parent;
				$d--;
			}
			break;
		} while ($t !== 0 && $d >= 0);
		return $treatments;
		
	}
}
?>