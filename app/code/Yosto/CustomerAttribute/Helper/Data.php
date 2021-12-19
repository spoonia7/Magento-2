<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 * @package Yosto\CustomerAttribute\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @param null $storeId
     * @return mixed
     */
    public function getCustomerFieldsetTitle($storeId = null)
    {
        return $this->scopeConfig->getValue("attribute/fieldset/customer", \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getAddressFieldsetTitle($storeId = null)
    {
        return $this->scopeConfig->getValue("attribute/fieldset/address", \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getDatetimeFormat($storeId = null)
    {
        return $this->scopeConfig->getValue("attribute/fieldset/datetime_format", \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    public function isMigratedSystem($storeId = null)
    {
        return (bool) $this->scopeConfig->getValue("attribute/fieldset/is_migrated_system", \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    public function isUsingOPC($storeId = null)
    {
        return $this->scopeConfig->getValue("attribute/fieldset/is_using_opc", \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getAllowedExtensions($id, $storeId = null)
    {
        return $this->scopeConfig->getValue("attribute/fieldset/" . $id, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
}