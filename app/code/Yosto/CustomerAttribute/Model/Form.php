<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Form
 * @package Yosto\CustomerAttribute\Model
 */
class Form extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Yosto\CustomerAttribute\Model\ResourceModel\Form');
    }
}