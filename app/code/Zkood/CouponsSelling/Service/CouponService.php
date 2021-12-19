<?php

namespace Zkood\CouponsSelling\Service;

use Zkood\CouponsSelling\Helper\Data;
use Zkood\CouponsSelling\Model\CouponFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;

class CouponService
{
    /**
     * @var \Zkood\CouponsSelling\Model\CouponFactory
     */
    private $couponFactory;
    /**
     * @var \Zkood\CouponsSelling\Helper\Data
     */
    private $helper;

    public function __construct(
        CouponFactory $couponFactory,
        Data $helper
    )
    {
        $this->couponFactory = $couponFactory;
        $this->helper = $helper;
    }

    public function generateCoupon($sellerCode, $sellerId, $productId,$productName,$productPrice, $customerId = null)
    {
        $couponEntity = $this->couponFactory->create();
        $couponEntity->setSellerId($sellerId);
        $couponEntity->setCustomerId($customerId);
        $couponEntity->setProductId($productId);
        $couponEntity->setProductName($productName);
        $couponEntity->setProductPrice($productPrice);
        $couponCode = $sellerCode . $this->helper->generateRandomString(7);
        $couponEntity->setCouponCode($couponCode);
        $couponEntity->setIsRedeemed(0);

        $currentDate = date("Y-m-d h:i:sa");
        $validTo = strtotime(date("Y-m-d h:i:sa", strtotime($currentDate)) . " +15 day");
        $couponEntity->setValidTo(date("Y-m-d H:i:s",$validTo));

        return $couponEntity;
    }

}
