<?php

namespace Zkood\CouponsSelling\Api\Data;

/**
 * Interface CouponInterface
 * @package Zkood\CouponsSelling\Api\Data
 */
interface CouponInterface
{
    const ENTITY_ID = 'entity_id';

    const CUSTOMER_ID = 'customer_id';

    const SELLER_ID = 'seller_id';

    const COUPON_CODE = 'coupon_code';

    const PRODUCT_ID = 'product_id';

    const PRODUCT_NAME = 'product_name';

    const PRODUCT_PRICE = 'product_price';

    const IS_REDEEMED = 'is_redeemed';

    const IS_VALID = 'is_valid';

    const CREATED_AT = 'created_at';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setEntityId($value);

    /**
 * @return int
 */
    public function getCustomerId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setCustomerId($value);
    /**
     * @return int
     */
    public function getSellerId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setSellerId($value);

    /**
     * @return string
     */
    public function getProductId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setProductId($value);
    /**
     * @return string
     */
    public function getProductName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setProductName($value);
    /**
     * @return string
     */
    public function getProductPrice();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setProductPrice($value);

    /**
     * @return string
     */
    public function getCouponCode();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCouponCode($value);

    /**
     * @return string
     */
    public function geIsRedeemed();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setIsRedeemed($value);

    /**
     * @return string
     */
    public function getValidTo();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValidTo($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value);
}
