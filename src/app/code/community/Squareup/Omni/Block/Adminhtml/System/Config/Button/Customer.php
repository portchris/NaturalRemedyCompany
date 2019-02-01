<?php
/**
 * SquareUp
 *
 * Oauth Block
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Block_Adminhtml_System_Config_Button_Customer extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('squareup/system/config/button.phtml');
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml($element);
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'id' => 'squareup_omni_customer_run_customer_sync',
                    'label' => $this->helper('squareup_omni')->__('Run'),
                    'onclick' => "location.href='" . $this->getRequiredUrl() . "'"
                )
            );

        return $button->toHtml();
    }

    public function getRequiredUrl()
    {
        return $this->getUrl('adminhtml/square/customer');
    }

}

/* Filename: Oauth.php */
/* Location: app/code/community/Squareup/Omni/Block/Adminhtml/System/Config/Button/Oauth.php */