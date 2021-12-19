<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Block\Widget\Customer;

/**
 * For Yesno attribute of customer entity
 *
 * Class Yesno
 * @package Yosto\CustomerAttribute\Block\Widget\Customer
 */
class Yesno extends Select
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Yosto_CustomerAttribute::customer/widget/yesno.phtml');
    }
}