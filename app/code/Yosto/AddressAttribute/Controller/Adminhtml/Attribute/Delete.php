<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AddressAttribute\Controller\Adminhtml\Attribute;


use Yosto\AddressAttribute\Controller\Adminhtml\AbstractController;

/**
 * Class Delete
 * @package Yosto\AddressAttribute\Controller\Adminhtml\Attribute
 */
class Delete extends AbstractController
{
    /**
     * Delete attribute
     */
    public function execute()
    {
        $attributeRepository = $this->_objectManager->get('Magento\Eav\Model\AttributeRepository');
        $eavAttributeModel = $this->_eavAttributeFactory->create();
        $attributeId = $this->getRequest()->getParam('attribute_id');

        if ($attributeId) {
            $attribute = $eavAttributeModel->load($attributeId);
            if (!$attribute->getId()) {
                $this->messageManager->addErrorMessage(__('Attribute is no longer exist'));
            } else {
                try {
                    $attributeRepository->deleteById($attributeId);
                    $this->messageManager
                        ->addSuccessMessage(__('Deleted Successfully!'));
                    $this->_redirect('*/*/');
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    $this->_redirect('*/*/');
                }
            }
        }
    }

    /**
     * Returns result of authorisation permission
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Yosto_CustomerAttribute::delete_attribute');
    }
}