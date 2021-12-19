<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AttributeRelation\Block\Adminhtml\Relation\Selecttype;
use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 * Class Tabs
 * @since 2.3.0
 * @package Yosto\AttributeRelation\Block\Adminhtml\Relation\Selecttype
 */
class Tabs extends WidgetTabs
{
    public function _construct()
    {
        $this->setId('selecttype_tabs');
        $this->setDestElementId('selecttype_form');
        $this->setTitle(__('Select attribute type'));
        parent::_construct();
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'select_type',
            [
                'label'=>__('Attribute Type'),
                'title'=>__('Attribute Type'),
                'content'=>$this->getLayout()->createBlock(
                    'Yosto\AttributeRelation\Block\Adminhtml\Relation\Selecttype\Tab\Main'
                )->toHtml(),
                'active'=>true
            ]
        );


        parent::_beforeToHtml();
    }


}