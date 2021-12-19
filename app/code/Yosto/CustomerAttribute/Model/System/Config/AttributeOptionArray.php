<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Model\System\Config;
use Magento\Framework\Data\OptionSourceInterface;
use Yosto\CustomerAttribute\Model\ResourceModel\EavAttribute\CollectionFactory;

/**
 * Class AttributeOptionArray
 * @since 2.3.0
 * @package Yosto\CustomerAttribute\Model\System\Config
 */
class AttributeOptionArray
{
    protected $_collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory )
    {
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * @param $entityTypeCode
     * @return array
     */
    public function toOptionArray($entityTypeCode)
    {
        $options[] = ['label'=> '', 'value' => ''];
        $attributeCollection = $this->_collectionFactory->create()->getUserDefinedAttributeByType($entityTypeCode);
        foreach($attributeCollection as $attribute) {
            $value = $attribute->getData('attribute_id');
            $label = $attribute->getData('attribute_code') . ' : ' . $attribute->getData('frontend_label');
            $options[] = [
                'label' => $label,
                'value' => $value
            ];
        }
        return $options;
    }

    /**
     * @param $entityTypeCode
     * @param $attributeId
     * @return array
     */
    public function toOptionArrayForMultiSelect($entityTypeCode, $attributeId) {
        $options[] = ['label'=> '', 'value' => ''];
        $attributeCollection = $this->_collectionFactory->create()->filterAttributeByTypeAndExclude($entityTypeCode, $attributeId);
        foreach($attributeCollection as $attribute) {
            $value = $attribute->getData('attribute_id');
            $label = $attribute->getData('attribute_code') . ' : ' . $attribute->getData('frontend_label');
            $options[] = [
                'label' => $label,
                'value' => $value
            ];
        }
        return $options;
    }


}