<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AddressAttribute\Model\ResourceModel\EavAttribute;


use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'attribute_id';

    protected function _construct()
    {
        $this->_init(
            'Yosto\AddressAttribute\Model\EavAttribute',
            'Yosto\AddressAttribute\Model\ResourceModel\EavAttribute'
        );
    }
}