<?php

namespace Zkood\CouponsSelling\Model;

use Magento\Framework\Model\AbstractModel;
use Zkood\CouponsSelling\Api\Data\CouponInterface;

class Coupon extends AbstractModel implements \Zkood\CouponsSelling\Api\Data\CouponInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel\Coupon::class);
    }

    /**
     * @return int
     */
    public function getEntityId(){
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setEntityId($value){
        return $this->setData(self::ENTITY_ID,$value);
    }

    /**
     * @return int
     */
    public function getCustomerId(){
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setCustomerId($value){
        return $this->setData(self::CUSTOMER_ID,$value);
    }
    /**
     * @return int
     */
    public function getSellerId(){
        return $this->getData(self::SELLER_ID);
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setSellerId($value){
        return $this->setData(self::SELLER_ID,$value);
    }

    /**
     * @return string
     */
    public function getProductId(){
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setProductId($value){
        return $this->setData(self::PRODUCT_ID,$value);
    }

    /**
     * @return string
     */
    public function getCouponCode(){
        return $this->getData(self::COUPON_CODE);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCouponCode($value){
        return $this->setData(self::COUPON_CODE,$value);
    }

    /**
     * @return string
     */
    public function geIsRedeemed(){
        return $this->getData(self::IS_REDEEMED);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setIsRedeemed($value){
        return $this->setData(self::IS_REDEEMED,$value);
    }

    /**
     * @return string
     */
    public function getValidTo(){
        return $this->getData(self::IS_VALID);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValidTo($value){
        return $this->setData(self::IS_VALID,$value);
    }

    /**
     * @return string
     */
    public function getCreatedAt(){
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value){
        return $this->setData(self::CREATED_AT,$value);
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->getData(self::PRODUCT_NAME);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setProductName($value)
    {
        return $this->setData(self::PRODUCT_NAME,$value);
    }

    /**
     * @return string
     */
    public function getProductPrice()
    {
        return $this->getData(self::PRODUCT_PRICE);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setProductPrice($value)
    {
        return $this->setData(self::PRODUCT_PRICE,$value);
    }
}
