<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAddress\Plugin\Order\Address;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Psr\Log\LoggerInterface;

/**
 * Class FormPlugin
 * @package Yosto\CustomerAddress\Block\Adminhtml\Order\Address
 */
class FormPlugin
{
    /**
     * @var AddressRepositoryInterface
     */
    protected $_addressRepository;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @param AddressRepositoryInterface $addressRepository
     * @param Registry $coreRegistry
     * @param LoggerInterface $logger
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        Registry $coreRegistry,
        LoggerInterface $logger
    ) {
        $this->_addressRepository = $addressRepository;
        $this->_coreRegistry = $coreRegistry;
        $this->_logger = $logger;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\Address\Form $subject
     */
    public function beforeGetFormValues(\Magento\Sales\Block\Adminhtml\Order\Address\Form $subject)
    {
        /** @var \Magento\Sales\Model\Order\Address $address */
        $address = $this->_coreRegistry->registry('order_address');
        if($address->getCustomerAddressId() != null) {
            try {
                $customerAddress = $this->_addressRepository->getById($address->getCustomerAddressId());
                $customAttributes = $customerAddress->getCustomAttributes();
                foreach ($customAttributes as $attribute) {
                    $this->_coreRegistry->registry('order_address')->setData(
                        $attribute->getAttributeCode(),
                        $attribute->getValue()
                    );
                }
            } catch (\Exception $e) {
                $this->_logger->error('Order Address Form Plugin: ' . $e->getTraceAsString());
            }
        } else {
            try {
                $customShippingAddress = $address->getData('custom_attribute_shipping_address_data');
                $customBillingAddress = $address->getData('custom_attribute_billing_address_data');
                $addressType = $address->getAddressType();
                //$this->_logger->debug("Address Type: " . $addressType . " | custom data: " . $customShippingAddress . " - " . $customBillingAddress);
                $customAttributes = [];
                if ($addressType == "billing" && $customBillingAddress) {

                    $customAttributes = json_decode($customBillingAddress);

                } if ($addressType == "shipping" && $customShippingAddress) {

                    $customAttributes = json_decode($customShippingAddress);

                }


                if (is_array($customAttributes)) {
                    foreach ($customAttributes as  $attribute) {
                        if ($attribute && $attribute->value) {
                            $this->_coreRegistry->registry('order_address')->setData(
                                $attribute->name,
                                $attribute->value
                            );
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->_logger->error('Order Address Form Plugin: ' . $e->getTraceAsString());
            }
        }

    }

}