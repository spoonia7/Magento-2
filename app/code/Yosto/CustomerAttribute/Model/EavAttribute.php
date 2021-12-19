<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * This class use for customer attributes grid
 *
 * Class EavAttribute
 * @package Yosto\CustomerAttribute\Model
 */
class EavAttribute extends AbstractModel
{

    const REQUIRED_YES = 1;
    const REQUIRED_NO = 0;
    const NO = 0;
    const YES =1;

    const CUSTOMER_ENTITY_TYPE = 'customer';
    const CUSTOMER_ADDRESS_ENTITY_TYPE = 'customer_address';

    protected function _construct()
    {
        $this->_init('Yosto\CustomerAttribute\Model\ResourceModel\EavAttribute');
    }

    /**
     * Required or not
     *
     * @return array
     */
    public function getMandatoryStatuses()
    {
        return [
            self::REQUIRED_YES => __('Yes'),
            self::REQUIRED_NO => __('No')
        ];
    }

    /**
     * Is user defined or not
     *
     * @return array
     */
    public function getColumnStatuses()
    {
        return [
            self::NO => __('No'),
            self::YES => __('Yes')
        ];
    }

    public function getEntityType()
    {
        return [
            self::CUSTOMER_ENTITY_TYPE => __('Customer'),
            self::CUSTOMER_ADDRESS_ENTITY_TYPE => __('Customer Address')
        ];
    }
}