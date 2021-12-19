<?php

namespace Meetanshi\Knet\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Lang
 * @package Meetanshi\Knet\Model\Config\Source
 */
class Lang implements Arrayinterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'USA', 'label' => __('English')], ['value' => 'AR', 'label' => __('Arabic')],];
    }
}
