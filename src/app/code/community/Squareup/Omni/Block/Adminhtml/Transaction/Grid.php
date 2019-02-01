<?php
/**
 * Transactions Grid
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Block_Adminhtml_Transaction_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
    * Constructor.
    */
    public function __construct()
    {
        parent::__construct();
        $this->setId('transaction_grid');
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
        $collection = Mage::getModel('squareup_omni/transaction')
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
                'header' => $this->__('Square Transaction Id'),
                'align'  => 'left',
                'type'   => 'text',
                'index'  => 'square_id',
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
            'location_id',
            array(
                'header'    => $this->__('Location'),
                'align'     => 'left',
                'type'      => 'options',
                'renderer'  => 'squareup_omni/adminhtml_transaction_renderer_location',
                'index'     => 'location_id',
                'options'   => Mage::helper('squareup_omni/data')->getLocationsOptionArray(),
                'filter_condition_callback' => array($this, 'filterLocation'),
            )
        );

        $this->addColumn(
            'customer_square_id',
            array(
                'header' => $this->__('Customer Id'),
                'align'  => 'left',
                'type'   => 'text',
                'index'  => 'customer_square_id',
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
            'type',
            array(
                'header'    => $this->__('Type'),
                'align'     => 'left',
                'type'      => 'options',
                'renderer'  => 'squareup_omni/adminhtml_transaction_renderer_type',
                'index'     => 'type',
                'options'   =>  array(
                    Squareup_Omni_Model_Transaction::TYPE_CARD_VALUE
                            => Squareup_Omni_Model_Transaction::TYPE_CARD_LABEL,
                    Squareup_Omni_Model_Transaction::TYPE_CASH_VALUE
                    => Squareup_Omni_Model_Transaction::TYPE_CASH_LABEL
                )
            )
        );

        $this->addColumn(
            'card_brand',
            array(
                'header' => $this->__('Card Brand'),
                'align'  => 'left',
                'type'   => 'text',
                'index'  => 'card_brand',
            )
        );

        $this->addColumn(
            'note',
            array(
                'header' => $this->__('Note'),
                'align'  => 'left',
                'type'   => 'text',
                'index'  => 'note',
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

        $this->addColumn(
            'action',
            array(
                'header'  =>  $this->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => $this->__('Refund'),
                        'url'     => array('base'=> '*/transaction/refundTransaction'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
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
     * Filter location by select
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
}