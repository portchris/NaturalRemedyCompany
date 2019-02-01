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
class Squareup_Omni_Block_Adminhtml_System_Config_Button_Webhooks extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('squareup/system/config/webhook_button.phtml');
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
                    'id' => 'squareup_omni_webhooks_settings_webhook_button',
                    'label' => $this->helper('squareup_omni')->__('Subscribe webhooks'),
                    'onclick' => "submitWebhooks()"
                )
            );

        return $button->toHtml();
    }

    public function getWebhooksUrl()
    {
        return $this->getUrl('adminhtml/square/subscribe');
    }

}

/* Filename: Oauth.php */
/* Location: app/code/community/Squareup/Omni/Block/Adminhtml/System/Config/Button/Oauth.php */