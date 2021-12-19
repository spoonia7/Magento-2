<?php
/**
 * @author  X-Mage2 Team
 * @copyright Copyright (c) 2019 X-Mage2 (Yosto) (https://www.x-mage2.com)
 *
 */


namespace Yosto\CustomerAddress\Plugin\Order\Address\Admin;


use Magento\Customer\Api\AddressRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Add custom attributes data to billing form at sales_order_create_index in admin
 *
 * Class BillingAddressFormPlugin
 * @package Yosto\CustomerAddress\Plugin\Order\Address\Admin
 */
class BillingAddressFormPlugin
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
     * @param AddressRepositoryInterface $addressRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        LoggerInterface $logger
    ) {
        $this->_addressRepository = $addressRepository;
        $this->_logger = $logger;
    }

    public function afterGetFormValues(\Magento\Sales\Block\Adminhtml\Order\Create\Billing\Address $subject, $result) {
        $address = $subject->getAddress();

        if($address->getCustomerAddressId() != null) {
            try {
                $customerAddress = $this->_addressRepository->getById($address->getCustomerAddressId());
                $customAttributes = $customerAddress->getCustomAttributes();
                foreach ($customAttributes as $attribute) {
                    $result[$attribute->getAttributeCode()] = $attribute->getValue();

                }
            } catch (\Exception $e) {
                $this->_logger->error('Billing form - when create order in admin: ' . $e->getTraceAsString());
            }
        }

        return $result;
    }
}