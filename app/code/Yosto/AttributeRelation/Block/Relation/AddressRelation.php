<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AttributeRelation\Block\Relation;
use Magento\Framework\View\Element\Template;
use Yosto\CustomerAttribute\Helper\Data;
use Yosto\AttributeRelation\Helper\JsonData;

/**
 * Class AddressRelation
 * @since 2.3.0
 * @package Yosto\AttributeRelation\Block\Relation
 */
class AddressRelation extends Template
{
    /**
     * @var JsonData
     */
    protected $_jsonData;

    protected $_configData;

    /**
     * @param Template\Context $context
     * @param JsonData $jsonData
     * @param array $data
     */
    function __construct(
        Template\Context $context,
        JsonData $jsonData,
        Data $configData,
        array $data = []
    ) {
        $this->_configData = $configData;
        $this->_jsonData = $jsonData;
        parent::__construct($context, $data);
    }

    public function getRelationJson()
    {
        $jsonData = $this->_jsonData->relationValueToJson('customer_address');
        return $jsonData;
    }

    public function isUsingOPC()
    {
        return $this->_configData->isUsingOPC() == 1 ? true : false;
    }
}