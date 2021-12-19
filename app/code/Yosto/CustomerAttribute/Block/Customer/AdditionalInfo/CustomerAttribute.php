<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Block\Customer\AdditionalInfo;

use Yosto\CustomerAttribute\Helper\Data;
use Yosto\CustomerAttribute\Model\ResourceModel\Form\CollectionFactory;
use Magento\Framework\View\Element\Template\Context;

/**
 * Main block to get data about attribute and show on template files
 *
 * Class CustomerAttribute
 * @package Yosto\CustomerAttribute\Block\Customer\AdditionalInfo
 */
class CustomerAttribute extends \Magento\Framework\View\Element\Template
{
    protected $_formCollection;

    protected $_dataHelper;

    /**
     * @param CollectionFactory $formCollection
     * @param Context $context
     * @param Data $dataHelper
     * @param array $data
     */
    function __construct(
        CollectionFactory $formCollection,
        Context $context,
        Data $dataHelper,
        array $data = []
    )
    {
        $this->_dataHelper = $dataHelper;
        $this->_formCollection = $formCollection;
        parent::__construct($context, $data);
    }

    /**
     * Return collection of attributes which are allowed show account create page
     *
     * @return $this
     */
    public function getCustomerAccountCreateAttributes()
    {
        $collection = $this->_formCollection->create();
        return $collection->getFormsAttribute('customer', 'customer_account_create')->load();
    }

    /**
     * Return collection of attributes which are allowed show account edit page
     *
     * @return $this
     */
    public function getCustomerAccountEditAttributes()
    {
        $collection = $this->_formCollection->create();
        return $collection->getFormsAttribute('customer', 'customer_account_edit')->load();
    }

    /**
     * Return collection of attributes which are allowed show address edit/create form
     *
     * @return $this
     */
    public function getCustomerAddressEditAttributes()
    {
        $collection = $this->_formCollection->create();
        return $collection->getFormsAttribute('customer_address', 'customer_address_edit')->load();
    }

    /**
     * Get title from configuration to show on account page
     *
     * @return string
     */
    public function getCustomerFieldsetTitle()
    {
        return $this->_dataHelper->getCustomerFieldsetTitle();
    }

    /**
     * Get title from configuration to show on address page
     *
     * @return string
     */
    public function getAddressFieldsetTitle()
    {
        return $this->_dataHelper->getAddressFieldsetTitle();
    }

    public function  getDatetimeFormat()
    {
        return $this->_dataHelper->getDatetimeFormat();
    }
}