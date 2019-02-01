<?php

class Squareup_Omni_Block_Adminhtml_Product_List
    extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_inventory;

    /**
     * Initialize block
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setProductId($this->getRequest()->getParam('id'));
        $this->setTemplate('squareup/product/list.phtml');
        $this->setId('squareup_list');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('squareup_omni')->__('Square Inventory');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('squareup_omni')->__('Square Location Inventory');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    protected function getProduct()
    {
        return Mage::registry('current_product');
    }

    public function getLocations()
    {
        $locationArr = array();
        $locations = Mage::getModel('squareup_omni/location')->getCollection()->addFieldToFilter('status', 1);
        foreach ($locations as $location) {
            $locationArr[$location->getSquareId()] = $location->getName();
        }

        return $locationArr;
    }

    public function getInventory()
    {
        $this->_inventory = Mage::getModel('squareup_omni/inventory')->getCollection()
                        ->addFieldToFilter('product_id', array('eq' => $this->getProduct()->getId()));

        $locationTable = Mage::getSingleton('core/resource')->getTableName('squareup_omni/location');
        $this->_inventory->getSelect()
            ->joinLeft(
                array('location'=> $locationTable),
                "main_table.location_id = location.square_id"
            )
            ->where('location.status =?', 1);
        return $this->_inventory;
    }

    public function getNewLocations()
    {
        $inventoryArr = array();
        foreach ($this->_inventory as $item) {
            $inventoryArr[] = $item->getLocationId();
        }

        $locations = Mage::getModel('squareup_omni/location')->getCollection()
            ->addFieldToFilter('status', array('eq' => 1));

        if (!empty($inventoryArr)) {
            $locations->addFieldToFilter('square_id', array('nin' => $inventoryArr));
        }

        return $locations;
    }
}
