<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\CustomerAddress\Model;


use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\AttributeMetadataDataProvider;

/**
 * Class CustomerAddressConfigProvider
 * @package Yosto\CustomerAddress\Model
 */
class CustomerAddressConfigProvider implements ConfigProviderInterface
{
    protected $attributeMetadata;
    function __construct(
        AttributeMetadataDataProvider $attributeMetadata
    ) {
        $this->attributeMetadata = $attributeMetadata;
    }

    public function getConfig()
    {
        /** @var \Magento\Eav\Api\Data\AttributeInterface[] $attributes */
        $attributes = $this->attributeMetadata->loadAttributesCollection(
            'customer_address',
            'customer_register_address'
        );
        $attrs = [];
        foreach ($attributes as $att) {
            if ($att->getIsUserDefined()) {
                $attrs[] = $att->getAttributeCode();
            }
        }

        $config = [
            'custom_address_attributes' => $attrs
        ];
        return $config;

    }
}