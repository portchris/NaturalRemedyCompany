<?php
/**
 * SquareUp
 *
 * Card Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Card extends Squareup_Omni_Model_Square
{
    /**
     * @var \SquareConnect\ApiClient
     */
    protected $_apiClient;
    protected $_alreadySavedCards = array();
    protected $_customerId;

    /**
     * Define Card on File values
     */
    const DISALLOW_CARD_ON_FILE = 0;
    const ALLOW_CARD_ON_FILE = 1;
    const ALLOW_ONLY_CARD_ON_FILE = 2;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_apiClient = $this->_helper->getClientApi();
    }

    /**
     * Send customer card to square and return card_id
     * @param $customerId
     * @param $alreadySavedCards
     * @param $squareCustomerId
     * @param $request
     * @return bool|string
     */
    public function sendSaveCard($customerId, $alreadySavedCards, $squareCustomerId, $request)
    {
        try{
            $this->_alreadySavedCards = $alreadySavedCards;
            $this->_customerId = $customerId;
            $api = new SquareConnect\Api\CustomersApi($this->_apiClient);
            $response = $api->createCustomerCard($squareCustomerId, $request);
            $errors = $response->getErrors();
            if (empty($errors)) {
                $this->_log->info(
                    'Credit card id:' . $response->getCard()->getId() . ' saved for customer ' .
                    $customerId . ' / square_id:' . $squareCustomerId
                );
                $this->processCardResponse($response->getCard());
                return $response->getCard()->getId();
            }
        } catch (\SquareConnect\ApiException $e) {
            $this->_log->error($e->__toString());
            Mage::throwException($e->getMessage());
            return false;
        }

        return false;
    }

    /**
     * Save card info to magento from square card response
     * @param $cardResponse
     */
    public function processCardResponse($cardResponse)
    {
        if (!empty($this->_alreadySavedCards)) {
            $alreadySavedCards = json_decode($this->_alreadySavedCards, true);
        }

        $alreadySavedCards[$cardResponse->getId()] = array(
            'card_brand' => $cardResponse->getCardBrand(),
            'last_4' => $cardResponse->getLast4(),
            'exp_month' => $cardResponse->getExpMonth(),
            'exp_year' => $cardResponse->getExpYear(),
            'cardholder_name' => $cardResponse->getCardholderName()
        );

        $squareCards = json_encode($alreadySavedCards);
        $customer = Mage::getModel('customer/customer');
        $customer->setId($this->_customerId);
        $customer->setSquareSavedCards($squareCards);

        try {
            $this->_log->info(
                'Saving customer card in Magento id:' . $cardResponse->getId() . ' customer '. $this->_customerId
            );
            $customer->getResource()->saveAttribute($customer, 'square_saved_cards');
            $this->_log->info(
                $customer->getSquareSavedCards()
            );
        } catch (Exception $e) {
            $this->_log->error($e->__toString());
        }
    }

    public function checkCcUpdates()
    {
        $customer = $this->_helper->getCustomer();
        if ($customer && $customer->getId()) {
            try{
                $api = new SquareConnect\Api\CustomersApi($this->_apiClient);
                $response = $api->retrieveCustomer($customer->getSquareupCustomerId());
                $responseErrors = $response->getErrors();
                if (empty($responseErrors)) {
                    $squareCards = $response->getCustomer()->getCards();
                    if (empty($squareCards)) {
                        $customer->setId($customer->getId());
                        $customer->setSquareSavedCards(null);
                        $customer->getResource()->saveAttribute($customer, 'square_saved_cards');
                        return true;
                    }

                    $magentoCc = array();
                    foreach ($squareCards as $card) {
                        $magentoCc[$card->getId()] = array(
                            'card_brand' => $card->getCardBrand(),
                            'last_4' => $card->getLast4(),
                            'exp_month' => $card->getExpMonth(),
                            'exp_year' => $card->getExpYear(),
                            'cardholder_name' => $card->getCardholderName()
                        );
                    }

                    $squareCards = json_encode($magentoCc);
                    $customer->setId($customer->getId());
                    $customer->setSquareSavedCards($squareCards);
                    $customer->getResource()->saveAttribute($customer, 'square_saved_cards');
                    return true;
                }
            } catch (\SquareConnect\ApiException $e) {
                $this->_log->error($e->__toString());
                Mage::throwException($e->getMessage());
                return false;
            }
        }

        return false;
    }
}

/* Filename: Card.php */
/* Location: app/code/community/Squareup/Omni/Model/Card.php */