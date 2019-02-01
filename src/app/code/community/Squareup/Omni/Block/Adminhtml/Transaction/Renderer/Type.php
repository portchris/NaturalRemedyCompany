<?php
/**
 * Render transaction type
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Block_Adminhtml_Transaction_Renderer_Type extends
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
            case Squareup_Omni_Model_Transaction::TYPE_CARD_VALUE:
                $typeLabel =  Squareup_Omni_Model_Transaction::TYPE_CARD_LABEL;
                break;
            case Squareup_Omni_Model_Transaction::TYPE_CASH_VALUE:
                $typeLabel =  Squareup_Omni_Model_Transaction::TYPE_CASH_LABEL;
                break;
            default:
                $typeLabel = $type;
        }

        return $typeLabel;
    }
}
