<?php
/**
 * SquareUp
 *
 * Index Controller
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_WebhooksController extends Mage_Core_Controller_Front_Action
{

    public function notifyAction()
    {
        if ('POST' !== $this->getRequest()->getMethod()) {
            return $this->_redirect('/');
        }

        $body = $this->getRequest()->getRawBody();
        $signature = $this->getRequest()->getHeader('X-Square-Signature');
        if (true !== $this->isRequestValid($body, $signature)) {
            return $this->_redirect('/');
        }

        $notification = json_decode($body);
        Mage::helper('squareup_omni/log')->info($body);
        switch ($notification->event_type) {
            case 'INVENTORY_UPDATED':
                if (Mage::helper('squareup_omni/config')->getSor() !=
                    Squareup_Omni_Model_System_Config_Source_Options_Records::SQUARE) {
                    break;
                }

                try {
                    Mage::getModel('squareup_omni/cron')->startInventory();
                } catch (Exception $e) {
                    Mage::helper('squareup_omni/log')->error($e->__toString());
                }

                break;
            case 'PAYMENT_UPDATED':
                try {
                    Mage::getModel('squareup_omni/cron')->startTransactionsImport(true);
                    Mage::getModel('squareup_omni/cron')->startRefundsImport(true);
//                    Mage::getModel('squareup_omni/transaction_import')->singleTransaction(
//                        $notification->location_id,
//                        $notification->entity_id
//                    );
                } catch (Exception $e) {
                    Mage::helper('squareup_omni/log')->error($e->__toString());
                }

                break;
            case 'TIMECARD_UPDATED':
                Mage::helper('squareup_omni/log')->info($body);
                break;
            default:
                Mage::helper('squareup_omni/log')->info($body);
        }

        return $this->getResponse()->setBody("Done");
    }

    protected function isRequestValid($requestBody, $requestSignature)
    {
        $webhookSignatureKey = Mage::helper('squareup_omni/config')->getWebhookSignature();
        $webhookUrl = Mage::getBaseUrl('web') . 'square_magento/webhooks/notify/';

        $stringToSign = $webhookUrl . $requestBody;
        $stringSignature = base64_encode(hash_hmac('sha1', $stringToSign, $webhookSignatureKey, true));

        return (sha1($stringSignature) === sha1($requestSignature));
    }
}

/* Filename: IndexController.php */
/* Location: app/code/community/Squareup/Omni/controllers/IndexController.php */