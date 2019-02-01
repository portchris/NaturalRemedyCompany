<?php

class Squareup_Omni_Model_Transaction_Shipping
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'square_shipping';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $result = Mage::getModel('shipping/rate_result');
        $result->append($this->_getDefaultRate());

        if (Mage::app()->getStore()->isAdmin()) {
            return $result;
        }

        if (Mage::getDesign()->getArea() == 'adminhtml') {
            return $result;
        }

        return false;

    }

    public function getAllowedMethods()
    {
        return array(
            'square_shipping' => 'square_shipping',
        );
    }

    protected function _getDefaultRate()
    {
        $rate = Mage::getModel('shipping/rate_result_method');

        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod($this->_code);
        $rate->setMethodTitle($this->getConfigData('title'));
        $rate->setPrice(0);
        $rate->setCost(0);

        return $rate;
    }
}
