<?php

namespace Zfloos\Zfloos\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class BeforeOrderPlaceObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $methods = [
            'zfloos'
        ];

        /**
         * @var $order Order
         */
        $order = $observer->getOrder();
        if (!$order) {
            return;
        }

        $payment = $order->getPayment();

        if ($payment && in_array($payment->getMethod(), $methods)) {
            $order->setCanSendNewEmailFlag(false);
        }
    }
}
