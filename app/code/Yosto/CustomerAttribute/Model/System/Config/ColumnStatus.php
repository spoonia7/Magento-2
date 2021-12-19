<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Model\System\Config;

use Magento\Framework\Data\OptionSourceInterface;
use Yosto\CustomerAttribute\Model\EavAttribute;

/**
 * Class ColumnStatus
 * @package Yosto\CustomerAttribute\Model\System\Config
 */
class ColumnStatus implements OptionSourceInterface
{
    /**
     * @var EavAttribute
     */
    protected $_eavAttribute;

    /**
     * @param EavAttribute $eavAttribute
     */
    public function __construct(EavAttribute $eavAttribute)
    {
        $this->_eavAttribute = $eavAttribute;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $columnStatusOptions = $this->_eavAttribute->getColumnStatuses();
        foreach ($columnStatusOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}