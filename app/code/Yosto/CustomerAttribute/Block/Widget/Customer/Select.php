<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Block\Widget\Customer;


use Magento\Customer\Block\Widget\AbstractWidget;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\OptionInterface;

/**
 * Class Select
 * @package Yosto\CustomerAttribute\Block\Widget\Customer
 */
class Select extends AbstractWidget
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    protected $_attributeCode;

    protected $_eavConfig;
    /**
     * Create an instance of the Gender widget
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param CustomerMetadataInterface $customerMetadata
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Address $addressHelper,
        CustomerMetadataInterface $customerMetadata,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Eav\Model\ConfigFactory $eavConfig,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->_eavConfig = $eavConfig;
        parent::__construct($context, $addressHelper, $customerMetadata, $data);
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
     * Get current customer from session
     *
     * @return CustomerInterface
     */
    public function getCustomer()
    {
        if($this->_customerSession->getCustomerId() == null){
            return null;
        }
        return $this->customerRepository->getById($this->_customerSession->getCustomerId());
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
        $attribute = $this->_eavConfig->create()
            ->getAttribute('customer', $this->_attributeCode);
        $storeId = $this->_storeManager->getStore()->getId();
        $storeLabel = $attribute->getStoreLabel($storeId) ? $attribute->getStoreLabel($storeId) : null ;
        $attributeMetadata = $this->_getAttribute($this->_attributeCode);
        $attributeMetadata->setStoreLabel($storeLabel);
        return $attributeMetadata;
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
     * Get value of attribute
     *
     * @return mixed|null
     */
    public function getAttributeValue()
    {
        if ($this->getCustomer() == null) {
            return $this->getDefaultValue();
        }
        $attribute = $this->getCustomer()->getCustomAttribute($this->_attributeCode);
        return $attribute != null ? $attribute->getValue() : $this->getDefaultValue();
    }

    /**
     * Get default value of attribute
     *
     * @return bool|float|int|string
     */
    public function getDefaultValue() {
        return $this->_eavConfig->create()
            ->getAttribute('customer', $this->_attributeCode)
            ->getDefaultValue();
    }

}