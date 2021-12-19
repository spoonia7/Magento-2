<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\CustomerAttribute\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
/**
 * Class Form
 * @package Yosto\CustomerAttribute\Model\ResourceModel
 */
class Form extends AbstractDb
{
    protected function _construct()
    {
        $this->_init($this->getTable('customer_form_attribute'), 'attribute_id, form_code');
    }
}