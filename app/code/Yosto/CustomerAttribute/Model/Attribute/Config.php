<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Model\Attribute;

/**
 * Class Config
 * @package Yosto\CustomerAttribute\Model\Attribute
 */
class Config
{
    /**
     * @var \Magento\Catalog\Model\Attribute\Config\Data
     */
    protected $_dataStorage;

    /**
     * @param \Magento\Catalog\Model\Attribute\Config\Data $dataStorage
     */
    public function __construct(\Magento\Catalog\Model\Attribute\Config\Data $dataStorage)
    {
        $this->_dataStorage = $dataStorage;
    }

    /**
     * Retrieve names of attributes belonging to specified group
     *
     * @param string $groupName Name of an attribute group
     * @return array
     */
    public function getAttributeNames($groupName)
    {
        return $this->_dataStorage->get($groupName, []);
    }
}