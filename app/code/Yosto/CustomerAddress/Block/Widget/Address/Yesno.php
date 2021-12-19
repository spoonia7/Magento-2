<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAddress\Block\Widget\Address;

/**
 * For Yesno attribute of address entity
 *
 * Class Yesno
 * @package Yosto\CustomerAddress\Block\Widget\Address
 */
class Yesno extends Select
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Yosto_CustomerAttribute::customer/widget/yesno.phtml');
    }
}