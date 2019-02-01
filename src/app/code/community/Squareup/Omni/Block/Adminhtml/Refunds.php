<?php
/**
 * Refunds grid container.
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Block_Adminhtml_Refunds extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Init class
     */
    public function __construct()
    {
        $this->_blockGroup = 'squareup_omni';
        $this->_controller = 'adminhtml_refunds';
        $this->_headerText = $this->__('Square Refunds');
        parent::__construct();
        $this->_removeButton('add');
    }
}