<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AddressAttribute\Model;


use Magento\Framework\Model\AbstractModel;

class EavAttribute extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Yosto\AddressAttribute\Model\ResourceModel\EavAttribute');
    }
}