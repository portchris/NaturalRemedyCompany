<?php

class Squareup_Omni_Block_Adminhtml_Product_Inventory extends Mage_Adminhtml_Block_Abstract
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getLayout()->getBlock('product_tabs')
            ->addTab('squareup_inventory', 'squareup_omni/adminhtml_product_list');
    }
}