<?php

namespace Zkood\CouponsSelling\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Coupon extends AbstractDb
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('zkood_coupons_entity', 'entity_id');
    }
}
