<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AttributeRelation\Controller\Adminhtml\Relation;


use Magento\Framework\App\ResponseInterface;
use Yosto\AttributeRelation\Controller\Adminhtml\Relation;

class Save extends Relation
{
    public function execute()
    {
        $isPost = $this->getRequest()->getPost();
        if ($isPost) {
            $relationModel = $this->_relationFactory->create();
            $relationParams = $this->getRequest()->getParam('relation');
            if (array_key_exists('relation_id', $relationParams)) {
                $relationId = $relationParams['relation_id'];
                if ($relationId) {
                    $relationModel->load($relationId);
                    $relationValueCollection = $this->_relationValueFactory->create()->getCollection()->addFieldToFilter('relation_id', $relationId);
                    foreach ($relationValueCollection as $relationValue) {
                        $relationValue->delete();
                    }

                }
            }
            $formData = $relationParams;
            $relationModel->setData($formData);
            try {
                // Save slide
                $relationModel->save();

                if (array_key_exists('value', $relationParams)) {
                    $selectedChildAttributes = $relationParams['value'];
                    for ($i = 0; $i < count($selectedChildAttributes); $i++) {
                        $relationValueModel = $this->_relationValueFactory->create();
                        $relationValueModel->setData('child_id', $selectedChildAttributes[$i]);
                        $relationValueModel->setData('relation_id', $relationModel->getId());
                        $relationValueModel->setData('parent_id', $relationParams['parent_id']);
                        $relationValueModel->setData('condition_value', $relationParams['condition_value']);
                        $relationValueModel->save();
                    }
                }

                // Display success message
                $this->messageManager->addSuccessMessage(__('The relation has been saved.'));

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['relation_id' => $relationModel->getId(), '_current' => true]);
                    return;
                }

                // Go to grid page
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_logger->debug($e->getTraceAsString());
            }

            $this->_getSession()->setFormData($formData);
            $this->_redirect('*/*/index');
        }

    }
}