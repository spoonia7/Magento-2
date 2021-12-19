<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AttributeRelation\Model\ResourceModel\Relation;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Yosto\AttributeRelation\Model\ResourceModel\Relation
 */
class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init('Yosto\AttributeRelation\Model\Relation', 'Yosto\AttributeRelation\Model\ResourceModel\Relation');
    }
}