<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Controller\Adminhtml\Attribute;

use Yosto\CustomerAttribute\Controller\Adminhtml\Attribute;

/**
 * Class Edit
 * @package Yosto\CustomerAttribute\Controller\Adminhtml\Attribute
 */
class Edit extends Attribute
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        if ($this->getRequest()->getParam('entity_type_id')) {
            $entityTypeId = $this->getRequest()->getParam('entity_type_id');
            $this->_entityTypeId = $entityTypeId;
        }
        /** @var $model \Yosto\CustomerAttribute\Model\ResourceModel\Eav\Attribute */
        $model = $this->_attributeFactory->create()
        ->setEntityTypeId(
            $this->_entityTypeId
        );
        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This attribute no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('yosto_customer_attribute/*/');
            }

        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getAttributeData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $attributeData = $this->getRequest()->getParam('attribute');
        if (!empty($attributeData) && $id === null) {
            $model->addData($attributeData);
        }


        $this->_coreRegistry->register('customer_entity_attribute', $model);

        $pageTitle = $id ? __('Edit Customer Attribute') : __('New Customer Attribute');
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()
            ->getTitle()
            ->prepend($pageTitle);

        return $resultPage;
    }

    /**
     * Returns result of authorisation permission
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Yosto_CustomerAttribute::new_attribute');
    }
}