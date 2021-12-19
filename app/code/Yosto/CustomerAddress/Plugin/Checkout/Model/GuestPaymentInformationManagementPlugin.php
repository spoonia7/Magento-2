<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\CustomerAddress\Plugin\Checkout\Model;

/**
 * Save custom address billing data to database in quote table
 *
 * Class GuestPaymentInformationManagementPlugin
 * @since 2.3.1
 * @package Yosto\CustomerAddress\Model\Checkout
 */
class GuestPaymentInformationManagementPlugin
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $_quoteFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $_quoteIdMarkFactory;

    /**
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->_logger = $logger;
        $this->_quoteFactory = $quoteFactory;
        $this->_quoteIdMarkFactory = $quoteIdMaskFactory;
    }

    /**
     * @param \Magento\Checkout\Model\GuestPaymentInformationManagement $subject
     * @param $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentInformation
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Model\GuestPaymentInformationManagement $subject,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentInformation,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $customData = $billingAddress->getExtensionAttributes()->getCustomData();
        $quoteId = $this->_quoteIdMarkFactory
            ->create()->load($cartId, 'masked_id')->getQuoteId();
        $sameAsBilling = $billingAddress->getSameAsBilling();
        $quote = $this->_quoteFactory->create()->load($quoteId);
        if ($sameAsBilling == 1) {
            $quote->setData('custom_attribute_billing_address_data', $quote->getData('custom_attribute_shipping_address_data'));
        } else {
            $quote->setData('custom_attribute_billing_address_data', $customData);
        }

        $quote->save();
    }
}