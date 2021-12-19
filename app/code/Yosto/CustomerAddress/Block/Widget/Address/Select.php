<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAddress\Block\Widget\Address;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\OptionInterface;
use Magento\Customer\Api\Data\AddressInterface;

/**
 * Class Select
 * @package Yosto\CustomerAddress\Block\Widget\Address
 */
class Select extends AbstractWidget
{
    /**
     * @var
     */
    protected $_addressId;

    /**
     * @var AddressRepositoryInterface
     */
    protected $_addressRepository;

    /**
     * @var
     */
    protected $_attributeCode;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param AddressMetadataInterface $addressMetadata
     * @param AddressRepositoryInterface $addressRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Address $addressHelper,
        AddressMetadataInterface $addressMetadata,
        AddressRepositoryInterface $addressRepository,
        array $data = []
    ) {
        $this->_addressRepository = $addressRepository;
        parent::__construct($context, $addressHelper, $addressMetadata, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Initialize block
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Yosto_CustomerAttribute::customer/widget/select.phtml');
    }

    /**
     * Check if gender attribute enabled in system
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_getAttribute($this->_attributeCode) ? (bool)$this->_getAttribute($this->_attributeCode)->isVisible() : false;
    }

    /**
     * Check if gender attribute marked as required
     * @return bool
     */
    public function isRequired()
    {
        return $this->_getAttribute($this->_attributeCode) ? (bool)$this->_getAttribute($this->_attributeCode)->isRequired() : false;
    }

    /**
     *
     * @return AddressInterface
     */
    public function getAddress()
    {
        if ($this->_addressId == 0) {
            return null;
        }
        return $this->_addressRepository->getById($this->_addressId);
    }

    /**
     * Returns options from a 'select' attribute
     * @return OptionInterface[]
     */
    public function getSelectOptions()
    {
        return $this->_getAttribute($this->_attributeCode)->getOptions();
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

    /**
     * Get value of custom attribute which has entity type is address
     *
     * @return mixed|null
     */
    public function getAttributeValue()
    {
        if ($this->getAddress() == null) {
            return null;
        }
        $attribute = $this->getAddress()->getCustomAttribute($this->_attributeCode);
        return $attribute != null ? $attribute->getValue() : null;
    }

    /**
     * Set address id from address edit form
     *
     * @param $addressId
     * @return $this
     */
    public function setAddressId($addressId)
    {
        $this->_addressId = $addressId;
        return $this;
    }
}