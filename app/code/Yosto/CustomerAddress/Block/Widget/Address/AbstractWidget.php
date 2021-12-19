<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAddress\Block\Widget\Address;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Abstract widget for components which related to address attribute
 *
 * Class AbstractWidget
 * @package Yosto\CustomerAddress\Block\Widget\Address
 */
class AbstractWidget extends \Magento\Framework\View\Element\Template
{
    /**
     * @var
     */
    protected $_addressMetadata;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param AddressMetadataInterface $addressMetadata
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Address $addressHelper,
        AddressMetadataInterface $addressMetadata,
        array $data = []
    ) {
        $this->_addressHelper = $addressHelper;
        $this->_addressMetadata = $addressMetadata;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve address attribute instance
     *
     * @param string $attributeCode
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface|null
     */
    protected function _getAttribute($attributeCode)
    {
        try {
            return $this->_addressMetadata->getAttributeMetadata($attributeCode);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }
}