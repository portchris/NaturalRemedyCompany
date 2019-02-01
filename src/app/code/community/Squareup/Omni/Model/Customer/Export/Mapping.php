<?php
/**
 * SquareUp
 *
 * Customer Mapping Model
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */
class Squareup_Omni_Model_Customer_Export_Mapping extends Mage_Core_Model_Abstract
{
    public $customerData = array();

    /**
     * Return an array with customers response from SquareUp
     * @return array
     */
    public function getNotExportedCustomers()
    {
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('squareup_customer_id', 'left')
            ->addAttributeToFilter('squareup_customer_id', array('null' => true))
            ->load();

        Mage::getSingleton('core/resource_iterator')->walk(
            $collection->getSelect(), array(array($this, 'processCustomer'))
        );

        return $this->customerData;
    }

    /**
     * Process Customer
     * @param $args
     * @return mixed
     */
    public function processCustomer($args)
    {
        if (is_array($args)) {
            $customerId = $args['row']['entity_id'];
        } else {
            $customerId = $args;
        }

        $customer = Mage::getModel('customer/customer')->load($customerId);
        $address = $customer->getPrimaryBillingAddress();
        if ($customer && $customer->getId()) {
            if (empty($address)) {
                $address = Mage::getModel('customer/address')->load($customer->getDefaultBilling());
            }

            $customerData = array(
                'given_name' => $customer->getFirstname(),
                'family_name' => $customer->getLastname(),
                'email_address' => $customer->getEmail(),
                'reference_id' => $customer->getId(),
                'note' => 'customer from Magento'
            );
            if (!empty($address) && count($address->getData()) > 1) {
                $region = Mage::getModel('directory/region')->load($address->getRegionId());
                $customerData['address'] = array(
                        'address_line_1' => $address->getData('street'),
                        'locality' => $address->getCity(),
                        'administrative_district_level_1' => $region->getCode(),
                        'postal_code' => $address->getPostcode(),
                        'country' => $address->getCountryId()
                    );
                $customerData['phone_number'] = $address->getTelephone();
                $digits = preg_match_all( "/[0-9]/", $address->getTelephone());

                if ($digits < 9 || $digits > 16) {
                    Mage::helper('squareup_omni/log')
                        ->error('SquareUp Error: Customers telephone format is incorrect.');
                    return null;
                }
            } else {
                Mage::helper('squareup_omni/log')
                    ->error('SquareUp Error: No default address selected.');
            }

            if (!is_array($args)) {
                return $customerData;
            }

            $this->customerData[] = $customerData;
        }
    }

    /**
     * Map not processed customer
     * @param $customer
     * @return array
     */
    public function mapNewCustomer($customer)
    {
        $address = $customer->getPrimaryBillingAddress();
        if (empty($address)) {
            $address = Mage::getModel('customer/address')->load($customer->getDefaultBilling());
        }

        $customerData = array(
            'given_name' => $customer->getFirstname(),
            'family_name' => $customer->getLastname(),
            'email_address' => $customer->getEmail(),
            'reference_id' => $customer->getId(),
            'note' => 'customer from Magento'
        );

        if (!empty($address) && count($address->getData()) > 1) {
            $region = Mage::getModel('directory/region')->load($address->getRegionId());
            $customerData['address'] = array(
                'address_line_1' => $address->getData('street'),
                'locality' => $address->getCity(),
                'administrative_district_level_1' => $region->getCode(),
                'postal_code' => $address->getPostcode(),
                'country' => $address->getCountryId()
            );
            $customerData['phone_number'] = $address->getTelephone();
            $digits = preg_match_all( "/[0-9]/", $address->getTelephone());

            if ($digits < 9 || $digits > 16) {
                Mage::helper('squareup_omni/log')
                    ->error('SquareUp Error: Customers telephone format is incorrect.');
                return null;
            }
        } else {
            Mage::helper('squareup_omni/log')
                ->error('SquareUp Error: No default address selected.');
        }

        return $customerData;
    }
}