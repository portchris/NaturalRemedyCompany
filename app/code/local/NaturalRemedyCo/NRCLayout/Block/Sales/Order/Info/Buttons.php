<?php 
/**
 * Add refund button to order buttons
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Block of links in Order view page
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Chris Rogers <chris@portchris.co.uk>
 */
class NaturalRemedyCo_NRCLayout_Block_Sales_Order_Info_Buttons extends Mage_Sales_Block_Order_Info_Buttons
{
	protected function _construct() {
		parent::_construct();
		$this->setTemplate('nrc_layout/sales/order/info/buttons.phtml');
	}
}