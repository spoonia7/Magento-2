<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\CustomerAddress\Plugin\Order\Address;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\AttributeMetadataDataProvider;

/**
 * Class RendererPlugin
 * @package Yosto\CustomerAddress\Plugin\Order\Address
 */
class RendererPlugin
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
     * @var AddressFactory
     */
    protected $_addressFactory;


    protected $attributeMetadata;
    /**
     * @param AddressRepositoryInterface $addressRepository
     * @param Registry $coreRegistry
     * @param LoggerInterface $logger
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        AddressFactory $addressFactory,
        AttributeMetadataDataProvider $attributeMetadata,
        Registry $coreRegistry,
        LoggerInterface $logger
    )
    {
        $this->_addressRepository = $addressRepository;
        $this->_addressFactory = $addressFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_logger = $logger;
        $this->attributeMetadata = $attributeMetadata;
    }

    /**
     * Magento 2 uses addresses saved in sales_order_address for sending email.
     * By default, if customer did not create an account, the system will not know
     * value of custom address attributes and what customer address they belong to.
     * So this plugin is used to fix that issue.
     *
     * @param \Magento\Sales\Model\Order\Address\Renderer $subject
     * @param \Magento\Sales\Model\Order\Address $address
     * @param string $type
     */
    public function beforeFormat(
        \Magento\Sales\Model\Order\Address\Renderer $subject,
        \Magento\Sales\Model\Order\Address $address,
        $type
    ) {
        /** @var \Magento\Sales\Model\Order\Address $address */
        try {
            if ($address->getCustomerAddressId() != null) {

                $customerAddress = $this->_addressFactory
                    ->create()
                    ->load($address->getCustomerAddressId());

                $attributes = $this->attributeMetadata->loadAttributesCollection(
                    'customer_address',
                    'customer_register_address'
                );

                foreach ($attributes as $att) {
                    if ($att->getIsUserDefined()) {
                        $address->setData(
                            $att->getAttributeCode(),
                            $customerAddress->getData($att->getAttributeCode())
                        );
                    }
                }


            } else {

                $order = $address->getOrder();
                /**
                 * If a guest checkout and did not create an account,
                 * the system will get data from json string saved in
                 * sales_order_address table
                 */
                if ($address->getAddressType() == $address::TYPE_BILLING) {
                    $customBillingAddressJsonData = $address->getData('custom_attribute_billing_address_data')
                        ? $address->getData('custom_attribute_billing_address_data')
                        : $order->getData('custom_attribute_billing_address_data');
                    $customBillingAddress = json_decode($customBillingAddressJsonData);
                    $isEmptyData = true;
                    if ($customBillingAddressJsonData && is_array($customBillingAddress)) {
                      foreach ($customBillingAddress as $attribute) {
                          if ($attribute->value != "") {
                              $isEmptyData = false;
                          }
                      }
                    }
                    if ($isEmptyData) {
                        $customBillingAddressJsonData = $address->getData('custom_attribute_shipping_address_data')
                            ? $address->getData('custom_attribute_shipping_address_data')
                            : $order->getData('custom_attribute_shipping_address_data');
                        $customBillingAddress = json_decode($customBillingAddressJsonData);
                    }
                    if ($customBillingAddressJsonData && is_array($customBillingAddress)) {
                        foreach ($customBillingAddress as $attribute) {
                            $address->setData(
                                $attribute->name,
                                $attribute->value
                            );
                        }
                    }
                } else {
                    $customShippingAddressJsonData =  $address->getData('custom_attribute_shipping_address_data')
                        ? $address->getData('custom_attribute_shipping_address_data')
                        : $order->getData('custom_attribute_shipping_address_data');
                    $customShippingAddress = json_decode($customShippingAddressJsonData);
                    if ($customShippingAddressJsonData && is_array($customShippingAddress)) {
                        foreach ($customShippingAddress as $attribute) {
                            $address->setData(
                                $attribute->name,
                                $attribute->value
                            );
                        }

                    } else {
                        /**
                         * In backend, after create order, if shipping is the same as billing address, there is no
                         * custom_attribute_shipping_address_data or custom_attribute_billing_address_data.
                         * Registered customer can be used to create order only
                         *
                         * This code block is useful when this case.
                         */
                        $customBillingAddress = $order->getBillingAddress();

                        $customerAddress = $this->_addressFactory
                            ->create()
                            ->load($customBillingAddress->getCustomerAddressId());

                        $attributes = $this->attributeMetadata->loadAttributesCollection(
                            'customer_address',
                            'customer_register_address'
                        );

                        foreach ($attributes as $att) {
                            if ($att->getIsUserDefined()) {
                                $address->setData(
                                    $att->getAttributeCode(),
                                    $customerAddress->getData($att->getAttributeCode())
                                );
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->_logger->error('Billing address for sending email (plugin): ' . $e->getTraceAsString());
        }

    }
}
