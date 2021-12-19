<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Model\ResourceModel\EavAttribute;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Yosto\CustomerAttribute\Model\ResourceModel\EavAttribute
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'attribute_id';

    protected function _construct()
    {
        $this->_init('Yosto\CustomerAttribute\Model\EavAttribute', 'Yosto\CustomerAttribute\Model\ResourceModel\EavAttribute');
    }

    public function getUserDefinedAttributeByType($entityTypeCode)
    {
        $eavEntityTypeTable = $this->getTable('eav_entity_type');
        $customerEavAttributeTable = $this->getTable('customer_eav_attribute');
        $this->getSelect()->join(
            $eavEntityTypeTable . ' as entity_type',
            'main_table.entity_type_id = entity_type.entity_type_id',
            ['entity_type_code']
        )->join($customerEavAttributeTable . ' as cea',
            'main_table.attribute_id = cea.attribute_id',
            ['is_visible']
        )
            ->where("entity_type.entity_type_code = '{$entityTypeCode}'")
            ->where('is_user_defined = 1')
            ->where('is_visible = 1')
            ->where("frontend_input = 'select'");
        if ($entityTypeCode == 'customer') {
            $this->getSelect()->where(
                "attribute_code not in ('created_at', 'updated_at', 'gender', 'taxvat', 'dob', 'suffix', 'middlename', 'prefix')"
            );
        } else {
            $this->getSelect()->where(
                "attribute_code not in ('fax', 'suffix', 'middlename', 'prefix')"
            );
        }
        return $this;
    }

    public function filterAttributeByTypeAndExclude($entityTypeCode, $excludeAttributeId)
    {
        $eavEntityTypeTable = $this->getTable('eav_entity_type');
        $customerEavAttributeTable = $this->getTable('customer_eav_attribute');
        $relationValueTable = $this->getTable('yosto_customer_attribute_relation_value');
        $queryIsNotParent = sprintf(
            "select parent_id from  " . $relationValueTable .
            " where child_id = %u", $excludeAttributeId
        );

        $this->getSelect()->join(
            $eavEntityTypeTable . ' as entity_type',
            'main_table.entity_type_id = entity_type.entity_type_id',
            ['entity_type_code']
        )->join($customerEavAttributeTable . ' as cea',
            'main_table.attribute_id = cea.attribute_id',
            ['is_visible']
        )
            ->where("entity_type.entity_type_code = '{$entityTypeCode}'")
            ->where('is_user_defined = 1')
            ->where('is_visible = 1')
            ->where("main_table.attribute_id <> {$excludeAttributeId}")
            ->where("main_table.attribute_id not in ({$queryIsNotParent})");
        if ($entityTypeCode == 'customer') {
            $this->getSelect()->where(
                "attribute_code not in ('created_at', 'updated_at', 'gender', 'taxvat', 'dob', 'suffix', 'middlename', 'prefix')"
            );
        } else {
            $this->getSelect()->where(
                "attribute_code not in ('fax', 'suffix', 'middlename', 'prefix')"
            );
        }
        return $this;
    }


    public function getAttributeTypeById($attributeId)
    {
        $eavEntityTypeTable = $this->getTable('eav_entity_type');

        $this->getSelect()->join(
            $eavEntityTypeTable . ' as type_table',
            "main_table.entity_type_id = type_table.entity_type_id",
            ['entity_type_code']
        )->where("main_table.attribute_id = {$attributeId}");
        return $this;
    }
}