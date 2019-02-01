<?php
/**
 * Render refunds status
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Block_Adminhtml_Refunds_Renderer_Status extends
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
        $type = $this->_getValue($row);
        switch ($type) {
            case Squareup_Omni_Model_Refunds::STATUS_PENDING_VALUE:
                $type = Squareup_Omni_Model_Refunds::STATUS_PENDING_LABEL;
                break;
            case Squareup_Omni_Model_Refunds::STATUS_APPROVED_VALUE:
                $type = Squareup_Omni_Model_Refunds::STATUS_APPROVED_LABEL;
                break;
            case Squareup_Omni_Model_Refunds::STATUS_REJECTED_VALUE:
                $type = Squareup_Omni_Model_Refunds::STATUS_REJECTED_LABEL;
                break;
            case Squareup_Omni_Model_Refunds::STATUS_FAILED_VALUE:
                $type = Squareup_Omni_Model_Refunds::STATUS_FAILED_LABEL;
                break;
            default:
        }

        return $type;
    }
}
