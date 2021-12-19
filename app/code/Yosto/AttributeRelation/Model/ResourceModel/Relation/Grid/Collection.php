<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AttributeRelation\Model\ResourceModel\Relation\Grid;

/**
 * For UI component
 *
 * Class Collection
 * @since 2.3.0
 * @package Yosto\AttributeRelation\Model\ResourceModel\Relation\Grid
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * Join eav_entity_type, eav_attribute and customer_eav_attribute tables
     */
    protected function _renderFiltersBefore()
    {
        $entityAttributeTable = $this->getTable('eav_attribute');
        $entityTypeTable = $this->getTable('eav_entity_type');
        $customerEavAttributeTable = $this->getTable('customer_eav_attribute');
        $this -> getSelect()
            ->join(
                $entityAttributeTable . ' as attribute_table',
                'main_table.parent_id = attribute_table.attribute_id',
                ['attribute_code as parent_code', 'frontend_label as parent_label']
            )
            ->join(
                $entityTypeTable . ' as attribute_type_table',
                'attribute_table.entity_type_id = attribute_type_table.entity_type_id',
                [
                    'entity_type_code'
                ]
            )
            ->join(
                $customerEavAttributeTable . ' as customer_eav_attribute',
                'main_table.parent_id = customer_eav_attribute.attribute_id',
                [
                    'sort_order',
                ]
            )->order('sort_order desc');

        parent::_renderFiltersBefore();
    }
}