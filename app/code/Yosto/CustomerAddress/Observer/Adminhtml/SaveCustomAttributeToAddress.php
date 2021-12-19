<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\CustomerAddress\Observer\Adminhtml;


use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Magento\Sales\Model\Order\AddressFactory as OrderAddressFactory;

/**
 * Class SaveCustomAttributeToAddress
 * @package Yosto\CustomerAddress\Observer\Adminhtml
 */
class SaveCustomAttributeToAddress implements ObserverInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var AddressRepositoryInterface
     */
    protected $_addressRepository;

    /**
     * @var
     */
    protected $_productMetadata;
    /**
     * @var OrderAddressFactory
     */
    protected $_orderAddressFactory;

    /**
     * @var OrderAddressRepositoryInterface
     */
    protected $_orderAddressRepository;

    /**
     * @var \Yosto\CustomerAttribute\Helper\Data
     */
    protected $_configData;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     * @param AddressFactory $addressFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param \Psr\Log\LoggerInterface $logger
     * @param ProductMetadata $productMetadata
     * @param OrderAddressRepositoryInterface $orderAddressRepository
     * @param OrderAddressFactory $orderAddressFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        AddressFactory $addressFactory,
        AddressRepositoryInterface $addressRepository,
        \Psr\Log\LoggerInterface $logger,
        ProductMetadata $productMetadata,
        OrderAddressRepositoryInterface $orderAddressRepository,
        OrderAddressFactory $orderAddressFactory,
        \Yosto\CustomerAttribute\Helper\Data $configData
    ) {
        $this->_addressRepository = $addressRepository;
        $this->_addressFactory = $addressFactory;
        $this->_logger = $logger;
        $this->_productMetadata = $productMetadata;
        $this->_objectManager = $objectmanager;
        $this->_orderAddressRepository = $orderAddressRepository;
        $this->_orderAddressFactory = $orderAddressFactory;
        $this->_configData = $configData;
    }

    /**
     * @param $order
     * @param $orderId
     * @param $isBillingAddress
     * @param $customerAddressId
     * @param $customerId
     * @param $quoteAddressId
     */
    public function updateOrderAddress(
        $order,
        $orderId,
        $isBillingAddress,
        $customerAddressId,
        $customerId,
        $quoteAddressId
    ) {

        try{
            if ($isBillingAddress) {
                $orderBillingAddress = $this->_orderAddressFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('parent_id', $orderId)
                    ->addFieldToFilter('address_type', 'billing')
                    ->getFirstItem();
                if ($orderBillingAddress != null && $order->getData('custom_attribute_billing_address_data')) {
                    $orderBillingAddress->setData('customer_address_id', $customerAddressId);
                    $orderBillingAddress->setData('customer_id', $customerId);
                    $orderBillingAddress->setData('quote_address_id', $quoteAddressId);
                    $orderBillingAddress->setData(
                        'custom_attribute_billing_address_data',
                        $order->getData('custom_attribute_billing_address_data')
                    );
                    $this->_orderAddressRepository->save($orderBillingAddress);
                }
            } else {

                $orderShippingAddress = $this->_orderAddressFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('parent_id', $orderId)
                    ->addFieldToFilter('address_type', 'shipping')
                    ->getFirstItem();
                if ($orderShippingAddress != null && $order->getData('custom_attribute_shipping_address_data')) {
                    $orderShippingAddress->setData('customer_address_id', $customerAddressId);
                    $orderShippingAddress->setData('customer_id', $customerId);
                    $orderShippingAddress->setData('quote_address_id', $quoteAddressId);
                    $orderShippingAddress->setData(
                        'custom_attribute_shipping_address_data',
                        $order->getData('custom_attribute_shipping_address_data')
                    );
                    $this->_orderAddressRepository->save($orderShippingAddress);
                }
            }
        } catch (\Exception $e) {
            $this->_logger->error('Could not update order addresses');
        }
    }

    /**
     * @param $entityId
     * @param $customAddressData
     */
    public function updateCustomerAddressAdminOrder($entityId, $customAddressData)
    {
        $customerAddress = $this->_addressFactory->create()->load($entityId);
        try {
            if ($customerAddress->getId()) {
                $data = $customAddressData;
                $addressData = $customerAddress->getDataModel();
                foreach ($data as $key => $value) {
                    if (is_scalar($value)) {
                        $addressData->setCustomAttribute($key, $value);
                    }
                }
                $customerAddress->updateData($addressData);
                $customerAddress->save();
            }
        } catch (\Exception $e) {
            $this->_logger->debug($e->getTraceAsString());
        }
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Quote\Model\Quote $quote
     */
    public function saveCustomAttributesForAddress(
        $order,
        $quote
    ) {
        $billingAddress = $quote->getBillingAddress();
        $billingCustomerAddressId = $billingAddress->getData('customer_address_id');

        $this->updateCustomerAddressAdminOrder($billingCustomerAddressId, $billingAddress->getData());
        $this->updateOrderAddress(
            $order,
            $order->getData('entity_id'),
            true,
            $billingCustomerAddressId,
            $quote->getCustomerId(),
            $billingAddress->getId()
        );
        $shippingAddress = $quote->getShippingAddress();
        $shippingCustomerAddressId = $shippingAddress->getData('customer_address_id');
        $this->updateCustomerAddressAdminOrder($shippingCustomerAddressId, $shippingAddress->getData());
        $this->updateOrderAddress(
            $order,
            $order->getData('entity_id'),
            false,
            $shippingCustomerAddressId,
            $quote->getCustomerId(),
            $shippingAddress->getId()
        );
    }


    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $observer->getOrder();
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getQuote();
        $this->saveCustomAttributesForAddress($order, $quote);
    }
}