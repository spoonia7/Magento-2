<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Block\Widget\Customer;

/**
 * For textfield attribute of customer entity
 *
 * Class Textfield
 * @package Yosto\CustomerAttribute\Block\Widget\Customer
 */
class Textfield extends Select
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Yosto_CustomerAttribute::customer/widget/textfield.phtml');
    }
}