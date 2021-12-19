<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AttributeRelation\Model\ResourceModel\RelationValue;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @since 2.3.0
 * @package Yosto\AttributeRelation\Model\ResourceModel\RelationValue
 */
class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init('Yosto\AttributeRelation\Model\RelationValue', 'Yosto\AttributeRelation\Model\ResourceModel\RelationValue');
    }

    /**
     * @param $entityTypeCode
     * @return $this
     */
    public function getChildAndParent($entityTypeCode)
    {
        $eavAttributeTable = $this->getTable('eav_attribute');
        $eavEntityType = $this->getTable('eav_entity_type');
        $this->getSelect()
            ->join(
                $eavAttributeTable . ' as parent_attribute_table',
                'main_table.parent_id = parent_attribute_table.attribute_id',
                ['parent_attribute_table.attribute_code as parent_code']
            )
            ->join(
                $eavAttributeTable . ' as child_attribute_table',
                'main_table.child_id = child_attribute_table.attribute_id',
                ['child_attribute_table.attribute_code as child_code']
            )->join(
                $eavEntityType . ' as attribute_type_table',
                'parent_attribute_table.entity_type_id = attribute_type_table.entity_type_id',
                ['entity_type_code']

            )->where("entity_type_code = '{$entityTypeCode}'");
        return $this;
    }
}