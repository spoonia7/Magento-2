<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AddressAttribute\Block\Adminhtml\Attribute\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 * Class Tabs
 * @package Yosto\AddressAttribute\Block\Adminhtml\Attribute\Edit
 */
class Tabs extends \Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('address_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }
}