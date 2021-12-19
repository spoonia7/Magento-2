<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AttributeRelation\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Relation
 * @since 2.3.0
 * @package Yosto\AttributeRelation\Model\ResourceModel
 */
class Relation extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('yosto_customer_attribute_relation', 'relation_id');
    }

}