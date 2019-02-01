<?php
/**
 * SquareUp
 *
 * Customer Mapping Model
 *
 * @category  Squareup
 * @package   Squareup_Omni
 * @copyright 2018
 * @author    SquareUp
 */
class Squareup_Omni_Model_Customer_Import extends Mage_Core_Model_Abstract
{
    /**
     * Square Api Client
     *
     * @var \SquareConnect\ApiClient
     */
    protected $_apiClient;

    /**
     * Log Helper
     *
     * @var Squareup_Omni_Helper_Log
     */
    protected $_logHelper;

    /**
     * @var Squareup_Omni_Helper_Data
     */
    protected $_helper;

    protected $_squareCustomers = array();

    /**
     * Squareup_Omni_Model_Customer_Export_Export constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_logHelper = Mage::helper('squareup_omni/log');
        $this->_helper = Mage::helper('squareup_omni');
        $this->_apiClient = $this->_helper->getClientApi();
    }

    /**
     * List all customers from SquareUp App
     *
     * @param $cursor
     * @return bool
     */
    public function getSquareupCustomers($cursor = null)
    {
        try {
            $api = new SquareConnect\Api\CustomersApi($this->_apiClient);
            $response =  $api->listCustomers($cursor);
            $customers = $response->getCustomers();
            $customersCursor = $response->getCursor();
            if (!empty($customers)) {
                foreach ($customers as $customer) {
                    $this->_squareCustomers[] = $customer->getId();
                    $this->importCustomer($customer);
                }
            }

            if (!empty($customersCursor)) {
                $this->getSquareupCustomers($customersCursor);
            }

            /*Check if exists customers to delete from Magento */
            $customersAlreadySync = $this->getCustomersSquareIds();
            $customersToDelete = array_diff($customersAlreadySync, $this->_squareCustomers);
            if (!empty($customersToDelete)) {
                /* Delete customers from Magento */
                $deleteModel = Mage::getModel('squareup_omni/customer_delete');
                $deleteModel->deleteMagentoCustomers($customersToDelete);
            }
        } catch (\SquareConnect\ApiException $e) {
            $this->_logHelper->error($e->__toString());
            Mage::throwException($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Save customer from SquareUp in Magento
     *
     * @param $squareupCustomer
     * @return bool
     */
    public function importCustomer($squareupCustomer)
    {
        try {
            $websites = Mage::getModel('core/website')->getCollection()
                ->addFieldToFilter('is_default', 1);
            $website = $websites->getFirstItem();
            $websiteId = $website->getId();

            $customer = Mage::getModel('customer/customer')->setWebsiteId(1);
            $referenceId = $squareupCustomer->getReferenceId();
            if (!empty($referenceId)) {
                $customer->load($squareupCustomer->getReferenceId());
            }

            $customerId = $customer->getId();
            if (empty($customerId)) {
                $customer->loadByEmail($squareupCustomer->getEmailAddress());
            }

            if (!empty($customerId) && $squareupCustomer->getUpdatedAt() == $customer->getSquareUpdatedAt()) {
                $this->_logHelper->info(
                    $this->_helper->__(
                        'No modification for customer Square Id:%s, Magento Id:%s, skip.',
                        $squareupCustomer->getId(),
                        $customer->getId()
                    )
                );
                return true;
            }

            $customer->setWebsiteId($websiteId)
                ->setGroupId(1)
                ->setFirstname($squareupCustomer->getGivenName())
                ->setLastname($squareupCustomer->getFamilyName())
                ->setEmail($squareupCustomer->getEmailAddress())
                ->setSquareupCustomerId($squareupCustomer->getId())
                ->setSquareUpdatedAt($squareupCustomer->getUpdatedAt())
                ->setSquareupJustImported(2);

            $nickname = $squareupCustomer->getNickname();
            if (!empty($nickname)) {
                $customer->setMiddleName($squareupCustomer->getNickname());
            }

            $squareCustomerCards = $squareupCustomer->getCards();
            $customerSquareSavedCards = $customer->getSquareSavedCards();
            if (empty($squareCustomerCards) && !empty($customerSquareSavedCards)) {
                $customer->setSquareSavedCards(null);
            }

            $customerCards = null;
            if (!empty($squareCustomerCards)) {
                if (!empty($customerSquareSavedCards)) {
                    $alreadySavedCards = json_decode($customer->getSquareSavedCards(), true);
                    $apiCards = array();
                    foreach ($squareupCustomer->getCards() as $card) {
                        $apiCards[] = $card->getId();
                        if (array_key_exists($card->getId(), $alreadySavedCards)) {
                            continue;
                        }

                        $alreadySavedCards[$card->getId()] = array(
                            'card_brand' => $card->getCardBrand(),
                            'last_4' => $card->getLast4(),
                            'exp_month' => $card->getExpMonth(),
                            'exp_year' => $card->getExpYear(),
                            'cardholder_name' => $card->getCardholderName()
                        );
                    }

                    $cardsToSave = array();
                    foreach ($apiCards as $apiCard) {
                        $cardsToSave[$apiCard] = $alreadySavedCards[$apiCard];
                    }

                    $customerCards = json_encode($cardsToSave);
                } else {
                    $squareCards = array();
                    foreach ($squareupCustomer->getCards() as $card) {
                        $squareCards[$card->getId()] = array(
                            'card_brand' => $card->getCardBrand(),
                            'last_4' => $card->getLast4(),
                            'exp_month' => $card->getExpMonth(),
                            'exp_year' => $card->getExpYear(),
                            'cardholder_name' => $card->getCardholderName()
                        );
                    }

                    $customerCards = json_encode($squareCards);
                }

                $customer->setSquareSavedCards($customerCards);
            }


            $customer->save();
            $squareupAddress = $squareupCustomer->getAddress();
            if (count(array_filter((array)$squareupAddress)) > 2) {
                $addressId = $customer->getData('default_billing');
                $address = Mage::getModel("customer/address");
                if (!empty($addressId)) {
                    $address->load($addressId);
                }

                $address->setCustomerId($customer->getId())
                    ->setFirstname($customer->getFirstname())
                    ->setMiddleName($customer->getMiddlename())
                    ->setLastname($customer->getLastname())
                    ->setCountryId($squareupAddress->getCountry())
                    ->setPostcode($squareupAddress->getPostalCode())
                    ->setCity($squareupAddress->getLocality())
                    ->setTelephone($squareupCustomer->getPhoneNumber())
                    ->setStreet($squareupAddress->getAddressLine1())
                    ->setIsDefaultBilling('1')
                    ->setIsDefaultShipping('1')
                    ->setSaveInAddressBook('1');
                $administrativeDistrictLevel1 = $squareupAddress->getAdministrativeDistrictlevel1();
                if (!empty($administrativeDistrictLevel1)) {
                    $region = Mage::getModel('directory/region')
                        ->loadByCode(
                            $squareupAddress->getAdministrativeDistrictlevel1(),
                            $squareupAddress->getCountry()
                        );
                    $stateId = $region->getId();
                    $address->setRegionId($stateId);
                }

                $address->save();
            }


            /* If customer is new send to SquareUp ReferenceId */
            $squareCustomerReferenceId = $squareupCustomer->getReferenceId();
            if (empty($squareCustomerReferenceId)) {
                $exportModel = Mage::getModel('squareup_omni/customer_export_export');
                $exportModel->sendSquareupCustomerReferenceId(
                    $customer->getSquareupCustomerId(),
                    $customer->getId()
                );
                $this->_logHelper->info(
                    $this->_helper->__(
                        'Created received customer from Square $Id:%s, Magento Id:%s',
                        $squareupCustomer->getId(),
                        $customer->getId()
                    )
                );
            } else {
                $this->_logHelper->info(
                    $this->_helper->__(
                        'Updated received customer from Square Id:%s, Magento Id:%s',
                        $squareupCustomer->getId(),
                        $customer->getId()
                    )
                );
            }
        } catch (Exception $e) {
            $this->_logHelper->error($e->__toString());
            return false;
        }

        return true;
    }

    /**
     * Get Square Id for all customers from Magento
     *
     * @return array
     */
    public function getCustomersSquareIds()
    {
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('squareup_customer_id', 'left')
            ->addAttributeToFilter('squareup_customer_id', array('notnull' => true))
            ->load();

        $customerSquareIds = array();
        foreach ($collection as $item) {
            $customerSquareIds[$item->getId()] = $item->getData('squareup_customer_id');
        }

        return $customerSquareIds;
    }
}