<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AttributeRelation\Block\Adminhtml\Relation\Edit;

/**
 * Class Tabs
 *
 * @since 2.3.0
 * @package Yosto\AttributeRelation\Block\Adminhtml\Relation\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    public function _construct()
    {
        $this->setId('edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Relation information'));
        parent::_construct();
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'relation_information',
            [
                'label'=>__('Relation Information'),
                'title'=>__('Relation Information'),
                'content'=>$this->getLayout()->createBlock(
                    'Yosto\AttributeRelation\Block\Adminhtml\Relation\Edit\Tab\Main'
                )->toHtml(),
                'active'=>true
            ]
        );


        parent::_beforeToHtml();
    }
}