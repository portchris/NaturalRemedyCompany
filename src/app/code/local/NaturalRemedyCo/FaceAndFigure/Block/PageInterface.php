<?php
/**
 * Interface Face and Figure Salon Page block class for Onepage React Web App
 * 
 * @author      Chris Rogers
 * @package     rwd_faceandfigure_default
 * @since       2019-01-12
*/
interface NaturalRemedyCo_FaceAndFigure_Block_PageInterface
{
	/**
	 * The main method that will return all the necessary content from Magento CMS in array format ready to be converted to JSON
	 * @return array
	 */
	public function getBlockConfig();

	/**
	 * The location of the React JS Component
	 * @return string
	 */
	public function getReactComponent();
}

?>