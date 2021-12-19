<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AttributeRelation\Model;


use Magento\Framework\Model\AbstractModel;

/**
 * Class RelationValue
 * @since 2.3.0
 * @package Yosto\AttributeRelation\Model
 */
class RelationValue extends AbstractModel
{
    public function _construct()
    {
        $this->_init('Yosto\AttributeRelation\Model\ResourceModel\RelationValue');
    }
}