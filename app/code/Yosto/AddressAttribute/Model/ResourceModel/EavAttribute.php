<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AddressAttribute\Model\ResourceModel;


use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class EavAttribute extends AbstractDb
{
    protected function _construct()
    {
        $this->_init($this->getTable('eav_attribute'), 'attribute_id');
    }
}