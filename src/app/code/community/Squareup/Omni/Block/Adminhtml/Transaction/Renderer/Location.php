<?php
/**
 * Render transaction type
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Block_Adminhtml_Transaction_Renderer_Location extends
    Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $squareId = $this->_getValue($row);
        $location = Mage::getModel('squareup_omni/location')->load($squareId, 'square_id');
        if ($location && $location->getId()) {
            return $location->getName();
        }

        return $squareId;
    }
}
