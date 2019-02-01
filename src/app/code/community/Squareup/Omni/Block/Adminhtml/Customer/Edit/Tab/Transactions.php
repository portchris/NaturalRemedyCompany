<?php

class Squareup_Omni_Block_Adminhtml_Customer_Edit_Tab_Transactions
    extends Squareup_Omni_Block_Adminhtml_Transaction_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Columns, that should be removed from grid
     *
     * @var array
     */
    protected $_columnsToRemove = array(
        'customer_square_id',
        'action'
    );

    /**
     * Disable filters and paging
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_edit_tab_square_transactions');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Square Transactions');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Square Transactions');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');
        return (bool)$customer->getId();
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

    public function getGridUrl()
    {
        return $this->getUrl('*/transaction/customerGrid', array('_current'=>true));
    }

    /**
     * Defines after which tab, this tab should be rendered
     *
     * @return string
     */
    public function getAfter()
    {
        return 'tags';
    }

    /**
     * Prepare collection for grid
     *
     * @return Mage_Sales_Block_Adminhtml_Customer_Edit_Tab_Agreement
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('squareup_omni/transaction')->getCollection()
            ->addFieldToFilter('customer_square_id', Mage::registry('current_customer')->getSquareupCustomerId());

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    /**
     * Retrieve grid export types
     *
     * @return array|false
     */
    public function getExportTypes()
    {
        return false;
    }
    
    /**
     * Remove some columns and make other not sortable
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $result = parent::_prepareColumns();

        foreach ($this->_columns as $key => $value) {
            if (in_array($key, $this->_columnsToRemove)) {
                unset($this->_columns[$key]);
            }
        }

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
                        'url'     => array('base'=> '*/transaction/refundTransactionCustomer/customer_id/'.
                            Mage::registry('current_customer')->getId()),
                        'field'   => 'trans_id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );

        return $result;
    }
}
