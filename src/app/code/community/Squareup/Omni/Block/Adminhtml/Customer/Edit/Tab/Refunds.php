<?php

class Squareup_Omni_Block_Adminhtml_Customer_Edit_Tab_Refunds
    extends Squareup_Omni_Block_Adminhtml_Refunds_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Columns, that should be removed from grid
     *
     * @var array
     */
    protected $_columnsToRemove = array();

    /**
     * Disable filters and paging
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_edit_tab_square_refunds');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Square Refunds');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Square Refunds');
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
        return $this->getUrl('*/refunds/customerGrid', array('_current'=>true));
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
        $collection = Mage::getModel('squareup_omni/refunds')->getCollection();
        $transactionTable = Mage::getSingleton('core/resource')->getTableName('squareup_omni/transaction');
        $collection->getSelect()
            ->joinLeft(
                array('transactions' => $transactionTable),
                "main_table.tender_id = transactions.tender_id",
                array(
                    'customer_square_id' => 'transactions.customer_square_id'
                )
            )
            ->where('customer_square_id = ?', Mage::registry('current_customer')->getSquareupCustomerId());

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

        return $result;
    }
}
