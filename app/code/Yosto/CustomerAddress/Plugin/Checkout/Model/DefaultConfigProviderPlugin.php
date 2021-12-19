<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\CustomerAddress\Plugin\Checkout\Model;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class DefaultConfigProviderPlugin
 * @package Yosto\CustomerAddress\Plugin\Checkout\Model
 */
class DefaultConfigProviderPlugin
{
    protected $_attributeMetadata;
    protected $_logger;
    protected $_addressApi;
    protected $_addressMetadata;
    protected $_customerSession;

    /**
     * DefaultConfigProviderPlugin constructor.
     * @param AttributeMetadataDataProvider $attributeMetadata
     * @param AddressInterface $addressApi
     * @param AddressMetadataInterface $addressMetadata
     * @param \Psr\Log\LoggerInterface $logger
     * @param CustomerSession $customerSession
     */
    function __construct(
        AttributeMetadataDataProvider $attributeMetadata,
        AddressInterface $addressApi,
        AddressMetadataInterface $addressMetadata,
        \Psr\Log\LoggerInterface $logger,
        CustomerSession $customerSession
    ) {
        $this->_attributeMetadata = $attributeMetadata;
        $this->_addressMetadata = $addressMetadata;
        $this->_logger = $logger;
        $this->_addressApi = $addressApi;
        $this->_customerSession = $customerSession;
    }

    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param $result
     * @return mixed
     */
    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        $result
    ) {
        if ($this->_customerSession->getCustomer()->getId()) {
            $attributes = $this->_attributeMetadata->loadAttributesCollection(
                'customer_address',
                'customer_register_address'
            );
            if (key_exists('addresses', $result['customerData'])) {
                foreach ($result['customerData']['addresses'] as $key => $address) {
                    foreach ($attributes as $att) {
                        if ($att->getIsUserDefined()) {
                            $attMetadata = $this->_addressMetadata->getAttributeMetadata($att->getAttributeCode());
                            $options = $attMetadata->getOptions();
                            if (count($options) > 0) {
                                if (!key_exists('custom_attributes', $result['customerData']['addresses'][$key])) {
                                    continue;
                                }
                                if (!key_exists($att->getAttributeCode(), $result['customerData']['addresses'][$key]['custom_attributes'])) {
                                    continue;
                                }
                                $attValue = $result['customerData']['addresses'][$key]['custom_attributes'][$att->getAttributeCode()]['value'];
                                foreach ($options as $option) {
                                    if ($option->getValue() == $attValue) {
                                        $result['customerData']['addresses'][$key]['custom_attributes'][$att->getAttributeCode()]['value'] = $option->getLabel();
                                    }
                                }
                            }

                        }


                    }
                }
            }

        }
        return $result;
    }
}