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

	/**
	 * @var 	Mage_Catalog_Model_Category
	 */
	public $_categoryCollection;

	/**
	 * @var 	array
	 */
	public $_treatments;

	public function __construct()
	{
		$this->_categoryCollection = Mage::getModel('catalog/category');
		$this->_treatments = [];
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
	 * @return array $this->_treatments
	 */
	private function getTreatments()
	{
		$depth = 0;
		$collection = $this->_categoryCollection->getCollection()
			->addNameToResult()
			->addUrlRewriteToResult()
			->addAttributeToFilter('url_key', self::REACT_COMPONENT)
			->getFirstItem();
		$categoryId = $collection->getEntityId();
		$category = $this->_categoryCollection->load($categoryId);
		$this->_treatments[] = $this->setCategoryData($category, $depth);
		$this->createCategoryTree($this->_treatments, $depth, $categoryId);
		return $this->_treatments;
	}

	/**
	 * Convert Magento Category Object into Category Tree For JSON Response
	 * @param 	Mage_Catalog_Model_Category
	 * @return 	array
	 */
	private function setCategoryData($category, $depth)
	{
		$children = ($category->getChildren()) ? explode(",", $category->getChildren()) : [];
		$parentId = ($depth === 0) ? $depth : $category->getParentCategory()->getEntityId();
		return [
			"depth" => $depth,
			"data" => $category->toArray(),
			"parent_id" => $parentId,
			"children" => $children
		];
	}

	/**
	 * @param array $cat (passed by reference)
	 * @param int 	$depth 	The current level in the tree
	 * @param int 	$parentId 	The ID of the parent we wish to set
	 */
	private function createCategoryTree(&$cat, $depth, $parentId)
	{
		foreach ($cat as $key => &$c) {
			if (is_array($c)) {

				// This category data has already been set
				if ($c["parent_id"] == $parentId) {

					// This is the depth we want to set
					$c = $this->setCategoryData($cat, $depth);
					if (!empty($c["children"])) {

						// This child has a children, continue the tree
						$this->createCategoryTree($c["children"], $depth + 1, $parentId);
					}
				} else if (!empty($c["children"])) {

					// This isn't the depth we are looking for, continue down the tree
					$this->createCategoryTree($c["children"], $depth + 1, $parentId);
				}
			} else if (is_numeric($c)) {

				// Here is a valid child, but data is yet to be populated
				$categoryObj = $this->_categoryCollection->load($c);
				if ($categoryObj) {
					$c = $this->setCategoryData($categoryObj, $depth);
					if (!empty($c["children"])) {

						// This child has a children, continue the tree
						$this->createCategoryTree($c["children"], $depth + 1, $parentId);
					}
				}
			} else {

				// Not found, continue down tree
				$this->createCategoryTree($child, $depth + 1, $parentId);
			}
		}
	}
}
 