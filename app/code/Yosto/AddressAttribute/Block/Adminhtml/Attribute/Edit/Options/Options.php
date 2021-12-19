<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AddressAttribute\Block\Adminhtml\Attribute\Edit\Options;

/**
 * Class Options
 * @package Yosto\AddressAttribute\Block\Adminhtml\Attribute\Edit\Options
 */
class Options extends \Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Options
{
    protected $_template = 'Yosto_AddressAttribute::address/attribute/options.phtml';

    /**
     * Return Customer Entity object
     *
     * @return mixed
     */
    protected function getAttributeObject()
    {
        return $this->_registry->registry('address_entity_attribute');
    }
}