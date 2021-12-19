<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAddress\Block\Widget\Address;

/**
 * For text field attribute of address entity.
 *
 * Class Textfield
 * @package Yosto\CustomerAddress\Block\Widget\Address
 */
class Textfield extends Select
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Yosto_CustomerAttribute::customer/widget/textfield.phtml');
    }
}