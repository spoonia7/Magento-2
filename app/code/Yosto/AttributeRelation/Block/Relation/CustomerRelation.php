<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AttributeRelation\Block\Relation;

use Magento\Framework\View\Element\Template;

use Yosto\AttributeRelation\Helper\JsonData;

/**
 * Class CustomerRelation
 * @since 2.3.0
 * @package Yosto\AttributeRelation\Block\Relation
 */
class CustomerRelation extends Template
{
    protected $_jsonData;

    /**
     * @param Template\Context $context
     * @param JsonData $jsonData
     * @param array $data
     */
    function __construct(
        Template\Context $context,
        JsonData $jsonData,
        array $data = []
    ) {
        $this->_jsonData = $jsonData;
        parent::__construct($context, $data);
    }

    /**
     * Convert all relations and conditions to json data.
     * Attribute entity type is customer
     *
     * @return string
     */
    public function getRelationJson()
    {
        $jsonData = $this->_jsonData->relationValueToJson('customer');
        return $jsonData;
    }

}