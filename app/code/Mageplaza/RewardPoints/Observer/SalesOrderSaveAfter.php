<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_RewardPoints
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\RewardPoints\Observer;

use Exception;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Mageplaza\RewardPoints\Helper\Data as HelperData;
use Psr\Log\LoggerInterface;

/**
 * Class SalesOrderSaveAfter
 * @package Mageplaza\RewardPoints\Observer
 */
class SalesOrderSaveAfter implements ObserverInterface
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SalesOrderSaveAfter constructor.
     *
     * @param LoggerInterface $logger
     * @param HelperData $helperData
     */
    public function __construct(
        LoggerInterface $logger,
        HelperData $helperData
    ) {
        $this->logger     = $logger;
        $this->helperData = $helperData;
    }

    /**
     * @param EventObserver $observer
     * @throws LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        /** @var Order $order */
        $order                = $observer->getEvent()->getOrder();
        $pointAmount          = $this->helperData->getCalculationHelper()
            ->calculatePointOrderCompleteByAction($order, HelperData::ACTION_EARNING_ORDER);
        $baseSubtotalInvoiced = $order->getBaseSubtotalInvoiced();
        $baseSubtotalRefunded = $order->getBaseSubtotalRefunded();

        if (($baseSubtotalInvoiced != $baseSubtotalRefunded || $this->helperData->isEarnPointAfterInvoiceCreated() == 1)
            && $pointAmount) {
            $maxBalance = $this->helperData->getMaxPointPerCustomer($order->getStoreId());
            $account    = $this->helperData->getAccountHelper()->create($order->getCustomerId());
            if ($maxBalance > 0 && $pointAmount > 0
                && ($account->getBalance() + $pointAmount > $maxBalance)) {
                $items           = [];
                $oldTotalEarn    = (int) $order->getMpRewardEarn();
                $availableAmount = $maxBalance - $account->getBalance();

                $order->setMpRewardEarn($availableAmount);

                if ($availableAmount > 0) {
                    foreach ($order->getItems() as $item) {
                        if ($item->getParentItemId()) {
                            continue;
                        }

                        $items[$item->getId()] = ($item->getMpRewardEarn() / $oldTotalEarn);
                    }

                    if (!empty($items)) {
                        $items = $this->calculateRewardEarn($items, $availableAmount);
                        try {
                            $this->updateRewardEarn($order->getItems(), $items);
                        } catch (Exception $e) {
                            $this->logger->critical($e->getMessage());
                        }
                    }
                } else {
                    $order->setMpRewardEarn(0);
                }

                $order->save();
            }

            $this->helperData->addTransaction(
                HelperData::ACTION_EARNING_ORDER,
                $order->getCustomerId(),
                $pointAmount,
                $order
            );
        }
    }

    /**
     * @param $items
     * @param $totalPointEarn
     *
     * @return mixed
     */
    public function calculateRewardEarn($items, $totalPointEarn)
    {
        $i            = 1;
        $balancePoint = 0;
        $lastElement  = count($items);

        foreach ($items as $key => $item) {
            $point        = $item * $totalPointEarn + $balancePoint;
            $balancePoint = $point - (int) $point;
            $items[$key]  = (int) $point;

            if ($i === $lastElement) {
                $items[$key] = round($point);
            }
            $i++;
        }

        return $items;
    }

    /**
     * @param $orderItems
     * @param $items
     *
     * @throws Exception
     */
    public function updateRewardEarn($orderItems, $items)
    {
        /** @var Item $item */
        foreach ($orderItems as $item) {
            if (isset($items[$item->getId()])) {
                $item->setData('mp_reward_earn', $items[$item->getId()]);
                $item->save();
            }
        }
    }
}
