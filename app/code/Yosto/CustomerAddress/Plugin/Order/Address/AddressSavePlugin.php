<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAddress\Plugin\Order\Address;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AddressSavePlugin
 * @package Yosto\CustomerAddress\Plugin\Order\Address
 */
class AddressSavePlugin
{
    /**
     * @var AddressRepositoryInterface
     */
    protected $_addressRepository;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var OrderAddressRepositoryInterface
     */
    protected $_orderAddressRepository;

    /**
     * @param AddressRepositoryInterface $addressRepository
     * @param OrderAddressRepositoryInterface $orderAddressRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        OrderAddressRepositoryInterface $orderAddressRepository,
        LoggerInterface $logger
    ) {
        $this->_addressRepository = $addressRepository;
        $this->_orderAddressRepository = $orderAddressRepository;
        $this->_logger = $logger;
    }

    /**
     * @param \Magento\Sales\Controller\Adminhtml\Order\AddressSave $subject
     */
    public function beforeExecute(\Magento\Sales\Controller\Adminhtml\Order\AddressSave $subject)
    {
        $addressId = $subject->getRequest()->getParam('address_id');
        /** @var $address \Magento\Sales\Api\Data\OrderAddressInterface|\Magento\Sales\Model\Order\Address */
        $address = $this->_orderAddressRepository->get($addressId);
        $data = $subject->getRequest()->getPostValue();
        if ($data && $address->getId()) {
            try {
                if ($address->getCustomerAddressId() != null) {

                    $customerAddress = $this->_addressRepository->getById($address->getCustomerAddressId());
                    foreach ($data as $key=>$value) {
                        $customerAddress->setCustomAttribute($key, $value);
                    }
                    $this->_addressRepository->save($customerAddress);
                } else {
                    $addressType = $address->getAddressType();
                    $customShippingAddress = $address->getData('custom_attribute_shipping_address_data');
                    $customBillingAddress = $address->getData('custom_attribute_billing_address_data');
                    //$this->_logger->debug("Address Type: " . $addressType . " | custom data: " . $customShippingAddress . " - " . $customBillingAddress);
                    if ($addressType == "billing" && $customBillingAddress) {

                        $customBillingAttributes= json_decode($customBillingAddress);
                        if (is_array($customBillingAttributes)) {
                            foreach ($customBillingAttributes as $index => $attribute) {
                                $attribute->value = $data[$attribute->name];
                                $customBillingAttributes[$index] = $attribute;
                            }
                        }

                        $address->setData('custom_attribute_billing_address_data', json_encode($customBillingAttributes));

                    }

                    if ($addressType == "shipping" && $customShippingAddress) {
                        $customShippingAttributes= json_decode($customShippingAddress);
                        if (is_array($customShippingAttributes)) {
                            foreach ($customShippingAttributes as $index => $attribute) {
                                $attribute->value = $data[$attribute->name];
                                $customShippingAttributes[$index] = $attribute;
                            }
                        }

                        $address->setData('custom_attribute_shipping_address_data', json_encode($customShippingAttributes));
                    }

                    $this->_orderAddressRepository->save($address);
                }
            } catch (\Exception $e) {
                $this->_logger->error('Admin order update address: ' . $e->getTraceAsString());
            }
        }
    }

}