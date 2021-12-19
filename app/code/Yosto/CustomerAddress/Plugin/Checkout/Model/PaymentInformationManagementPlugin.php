<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAddress\Plugin\Checkout\Model;

/**
 * Class PaymentInformationManagementPlugin
 * @package Yosto\CustomerAddress\Model\Checkout
 */
class PaymentInformationManagementPlugin
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $_quoteRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_logger = $logger;
        $this->_quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Checkout\Model\PaymentInformationManagement $subject
     * @param $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentInformation
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Model\PaymentInformationManagement $subject,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentInformation,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        try {
            $customData = $billingAddress->getExtensionAttributes()->getCustomData();
            $quote = $this->_quoteRepository->getActive($cartId);
            $sameAsBilling = $billingAddress->getSameAsBilling();
            if ($sameAsBilling == 1) {
                $quote->setData('custom_attribute_billing_address_data', $quote->getData('custom_attribute_shipping_address_data'));
            } else {
                $quote->setData('custom_attribute_billing_address_data', $customData);
            }
        } catch (\Exception $e) {
            $this->_logger->debug($e->getTraceAsString());
        }
    }
}