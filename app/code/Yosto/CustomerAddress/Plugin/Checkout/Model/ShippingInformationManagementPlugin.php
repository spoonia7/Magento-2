<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAddress\Plugin\Checkout\Model;

use Magento\Customer\Model\AddressFactory;

class ShippingInformationManagementPlugin
{

    protected $_quoteRepository;
    protected $_logger;
    protected $_addressFactory;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        AddressFactory $addressFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_logger = $logger;
        $this->_addressFactory = $addressFactory;
        $this->_quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes();
        $customData = $extAttributes->getCustomData();
        $quote = $this->_quoteRepository->getActive($cartId);
        $quote->setData('custom_attribute_shipping_address_data',$customData);
//        $customShippingAddress = json_decode($customData);
//        foreach ($customShippingAddress as $item) {
//            $quote->getShippingAddress()->setCustomAttribute($item->name, $item->value);
//        }
    }


}