<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\CustomerAttribute\Model\System\Config;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class RelationStatus
 * @package Yosto\CustomerAttribute\Model\System\Config
 */
class RelationStatus implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => 'Active', 'value' => 1],
            ['label' => 'Inactive', 'value' => 0]
        ];
    }

}