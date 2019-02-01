<?php
/**
 * Refunds Grid
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Block_Adminhtml_Refunds_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('refunds_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Setup the collection to show in the grid.
     *
     * @Override
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('squareup_omni/refunds')
            ->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Setup the shown columns.
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                'header' => $this->__('ID'),
                'align'  => 'left',
                'type'   => 'number',
                'index'  => 'id',
                'width'  => '50px',
            )
        );

        $this->addColumn(
            'square_id',
            array(
                'header' => $this->__('Square Refund Id'),
                'align'  => 'left',
                'type'   => 'text',
                'index'  => 'square_id',
            )
        );

        $this->addColumn(
            'location_id',
            array(
                'header'    => $this->__('Location'),
                'align'     => 'left',
                'type'      => 'options',
                'renderer'  => 'squareup_omni/adminhtml_refunds_renderer_location',
                'index'     => 'location_id',
                'options'   => Mage::helper('squareup_omni')->getLocationsOptionArray(),
                'filter_condition_callback' => array($this, 'filterLocation'),
            )
        );

        $this->addColumn(
            'transaction_id',
            array(
                'header' => $this->__('Transaction Id'),
                'align'  => 'left',
                'type'   => 'text',
                'index'  => 'transaction_id',
            )
        );

        $this->addColumn(
            'tender_id',
            array(
                'header' => $this->__('Tender Id'),
                'align'  => 'left',
                'type'   => 'text',
                'index'  => 'tender_id',
            )
        );

        $this->addColumn(
            'amount',
            array(
                'header'=> $this->__('Amount'),
                'type'  => 'price',
                'currency_code' => Mage::app()->getStore()->getDefaultCurrencyCode(),
                'index' => 'amount',
            )
        );

        $this->addColumn(
            'processing_fee_amount',
            array(
                'header'=> $this->__('Processing Fee Amount'),
                'type'  => 'price',
                'currency_code' => Mage::app()->getStore()->getDefaultCurrencyCode(),
                'index' => 'processing_fee_amount',
            )
        );
        $this->addColumn(
            'reason',
            array(
                'header' => $this->__('Reason'),
                'align'  => 'left',
                'type'   => 'text',
                'index'  => 'reason',
            )
        );

        $this->addColumn(
            'status',
            array(
                'header'    => $this->__('Status'),
                'align'     => 'left',
                'type'      => 'options',
                'renderer'  => 'squareup_omni/adminhtml_refunds_renderer_status',
                'index'     => 'status',
                'options'   =>  array(
                    Squareup_Omni_Model_Refunds::STATUS_PENDING_VALUE
                            => Squareup_Omni_Model_Refunds::STATUS_PENDING_LABEL,
                    Squareup_Omni_Model_Refunds::STATUS_APPROVED_VALUE
                            => Squareup_Omni_Model_Refunds::STATUS_APPROVED_LABEL,
                    Squareup_Omni_Model_Refunds::STATUS_REJECTED_VALUE
                            => Squareup_Omni_Model_Refunds::STATUS_REJECTED_LABEL,
                    Squareup_Omni_Model_Refunds::STATUS_FAILED_VALUE
                            => Squareup_Omni_Model_Refunds::STATUS_FAILED_LABEL
                ),
                'filter_condition_callback' => array($this, 'filterStatus'),
            )
        );

        $this->addColumn(
            'created_at',
            array(
                'header' => $this->__('Created at'),
                'align'  => 'center',
                'type'   => 'datetime',
                'index'  => 'created_at',
                'width'  => '150px',
            )
        );

        //export to csv and excel
        $this->addExportType('*/*/exportCsv', $this->__('CSV'));
        $this->addExportType('*/*/exportExcel', $this->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Grid url.
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * Filter location
     *
     * @return object
     */
    protected function filterLocation($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $collection->addFieldToFilter('location_id', $value);

        return $this;
    }

    /**
     * Filter status
     *
     * @return object
     */
    protected function filterStatus($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $collection->addFieldToFilter('status', $value);

        return $this;
    }
}