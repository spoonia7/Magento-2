<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Model\ResourceModel\Form;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
/**
 * Class Collection
 * @package Yosto\CustomerAttribute\Model\ResourceModel\Form
 */
class Collection extends AbstractCollection
{

    protected function _construct()
    {
        $this->_init('Yosto\CustomerAttribute\Model\Form', 'Yosto\CustomerAttribute\Model\ResourceModel\Form');
    }

    /**
     * @param $entityTypeCode
     * @param $formCode
     * @return $this
     */
    public function getFormsAttribute($entityTypeCode, $formCode)
    {
        $cusotmerEavAtrributeTable = $this->getTable('customer_eav_attribute');
        $eavAttribute = $this->getTable('eav_attribute');
        $eavEntityType = $this->getTable('eav_entity_type');

        $this->getSelect()->join(
            $cusotmerEavAtrributeTable.' as cea',
            'main_table.attribute_id = cea.attribute_id',
            [
                'is_visible'
            ]
        )->join(
            $eavAttribute . ' as ea',
            'cea.attribute_id = ea.attribute_id',
            [
                'is_user_defined',
                'attribute_code',
                'frontend_input',
                'frontend_label',
                'frontend_class',
                'is_required',
                'default_value'
            ]
        )->join(
            $eavEntityType . ' as eet',
            'ea.entity_type_id = eet.entity_type_id',
            [
                'entity_type_code'
            ]
        )->where("entity_type_code = '{$entityTypeCode}'" )
            ->where('is_user_defined = 1')
            ->where('is_visible = 1')
            ->where("form_code = '{$formCode}'")
            ->order('sort_order ASC');
        if($entityTypeCode == 'customer'){
            $this->getSelect()->where(
                "attribute_code not in ('created_at', 'updated_at', 'gender', 'taxvat', 'dob', 'suffix', 'middlename', 'prefix')"
            );
        }else{
            $this->getSelect()->where(
                "attribute_code not in ('fax', 'suffix', 'middlename', 'prefix')"
            );
        }
        return $this;
    }


}