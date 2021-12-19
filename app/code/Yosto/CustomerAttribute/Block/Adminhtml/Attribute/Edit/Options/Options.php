<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Block\Adminhtml\Attribute\Edit\Options;

/**
 * Class Options
 * @package Yosto\CustomerAttribute\Block\Adminhtml\Attribute\Edit\Options
 */
class Options extends \Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Options
{
    protected $_template = 'Yosto_CustomerAttribute::customer/attribute/options.phtml';

    /**
     * Return Customer Entity object
     *
     * @return mixed
     */
    protected function getAttributeObject()
    {
        return $this->_registry->registry('customer_entity_attribute');
    }
}