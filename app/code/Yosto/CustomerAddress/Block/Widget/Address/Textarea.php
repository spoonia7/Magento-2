<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAddress\Block\Widget\Address;

/**
 * For textarea attribute of address entity
 *
 * Class Textarea
 * @package Yosto\CustomerAdress\Block\Widget\Address
 */
class Textarea extends Select
{
    /**
     * Initialize block
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Yosto_CustomerAttribute::customer/widget/textarea.phtml');
    }
}