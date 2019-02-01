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
class Squareup_Omni_Block_Adminhtml_System_Config_Button_Message extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $htmlId = $element->getHtmlId();
        $id = str_replace('squareup_omni_', '', $htmlId);
        switch ($id){
            case 'general_sandbox_documentation':
                $this->setTemplate('squareup/system/config/sandbox_documentation.phtml');
                break;
            case 'oauth_settings_oauth_message':
                $this->setTemplate('squareup/system/config/oauth_message.phtml');
                break;
            case 'oauth_settings_redirect_url':
                $this->setTemplate('squareup/system/config/redirect.phtml');
                break;
            case 'webhooks_settings_webhook_url':
                $this->setTemplate('squareup/system/config/webhook.phtml');
                break;
            case 'catalog_images_size':
                $this->setTemplate('squareup/system/config/images_size.phtml');
                break;
            default:
                $this->setTemplate('squareup/system/config/message.phtml');
        }

        return $this->_toHtml();
    }

    public function getElementHtml()
    {
        $element = $this->getLayout()->createBlock('core/template');
        return $element->toHtml();
    }

}

/* Filename: Oauth.php */
/* Location: app/code/community/Squareup/Omni/Block/Adminhtml/System/Config/Button/Oauth.php */
