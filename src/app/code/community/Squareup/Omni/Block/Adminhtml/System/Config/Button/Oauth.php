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
class Squareup_Omni_Block_Adminhtml_System_Config_Button_Oauth extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public $squareUrl = 'https://connect.squareup.com/oauth2/';
    protected $_scope = 'PAYMENTS_WRITE%20PAYMENTS_READ%20CUSTOMERS_READ%20CUSTOMERS_WRITE%20ORDERS_READ%20ORDERS_WRITE%20MERCHANT_PROFILE_READ%20ITEMS_READ%20ITEMS_WRITE%20INVENTORY_READ%20INVENTORY_WRITE';
    protected $_session = 'false';
    protected $_state;

    protected function _construct()
    {
        parent::_construct();
        $this->_state = hash('sha384', Mage::getBaseUrl() . Mage::getSingleton('core/date')->gmtTimestamp());
        $fh = new SplFileObject(Mage::getBaseDir('var') . '/onlytoken.txt', 'w');
        $fh->fwrite($this->_state);
        $fh = null;
        $this->setTemplate('squareup/system/config/oauth.phtml');
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
                    'id' => 'squareup_omni_general_application_oauth',
                    'label' => $this->helper('squareup_omni')->__('Get OAuth Token'),
                    'onclick' => "openConnection()"
                )
            );

        return $button->toHtml();
    }

    public function buildOauthUrl()
    {
        return $this->squareUrl . 'authorize?scope=' . $this->_scope . '&session=' . $this->_session .
            '&state=' . $this->_state . '&client_id=';
    }

    public function getRevokeButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'id' => 'squareup_omni_general_application_oauth_revoke',
                    'label' => $this->helper('squareup_omni')->__('Revoke OAuth Token'),
                    'onclick' => "openRevoke()"
                )
            );

        return $button->toHtml();
    }

    public function getRevokeUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/square/revoke');
    }
}

/* Filename: Oauth.php */
/* Location: app/code/community/Squareup/Omni/Block/Adminhtml/System/Config/Button/Oauth.php */