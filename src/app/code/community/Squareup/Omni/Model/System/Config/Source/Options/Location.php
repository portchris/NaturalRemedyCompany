<?php
/**
 * SquareUp
 *
 * Locations Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */

class Squareup_Omni_Model_System_Config_Source_Options_Location
{
    protected $_optionsArray = array();

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $collection = Mage::getModel('squareup_omni/location')
            ->getResourceCollection()
            ->addFieldToFilter('status', 1);
        $this->_optionsArray = array();
        if (!empty($collection)) {
            foreach ($collection as $item) {
                $this->_optionsArray[] = array(
                    'label' => $item->getName(),
                    'value'=> $item->getSquareId()
                );
            }

            array_unshift(
                $this->_optionsArray,
                array(
                    'value'=>'', 'label' => Mage::helper('squareup_omni')->__('Please select location')
                )
            );
        }

        return $this->_optionsArray;
    }

    /**
     * Returning the available locations
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * Get option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $collection = Mage::getModel('squareup_omni/location')
            ->getResourceCollection()
            ->addFieldToFilter('status', array('eq' => 1));

        if (!empty($collection)) {
            foreach ($collection as $item) {
                $this->_optionsArray[$item->getSquareId()] = $item->getName();
            }
        }

        return $this->_optionsArray;
    }
}

/* Filename: Action.php */
/* Location: app/code/community/Squareup/Omni/Model/System/Config/Source/Options/Action.php */