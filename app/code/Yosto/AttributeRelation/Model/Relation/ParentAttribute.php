<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AttributeRelation\Model\Relation;


use Magento\Framework\Option\ArrayInterface;

class ParentAttribute implements  ArrayInterface
{
    private $attributeType;

    public function getAttributeType()
    {
        return $this->attributeType;
    }

    public function setAttributeType($attributeType)
    {
        $this->attributeType = $attributeType;
    }

    /**
     * Return attributes which is dropdown or yesno
     */
    public function toOptionArray()
    {
        // TODO: Implement toOptionArray() method.
    }

}