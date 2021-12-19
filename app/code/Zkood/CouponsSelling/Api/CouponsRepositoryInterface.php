<?php

namespace Zkood\CouponsSelling\Api;

interface CouponsRepositoryInterface
{
    /**
     * Retrieve list by page type, term, store, etc
     *
     * @return \Zkood\CouponsSelling\Api\Data\CouponInterface[]
     */
    public function getCustomerList();

    /**
     * Retrieve Coupons Details By Id
     *
     * @param  int $id
     * @return string
     */
    public function getById($id);

    /**
     * An endpoint to Redeem a coupon by coupon code.
     *
     * @return string
     */
    public function redeemCoupon();

    /**
     * An endpoint returns the data of the coupon related to a specific seller .
     *
     * @return string
     */
    public function sellerCoupons();
}