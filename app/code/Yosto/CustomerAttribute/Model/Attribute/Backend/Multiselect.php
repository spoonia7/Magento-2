<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Model\Attribute\Backend;
/**
 * Class Multiselect
 * @package Yosto\MpVendorAttributeManager\Model\VendorAttribute\Attribute\Backend
 */
class Multiselect extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    protected $request;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    /**
     * Before Attribute Save Process
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        if ($this->getAttribute()->getIsUserDefined()) {
            $data = $this->request->getPostValue();
            if (isset($data[$attributeCode]) && !is_array($data[$attributeCode])) {
                $data = [];
            }
            if (isset($data[$attributeCode])) {
                $object->setData($attributeCode, join(',', $data[$attributeCode]));
            }
        }
        if (!$object->hasData($attributeCode)) {
            $object->setData($attributeCode, false);
        }
        return $this;
    }

    /**
     * After Load Attribute Process
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function afterLoad($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        if ($this->getAttribute()->getIsUserDefined()) {
            $data = $object->getData($attributeCode);
            if (!is_array($data) && $data) {
                $object->setData($attributeCode, explode(',', $data));
            } else {
                $object->setData($attributeCode, []);
            }
        }
        return $this;
    }
}
