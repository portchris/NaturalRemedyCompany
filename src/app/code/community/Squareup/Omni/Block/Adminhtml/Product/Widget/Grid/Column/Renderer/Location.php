<?php
/**
 * Locations column renderer
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Block_Adminhtml_Product_Widget_Grid_Column_Renderer_Location
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render location column
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $collection = Mage::getModel('squareup_omni/inventory')
            ->getCollection()
            ->addFieldToFilter('product_id', array('eq' => $row->getEntityId()));

        $locations = array();

        foreach ($collection as $item) {
            $locations[] = $item->getLocationId();
        }

        $collection = Mage::getModel('squareup_omni/location')
            ->getCollection()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('square_id', array('in' => $locations));

        $locations = array();

        foreach ($collection as $item) {
            $locations[] = $item->getName();
        }

        return implode(', ', $locations);
    }

}