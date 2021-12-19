<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\CustomerAddress\Plugin\Controller\Account;


use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Magento\Sales\Model\Order\AddressFactory as OrderAddressFactory;
/**
 * Save custom attribute to address if guest create an account.
 *
 * Class CheckoutCreateAccountPlugin
 * @since 2.3.1
 * @package Yosto\CustomerAddress\Plugin\Controller\Account
 */
class CheckoutCreateAccountPlugin
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var OrderAddressRepositoryInterface
     */
    protected $_orderAddressRepository;

    /**
     * @var OrderAddressFactory
     */
    protected $_orderAddressFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Psr\Log\LoggerInterface $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository ,
        OrderAddressRepositoryInterface $orderAddressRepository,
        OrderAddressFactory $orderAddressFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->_addressFactory = $addressFactory;
        $this->_orderRepository = $orderRepository;
        $this->_objectManager = $objectManager;
        $this->_orderAddressRepository = $orderAddressRepository;
        $this->_orderAddressFactory = $orderAddressFactory;
        $this->_logger = $logger;
    }

    /**
     * Update sales order addresses after guest create account
     *
     * @param $orderId
     * @param $isBillingAddress
     * @param $customerAddressId
     * @param $customerId
     * @param $quoteId
     */
    public function updateOrderAddress(
        $orderId,
        $isBillingAddress,
        $customerAddressId,
        $customerId,
        $quoteId
    ) {

        try{
            $orderBillingAddress = $this->_orderAddressFactory->create()
                ->getCollection()
                ->addFieldToFilter('parent_id', $orderId)
                ->addFieldToFilter('address_type', 'billing')
                ->getFirstItem();

            $orderShippingAddress = $this->_orderAddressFactory->create()
                ->getCollection()
                ->addFieldToFilter('parent_id', $orderId)
                ->addFieldToFilter('address_type', 'shipping')
                ->getFirstItem();
            if ($isBillingAddress) {
                if ($orderBillingAddress != null) {
                    $orderBillingAddress->setData('customer_address_id', $customerAddressId);
                    $orderBillingAddress->setData('customer_id', $customerId);
                    $orderBillingAddress->setData('quote_id', $quoteId);
                    $this->_orderAddressRepository->save($orderBillingAddress);
                }
            } else {
                if ($orderShippingAddress != null) {
                    $orderShippingAddress->setData('customer_address_id', $customerAddressId);
                    $orderShippingAddress->setData('customer_id', $customerId);
                    $orderShippingAddress->setData('quote_id', $quoteId);
                    $this->_orderAddressRepository->save($orderShippingAddress);
                }
            }
        } catch (\Exception $e) {
            $this->_logger->error('Could not update order addresses');
        }
    }

    /**
     * After guest create an account. custom attribute will be saved to address entity
     *
     * @param \Magento\Checkout\Controller\Account\Create $subject
     */
    public function afterExecute(\Magento\Checkout\Controller\Account\Create $subject)
    {

        if ($this->customerSession->isLoggedIn()) {
            return;
        }

        $orderId = $this->checkoutSession->getLastOrderId();
        if (!$orderId) {
            return;
        }
        try {
            $order = $this->_orderRepository->get($orderId);
            $customerId = $order->getCustomerId();
            $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
            $quote = $quoteRepository->get($order->getQuoteId());
            $customBillingAddress = $quote->getData('custom_attribute_billing_address_data');
            $customShippingAddress = $quote->getData('custom_attribute_shipping_address_data');
            $shippingCustomerAddressId = $quote->getShippingAddress()->getData('customer_address_id');
            // $isShippingSaveInAddressBook = $quote->getShippingAddress()->getData('save_in_address_book');

            $billingCustomerAddressId = $quote->getBillingAddress()->getData('customer_address_id');
            //  $isBillingSaveInAddressBook = $quote->getBillingAddress()->getData('save_in_address_book');
            // $isSameAsBilling = $quote->getShippingAddress()->getData('same_as_billing');


            if (!$shippingCustomerAddressId && !$billingCustomerAddressId) {
                $shippingAddress = $this->_addressFactory->create();
                $billingAddress = $this->_addressFactory->create();

                $addresses = $this->_addressFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('parent_id', $customerId)
                    ->addOrder('entity_id', 'desc')->setPageSize(2);
                $billingAddress->load($addresses->getFirstItem()->getId());
                $shippingAddress->load($addresses->getLastItem()->getId());

                try {
                    if($customBillingAddress != null) {
                        $billingData = json_decode($customBillingAddress);
                        $billingAddressData = $billingAddress->getDataModel();
                        foreach ($billingData as $item) {
                            $billingAddressData->setCustomAttribute($item->name, $item->value);
                        }
                        $billingAddress->updateData($billingAddressData);
                        $billingAddress->save();

                        $this->updateOrderAddress(
                            $orderId,
                            true,
                            $billingAddress->getId(),
                            $order->getCustomerId(),
                            $order->getQuoteId()
                        );
                    }
                    if($customShippingAddress !=null) {
                        $shippingData = json_decode($customShippingAddress);
                        $shippingAddressData = $shippingAddress->getDataModel();
                        foreach ($shippingData as $item) {
                            $shippingAddressData->setCustomAttribute($item->name, $item->value);
                        }
                        $shippingAddress->updateData($shippingAddressData);
                        $shippingAddress->save();
                        $this->updateOrderAddress(
                            $orderId,
                            false,
                            $shippingAddress->getId(),
                            $order->getCustomerId(),
                            $order->getQuoteId()
                        );
                    }

                } catch (\Exception $e) {
                    $this->_logger->debug('Save new billing & shipping address fail:'.$e->getTraceAsString());
                }

            } elseif ($shippingCustomerAddressId && !$billingCustomerAddressId) {

                $customerAddress = $this->_addressFactory->create();

                $address = $this->_addressFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('parent_id', $customerId)
                    ->addOrder('entity_id', 'desc')->getFirstItem();

                $customerAddress->load($address->getId());

                try {

                    $data = json_decode($customBillingAddress);
                    $addressData = $customerAddress->getDataModel();
                    foreach ($data as $item) {
                        $addressData->setCustomAttribute($item->name, $item->value);
                    }
                    $customerAddress->updateData($addressData);
                    $customerAddress->save();
                    $this->updateOrderAddress(
                        $orderId,
                        true,
                        $customerAddress->getId(),
                        $order->getCustomerId(),
                        $order->getQuoteId()
                    );
                } catch (\Exception $e) {
                    $this->_logger->debug($e->getTraceAsString());
                }

            } elseif (!$shippingCustomerAddressId && $billingCustomerAddressId) {
                $customerAddress = $this->_addressFactory->create();

                $address = $this->_addressFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('parent_id', $customerId)
                    ->addOrder('entity_id', 'desc')->getFirstItem();

                $customerAddress->load($address->getId());
                try {
                    if($customShippingAddress !=null) {
                        $data = json_decode($customShippingAddress);
                        $addressData = $customerAddress->getDataModel();
                        foreach ($data as $item) {
                            $addressData->setCustomAttribute($item->name, $item->value);
                        }
                        $customerAddress->updateData($addressData);
                        $customerAddress->save();
                        $this->updateOrderAddress(
                            $orderId,
                            false,
                            $customerAddress->getId(),
                            $order->getCustomerId(),
                            $order->getQuoteId()
                        );
                    }

                } catch (\Exception $e) {
                    $this->_logger->debug($e->getTraceAsString());
                }

            } elseif ($shippingCustomerAddressId && $billingCustomerAddressId) {

            }
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->_objectManager->get(\Magento\Framework\Controller\Result\JsonFactory::class)->create();
            return $resultJson->setData(
                [
                    'errors' => false,
                    'message' => __('A letter with further instructions will be sent to your email.')
                ]
            );
        } catch (\Exception $e) {
            $this->_logger->debug($e->getTraceAsString());
        }
    }
}