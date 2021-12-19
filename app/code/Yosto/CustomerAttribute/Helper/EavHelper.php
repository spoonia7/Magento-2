<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\CustomerAttribute\Helper;

/**
 * Class EavHelper
 * @package Yosto\CustomerAttribute\Helper
 */
class EavHelper extends \Magento\Eav\Helper\Data
{
    /**
     * At Magento 2.1.x, the system validates both hidden fields and visible fields.
     * if you need required fields, you must sure that these fields are not child fields
     * in attribute relations.
     *
     * @return array
     */
    protected function _getDefaultFrontendClasses()
    {
        return [
            ['value' => '', 'label' => __('None')],
            ['value' => 'required-entry', 'label' => __('Required Field')],
            ['value' => 'validate-number', 'label' => __('Decimal Number')],
            ['value' => 'validate-digits', 'label' => __('Integer Number')],
            ['value' => 'validate-email', 'label' => __('Email')],
            ['value' => 'validate-url', 'label' => __('URL')],
            ['value' => 'validate-alpha', 'label' => __('Letters')],
            ['value' => 'validate-alphanum', 'label' => __('Letters (a-z, A-Z) or Numbers (0-9)')],
            ['value' => 'validate-ssn', 'label' => __('Social Security Number')],
            ['value' => 'validate-phoneStrict', 'label' => __('Phone Strict')],
            ['value' => 'validate-phoneLax', 'label' => __('Phone Lax')],
            ['value' => 'validate-fax', 'label' => __('Fax')],
            ['value' => 'validate-zip-international', 'label' => __('Zip code')],
            ['value' => 'validate-percents', 'label' => __('Percents')],
            ['value' => 'validate-currency-dollar', 'label' => __('Currency Dollar')],
            ['value' => 'validate-no-html-tags', 'label' => __('No HTML Tags')]
        ];
    }
}