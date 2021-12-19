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
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Mageplaza\RewardPoints\Helper\Data as HelperData;

/**
 * Class CreditmemoRefundSaveAfter
 * @package Mageplaza\RewardPoints\Observer
 */
class CreditmemoRefundSaveAfter implements ObserverInterface
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var bool
     */
    private $isRefund = false;

    /**
     * CreditmemoRefundSaveAfter constructor.
     *
     * @param HelperData $helperData
     * @param RequestInterface $request
     */
    public function __construct(
        HelperData $helperData,
        RequestInterface $request
    ) {
        $this->helperData = $helperData;
        $this->request    = $request;
    }

    /**
     * @param EventObserver $observer
     *
     * @throws Exception
     */
    public function execute(EventObserver $observer)
    {
        /* @var $creditmemo Creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order      = $creditmemo->getOrder();
        $data       = $this->request->getPost('creditmemo');

        if (!$this->isRefund) {
            $this->isRefund = true;
            if ($creditmemo->getMpRewardEarn() > 0) {
                $this->helperData->addTransaction(
                    HelperData::ACTION_EARNING_REFUND,
                    $creditmemo->getOrder()->getCustomerId(),
                    -$creditmemo->getMpRewardEarn(),
                    $creditmemo->getOrder()
                );
                $earnedPoints = abs($order->getMpRewardEarn() - $creditmemo->getMpRewardEarn());
                $order->setMpRewardEarn($earnedPoints)->save();
            }

            if ($creditmemo->getMpRewardSpent() > 0
                && isset($data['is_refund_point'])
                && (int) $data['refund_point'] > 0
            ) {
                $spendPoint = min($data['refund_point'], $creditmemo->getMpRewardSpent());

                if ($data['refund_point'] === $spendPoint) {
                    $orderPoint = abs($order->getMpRewardSpent() - $spendPoint);
                    $order->setMpRewardSpent($orderPoint)->save();
                    $invoice = $creditmemo->getInvoice();

                    if ($invoice) {
                        $invoice->setMpRewardSpent($orderPoint)->save();
                    }
                    $creditmemo->setMpRewardSpent($spendPoint)->save();
                }

                $this->helperData->addTransaction(
                    HelperData::ACTION_SPENDING_REFUND,
                    $creditmemo->getOrder()->getCustomerId(),
                    $spendPoint,
                    $creditmemo->getOrder()
                );
            }
            $baseSubtotalInvoiced = round($creditmemo->getOrder()->getBaseSubtotalInvoiced(), 2);
            $baseSubtotalRefunded = round($creditmemo->getOrder()->getBaseSubtotalRefunded(), 2);
            if ($baseSubtotalInvoiced === $baseSubtotalRefunded) {
                $order->setState(Order::STATE_CLOSED)
                    ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_CLOSED));
            }
        }
    }
}
