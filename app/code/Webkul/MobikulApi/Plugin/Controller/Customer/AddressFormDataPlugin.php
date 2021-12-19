<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Webkul\MobikulApi\Plugin\Controller\Customer;

use \Magento\Framework\Controller\ResultFactory;

class AddressFormDataPlugin
{
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Yosto\CustomerAttribute\Block\Customer\AdditionalInfo\CustomerAttribute
     */
    protected $customerAttribute;

    public function __construct(
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Yosto\CustomerAttribute\Block\Customer\AdditionalInfo\CustomerAttribute $customerAttribute
    ) {
        $this->resultFactory = $resultFactory;
        $this->customerAttribute = $customerAttribute;
    }

    public function afterExecute(
        \Webkul\MobikulApi\Controller\Customer\AddressFormData $subject,
        $result
    ) {
        if ($result && $result->getRawData()) {
            $this->returnArray = json_decode($result->getRawData());
            if ($this->returnArray->success == true) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $customerAttribute = $objectManager->create(\Yosto\CustomerAttribute\Block\Customer\AdditionalInfo\CustomerAttribute::class);
                $collection = $customerAttribute->getCustomerAddressEditAttributes();
                $extraField = [];
                foreach ($collection as $model) {
                    $attributeCode = $model->getAttributeCode();
                    $extraField['is_'.$attributeCode.'_visible'] = $model->getIsVisible() ? true : false;
                    $extraField['is_'.$attributeCode.'_required'] = $model->getIsRequired() ? true : false;
                }
                $this->returnArray->extraFiled = $extraField;
                $resultJson  = $this->resultFactory->create(
                    ResultFactory::TYPE_JSON
                );
                $resultJson->setData($this->returnArray);
                return $resultJson;
            } else {
                $resultJson  = $this->resultFactory->create(
                    ResultFactory::TYPE_JSON
                );
                $resultJson->setData($this->returnArray);
                return $resultJson;
            }
        }
        return $result;
    }
}
