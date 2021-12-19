<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Block\Widget\Customer;

/**
 * For textarea attribute of customer entity
 *
 * Class Textarea
 * @package Yosto\CustomerAttribute\Block\Widget\Customer
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