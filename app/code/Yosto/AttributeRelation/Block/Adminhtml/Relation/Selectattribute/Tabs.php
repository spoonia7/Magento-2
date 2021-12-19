<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AttributeRelation\Block\Adminhtml\Relation\Selectattribute;

/**
 * Class Tabs
 * @since 2.3.0
 * @package Yosto\AttributeRelation\Block\Adminhtml\Relation\Selectattribute
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    public function _construct()
    {
        $this->setId('selectattribute_tab');
        $this->setDestElementId('selectattribute_form');
        $this->setTitle(__('Select attribute'));
        parent::_construct();
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'select_attribute',
            [
                'label'=>__('Attribute'),
                'title'=>__('Attribute'),
                'content'=>$this->getLayout()->createBlock(
                    'Yosto\AttributeRelation\Block\Adminhtml\Relation\Selectattribute\Tab\Main'
                )->toHtml(),
                'active'=>true
            ]
        );


        parent::_beforeToHtml();
    }
}