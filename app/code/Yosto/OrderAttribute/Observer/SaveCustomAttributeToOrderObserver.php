<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\OrderAttribute\Observer;


use Magento\Framework\Event\Observer;

/**
 * Class SaveCustomAttributeToOrderObserver
 * @package Yosto\OrderAttribute\Observer
 */
class SaveCustomAttributeToOrderObserver extends AbstractObserver
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $quoteRepository->get($order->getQuoteId());
        $order->setData('custom_attribute_billing_address_data', $quote->getData('custom_attribute_billing_address_data'));
        $order->setData('custom_attribute_shipping_address_data', $quote->getData('custom_attribute_shipping_address_data'));

        return $this;
    }

}