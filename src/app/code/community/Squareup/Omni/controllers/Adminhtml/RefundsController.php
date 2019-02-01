<?php
/**
 * Square Refunds controller.
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Adminhtml_RefundsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Display transactions.
     */
    public function indexAction()
    {
        $this->_title($this->__('Refunds'))->_title($this->__('Refunds'));
        $this->loadLayout();
        $this->_setActiveMenu('sales');
        $this->_addContent($this->getLayout()->createBlock('squareup_omni/adminhtml_refunds'));
        $this->renderLayout();
    }

    /**
     * Grid Action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('squareup_omni/adminhtml_refunds_grid')->toHtml()
        );
    }

    /**
     * Export refunds grid to csv
     */
    public function exportCsvAction()
    {
        $fileName = 'square_refunds.csv';
        $grid = $this->getLayout()->createBlock('squareup_omni/adminhtml_refunds_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export refunds grid to excel
     */
    public function exportExcelAction()
    {
        $fileName = 'square_refunds.xml';
        $grid = $this->getLayout()->createBlock('squareup_omni/adminhtml_refunds_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/squareup_omni/refunds');
    }

    /**
     * Customer square refunds ajax action
     *
     */
    public function customerGridAction()
    {
        $this->_initCustomer();
        $this->loadLayout(false)
            ->renderLayout();
    }

    /**
     * Initialize customer by ID specified in request
     *
     * @return Squareup_Omni_Adminhtml_RefundsController
     */
    protected function _initCustomer()
    {
        $customerId = (int) $this->getRequest()->getParam('id');
        $customer = Mage::getModel('customer/customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);
        return $this;
    }
}