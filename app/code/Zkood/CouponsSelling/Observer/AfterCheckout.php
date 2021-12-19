<?php

namespace Zkood\CouponsSelling\Observer;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\Event\ObserverInterface;
use Zkood\CouponsSelling\Model\Mail;
use Zkood\CouponsSelling\Service\CouponService;

class AfterCheckout implements ObserverInterface
{

    /**
     * @var CustomerCollectionFactory
     */
    private $collectionFactory;
    /**
     * @var \Zkood\CouponsSelling\Service\CouponService
     */
    private $couponService;
    /**
     * @var \Zkood\CouponsSelling\Model\Mail
     */
    private $mail;

    public function __construct(
        Mail $mail,
        CouponService $couponService,
        CustomerCollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->couponService = $couponService;
        $this->mail = $mail;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getOrder();

        $mailData = ['data' => [
            'customerEmail' => $order->getCustomerEmail(),
            'customerName' => $order->getCustomerName(),
            'orderId' => $order->getIncrementId(),
            'orderCreatedAt' => $order->getCreatedAt(),
            'couponsData' => []
        ]];

        $orderHasCoupons = false;

        foreach ($order->getAllItems() as $item) {
            $seller = $item->getProduct()->getAttributeText('seller');
            if (!$seller) {
                continue;
            }
            $orderHasCoupons = true;
            $sellerCode = substr($seller, 0, 5);
            $sellerEntity = $this->collectionFactory->create()
                ->addAttributeToSelect('seller_code')
                ->addAttributeToFilter('seller_code', ['like' => $sellerCode . '%'])
                ->load()->getFirstItem();
            if ($sellerId = $sellerEntity->getId()) {
                for ($i = 1; $i <= $item->getQtyOrdered(); $i++) {
                    $couponEntity = $this->couponService->generateCoupon($sellerCode, $sellerId, $item->getProduct()->getId() ,$item->getName() , $item->getPrice(), $order->getCustomerId());
                    $couponEntity->save();
                    $mailData['data']['couponsData'][] = [
                        'itemName' => $item->getName(),
                        'itemSku' => $item->getSku(),
                        'code' => $couponEntity->getCouponCode(),
                        'validTo' => $couponEntity->getValidTo()
                    ];
                }
            }
        }
        if ($orderHasCoupons) {
            $this->mail->send($mailData);
        }
    }
}
