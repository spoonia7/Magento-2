<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AttributeRelation\Helper;

use Yosto\AttributeRelation\Model\ResourceModel\RelationValue\CollectionFactory;

/**
 * Convert relation and condition to json data
 *
 * Class JsonData
 * @since 2.3.0
 * @package Yosto\AttributeRelation\Helper
 */
class JsonData
{

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * @param $items
     * @return string
     */
    public function arrayToJson($items)
    {
        return json_encode($items);
    }

    /**
     * @param $entityTypeCode
     * @return string
     */
    public function relationValueToJson($entityTypeCode)
    {
        $collection = $this->_collectionFactory->create()->getChildAndParent($entityTypeCode);
        $valueArray = null;

        foreach ($collection as $item) {

            $valueArray[$item->getData('child_code')] =
                [
                    $item->getData('parent_code') => [

                        'values' => [$item->getData('condition_value')],
                        "negative" => false

                    ]

                ];

        }

        return $this->arrayToJson($valueArray);
    }

}