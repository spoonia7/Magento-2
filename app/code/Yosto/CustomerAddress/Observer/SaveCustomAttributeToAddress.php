<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAddress\Observer;


use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Magento\Sales\Model\Order\AddressFactory as OrderAddressFactory;
use Yosto\CustomerAddress\Observer\Adminhtml\SaveCustomAttributeToAddress as SaveAttributesToAddressAdmin;
/**
 * Class SaveCustomAttributeToAddress
 * @package Yosto\CustomerAddress\Observer
 */
class SaveCustomAttributeToAddress extends SaveAttributesToAddressAdmin
{
    /**
     * @param $jsonData
     * @return bool
     */
    public function isNewAddress($jsonData)
    {
        try {
            foreach ($jsonData as $node) {
                if ($node->value != null && $node->value != '') {
                    return true;
                }
            }
        } catch (\Exception $e) {
            $this->_logger->debug('Incorrect JSON data:' . json_encode($jsonData));
            return false;
        }
        return false;
    }

    /**
     * @param $entityId
     * @param $customAddressData
     */
    public function updateCustomerAddress($entityId, $customAddressData)
    {
        $customerAddress = $this->_addressFactory->create()->load($entityId);
        try {
            $data = $customAddressData;
            $addressData = $customerAddress->getDataModel();
            foreach ($data as $item) {
                $addressData->setCustomAttribute($item->name, $item->value);
            }
            $customerAddress->updateData($addressData);
            $customerAddress->save();

        } catch (\Exception $e) {
            $this->_logger->debug($e->getTraceAsString());
        }
    }

    /**
     * @param $order
     * @param $quote
     * @param $customShippingAddress
     * @param $customBillingAddress
     * @param $shippingCustomerAddressId
     * @param $billingCustomerAddressId
     * @param $isShippingSaveInAddressBook
     * @param $isBillingSaveInAddressBook
     */
    public function saveCustomAttributesToAddress(
        $order,
        $quote,
        $customShippingAddress,
        $customBillingAddress,
        $shippingCustomerAddressId,
        $billingCustomerAddressId,
        $isShippingSaveInAddressBook,
        $isBillingSaveInAddressBook

    ) {
       // Magento 2.1.2 and later version always set customer address id for a quote address.
        $customShippingAddress = json_decode($customShippingAddress);
        $customBillingAddress = json_decode($customBillingAddress);
        if ($this->isNewAddress($customBillingAddress)) {
            if ($isBillingSaveInAddressBook) {
                $this->updateCustomerAddress($billingCustomerAddressId, $customBillingAddress);
                $this->updateOrderAddress(
                    $order,
                    $order->getId(),
                    true,
                    $billingCustomerAddressId,
                    $order->getCustomerId(),
                    $quote->getBillingAddress()->getId()
                );
            } else {
                /**
                 * In case, billing address is the same as shipping address
                 * The system will assign value of shipping_address_id to customer_address_id
                 * in billing address at sales_order_address table
                 *
                 * Issue has not fixed yet: billing address is different from shipping address,
                 * but customer did not save billing address in address book. Magento core did not
                 * update same_as_billing field in quote_address, so can not know whether or not
                 * addresses are the same.
                 */
                $this->updateOrderAddress(
                    $order,
                    $order->getId(),
                    true,
                    $shippingCustomerAddressId,
                    $order->getCustomerId(),
                    $quote->getBillingAddress()->getId()
                );
            }
        }

        if ($this->isNewAddress($customShippingAddress)) {
            if ($isShippingSaveInAddressBook) {
                $this->updateCustomerAddress($shippingCustomerAddressId, $customShippingAddress);
                $this->updateOrderAddress(
                    $order,
                    $order->getId(),
                    false,
                    $shippingCustomerAddressId,
                    $order->getCustomerId(),
                    $quote->getShippingAddress()->getId()
                );
            }
        }
    }


    /**
     * @param Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $observer->getOrder();
        $quote = $observer->getQuote();
        $customBillingAddress = $quote->getData('custom_attribute_billing_address_data');
        $customShippingAddress = $quote->getData('custom_attribute_shipping_address_data');
        if ($customBillingAddress == null && $customShippingAddress == null ) {
            parent::execute($observer);
        } else {

            if ($order->getData('customer_is_guest') != 1) {
                $shippingCustomerAddressId = $quote->getShippingAddress()->getData('customer_address_id');
                $isShippingSaveInAddressBook = $quote->getShippingAddress()->getData('save_in_address_book');

                $billingCustomerAddressId = $quote->getBillingAddress()->getData('customer_address_id');
                $isBillingSaveInAddressBook = $quote->getBillingAddress()->getData('save_in_address_book');

                    $this->saveCustomAttributesToAddress(
                        $order,
                        $quote,
                        $customShippingAddress,
                        $customBillingAddress,
                        $shippingCustomerAddressId,
                        $billingCustomerAddressId,
                        $isShippingSaveInAddressBook,
                        $isBillingSaveInAddressBook
                    );


            } else {
                /**
                 * Magento core did not save customer address if customer is a guest,
                 * so we need to save json data of custom attributes to sales_order_address table
                 */
                $this->updateOrderAddress(
                    $order,
                    $order->getId(),
                    true,
                    null,
                    $order->getCustomerId(),
                    $quote->getBillingAddress()->getId()
                );
                $this->updateOrderAddress(
                    $order,
                    $order->getId(),
                    false,
                    null,
                    $order->getCustomerId(),
                    $quote->getShippingAddress()->getId()
                );
            }

        }


    }


}