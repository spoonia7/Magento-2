<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AddressAttribute\Controller\Adminhtml\Attribute;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Yosto\AddressAttribute\Controller\Adminhtml\AbstractController;
use Yosto\AddressAttribute\Controller\Adminhtml\Attribute;

/**
 * Class Edit
 * @package Yosto\AddressAttribute\Controller\Adminhtml\Attribute
 */
class Edit extends AbstractController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('attribute_id');

        $model = $this->_attributeFactory->create()
        ->setEntityTypeId($this->_entityTypeId);
        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This attribute no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('yosto_address_attribute/*/');
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


        $this->_coreRegistry->register('address_entity_attribute', $model);


        $pageTitle = $id ? __('Edit Address Attribute') : __('New Address Attribute');
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()
            ->getTitle()
            ->prepend($pageTitle);

        $resultPage->getConfig()->getTitle()->prepend($id ? $model->getName() : '');
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
            ->isAllowed('Yosto_CustomerAttribute::new_address_attribute');
    }
}