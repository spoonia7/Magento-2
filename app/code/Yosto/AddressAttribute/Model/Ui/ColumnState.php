<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AddressAttribute\Model\Ui;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ColumnState
 * @package Yosto\AddressAttribute\Model\Ui
 */
class ColumnState implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => 'No', 'value' => 0],
            ['label' => 'Yes', 'value' => 1],
        ];

    }
}