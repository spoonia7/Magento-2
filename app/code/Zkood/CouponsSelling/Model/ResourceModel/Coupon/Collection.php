<?php

namespace Zkood\CouponsSelling\Model\ResourceModel\Coupon;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zkood\CouponsSelling\Model\Coupon as Model;
use Zkood\CouponsSelling\Model\ResourceModel\Coupon as ResourceModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
