<?php

namespace Yosto\CustomerAttribute\Controller\Adminhtml\Customer\Index;
class Validate extends \Magento\Customer\Controller\Adminhtml\Index\Validate
{
    /**
     * Customer validation
     *
     * @param \Magento\Framework\DataObject $response
     * @return CustomerInterface|null
     */
    protected function _validateCustomer($response)
    {
        $customer = null;
        $errors = [];

        try {
            /** @var CustomerInterface $customer */
            $customer = $this->customerDataFactory->create();

            $customerForm = $this->_formFactory->create(
                'customer',
                'adminhtml_customer',
                $this->_extensibleDataObjectConverter->toFlatArray(
                    $customer,
                    [],
                    '\Magento\Customer\Api\Data\CustomerInterface'
                ),
                true
            );
            $customerForm->setInvisibleIgnored(true);

            $data = $customerForm->extractData($this->getRequest(), 'customer');
            $customerParams = $this->getRequest()->getParam('customer');
            if(array_key_exists('entity_id', $customerParams)) {
                $customerId = $customerParams['entity_id'];
                if($customerId != null) {
                    $data['id'] = $customerId;
                }
            }



            if ($customer->getWebsiteId()) {
                unset($data['website_id']);
            }

            $this->dataObjectHelper->populateWithArray(
                $customer,
                $data,
                '\Magento\Customer\Api\Data\CustomerInterface'
            );

            $errors = $this->customerAccountManagement->validate($customer)->getMessages();
        } catch (\Magento\Framework\Validator\Exception $exception) {
            /* @var $error Error */
            foreach ($exception->getMessages(\Magento\Framework\Message\MessageInterface::TYPE_ERROR) as $error) {
                $errors[] = $error->getText();
            }
        }

        if ($errors) {
            $messages = $response->hasMessages() ? $response->getMessages() : [];
            foreach ($errors as $error) {
                $messages[] = $error;
            }
            $response->setMessages($messages);
            $response->setError(1);
        }

        return $customer;
    }

}