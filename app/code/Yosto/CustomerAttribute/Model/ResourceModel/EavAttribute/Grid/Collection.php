<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Model\ResourceModel\EavAttribute\Grid;

use Magento\Framework\Api;

/**
 * Class Collection
 * @package Yosto\CustomerAttribute\Model\ResourceModel\EavAttribute\Grid
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * Join eav_entity_type and customer_eav_attribute tables
     */
    protected function _renderFiltersBefore()
    {
        $entityTypeTable = $this->getTable('eav_entity_type');
        $customerEavAttributeTable = $this->getTable('customer_eav_attribute');
        $this->getSelect()->join(
            $entityTypeTable . ' as entity_type',
            'main_table.entity_type_id = entity_type.entity_type_id',
            [
                'entity_type_code',
            ]
        )->join(
            $customerEavAttributeTable . ' as ce_attribute',
            'main_table.attribute_id = ce_attribute.attribute_id',
            [
                'is_visible',
                'is_system',
                'sort_order',
                'is_used_in_grid',
                'is_visible_in_grid',
                'is_filterable_in_grid',
                'is_searchable_in_grid'
            ]
        )
            ->where("entity_type.entity_type_code = 'customer'");

        parent::_renderFiltersBefore();
    }


}