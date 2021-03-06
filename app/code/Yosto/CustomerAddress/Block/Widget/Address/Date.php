<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAddress\Block\Widget\Address;

use Magento\Customer\Api\AddressMetadataInterface;
use Yosto\CustomerAddress\Block\Widget\Address\AbstractWidget;
use Magento\Framework\Api\ArrayObjectSearch;

/**
 * Widget class for date component of an address attribute.
 *
 * Class Date
 * @package Yosto\CustomerAttribute\Block\Widget\Address
 */
class Date extends AbstractWidget
{

    /**
     * Constants for borders of date-type customer attributes
     */
    const MIN_DATE_RANGE_KEY = 'date_range_min';

    const MAX_DATE_RANGE_KEY = 'date_range_max';

    /**
     * Date inputs
     *
     * @var array
     */
    protected $_dateInputs = [];

    /**
     * @var \Magento\Framework\View\Element\Html\Date
     */
    protected $dateElement;


    protected $_attributeCode;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param AddressMetadataInterface $addressMetadata
     * @param \Magento\Framework\View\Element\Html\Date $dateElement
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Address $addressHelper,
        AddressMetadataInterface $addressMetadata,
        \Magento\Framework\View\Element\Html\Date $dateElement,
        array $data = []
    )
    {
        $this->dateElement = $dateElement;
        parent::__construct($context, $addressHelper, $addressMetadata, $data);
    }

    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Yosto_CustomerAttribute::customer/widget/date.phtml');
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        $attributeMetadata = $this->_getAttribute("{$this->_attributeCode}");
        return $attributeMetadata ? (bool)$attributeMetadata->isVisible() : false;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        $attributeMetadata = $this->_getAttribute("{$this->_attributeCode}");
        return $attributeMetadata ? (bool)$attributeMetadata->isRequired() : false;
    }

    /**
     * @param string $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->setTime($date ? strtotime($date) : false);
        $this->setValue($date);
        return $this;
    }
    /**
     * @return string|bool
     */
    public function getDay()
    {
        return $this->getTime() ? date('d', $this->getTime()) : '';
    }

    /**
     * @return string|bool
     */
    public function getMonth()
    {
        return $this->getTime() ? date('m', $this->getTime()) : '';
    }

    /**
     * @return string|bool
     */
    public function getYear()
    {
        return $this->getTime() ? date('Y', $this->getTime()) : '';
    }

    /**
     * Return label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Date of Birth');
    }

    /**
     * Create correct date field
     *
     * @return string
     */
    public function getFieldHtml()
    {
        $this->dateElement->setData([
            'extra_params' => $this->isRequired() ? 'data-validate="{required:true}"' : '',
            'name' => $this->getHtmlId(),
            'id' => $this->getHtmlId(),
            'class' => $this->getHtmlClass(),
            'value' => $this->getValue(),
            'date_format' => $this->getDateFormat(),
            'image' => $this->getViewFileUrl('Magento_Theme::calendar.png'),
            'years_range' => '-120y:c+nn',
            'max_date' => '-1d',
            'change_month' => 'true',
            'change_year' => 'true',
            'show_on' => 'both'
        ]);
        return $this->dateElement->getHtml();
    }

    /**
     * Return id
     *
     * @return string
     */
    public function getHtmlId()
    {
        return "{$this->_attributeCode}";
    }

    /**
     * Returns format which will be applied for DOB in javascript
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
    }

    /**
     * Add date input html
     *
     * @param string $code
     * @param string $html
     * @return void
     */
    public function setDateInput($code, $html)
    {
        $this->_dateInputs[$code] = $html;
    }

    /**
     * Sort date inputs by dateformat order of current locale
     *
     * @param bool $stripNonInputChars
     *
     * @return string
     */
    public function getSortedDateInputs($stripNonInputChars = true)
    {
        $mapping = [];
        if ($stripNonInputChars) {
            $mapping['/[^medy]/i'] = '\\1';
        }
        $mapping['/m{1,5}/i'] = '%1$s';
        $mapping['/e{1,5}/i'] = '%2$s';
        $mapping['/d{1,5}/i'] = '%2$s';
        $mapping['/y{1,5}/i'] = '%3$s';

        $dateFormat = preg_replace(array_keys($mapping), array_values($mapping), $this->getDateFormat());

        return sprintf($dateFormat, $this->_dateInputs['m'], $this->_dateInputs['d'], $this->_dateInputs['y']);
    }

    /**
     * Return minimal date range value
     *
     * @return string|null
     */
    public function getMinDateRange()
    {
        $date = $this->_getAttribute("{$this->_attributeCode}");
        if ($date !== null) {
            $rules = $this->_getAttribute("{$this->_attributeCode}")->getValidationRules();
            $minDateValue = ArrayObjectSearch::getArrayElementByName(
                $rules,
                self::MIN_DATE_RANGE_KEY
            );
            if ($minDateValue !== null) {
                return date("Y/m/d", $minDateValue);
            }
        }
        return null;
    }

    /**
     * Return maximal date range value
     *
     * @return string|null
     */
    public function getMaxDateRange()
    {
        $date = $this->_getAttribute("{$this->_attributeCode}");
        if ($date !== null) {
            $rules = $this->_getAttribute("{$this->_attributeCode}")->getValidationRules();
            $maxDateValue = ArrayObjectSearch::getArrayElementByName(
                $rules,
                self::MAX_DATE_RANGE_KEY
            );
            if ($maxDateValue !== null) {
                return date("Y/m/d", $maxDateValue);
            }
        }
        return null;
    }

    /**
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface|null
     */
    public function getAttribute()
    {
        return $this->_getAttribute($this->_attributeCode);
    }

    /**
     * @param $attributeCode
     * @return $this
     */
    public function setAttributeCode($attributeCode)
    {
        $this->_attributeCode = $attributeCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttributeCode()
    {
        return $this->_attributeCode;
    }


}