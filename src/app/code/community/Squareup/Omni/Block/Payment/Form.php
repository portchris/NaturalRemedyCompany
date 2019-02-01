<?php
/**
 * SquareUp
 *
 * Form Block
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Block_Payment_Form extends Mage_Payment_Block_Form
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('squareup/payment/form.phtml');
    }

    public function isOnePageCheckout()
    {
        $controllerName = Mage::app()->getRequest()->getControllerName();
        $moduleName = Mage::app()->getRequest()->getModuleName();

        return $controllerName == 'onepage' && $moduleName == 'checkout';
    }
}

/* Filename: Form.php */
/* Location: app/code/community/Squareup/Omni/Block/Payment/Form.php */