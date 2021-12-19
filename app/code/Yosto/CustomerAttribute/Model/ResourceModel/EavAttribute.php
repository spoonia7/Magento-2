<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class EavAttribute
 * @package Yosto\CustomerAttribute\Model\ResourceModel
 */
class EavAttribute extends AbstractDb
{
    protected function _construct()
    {
        $this->_init($this->getTable('eav_attribute'), 'attribute_id');
    }

}