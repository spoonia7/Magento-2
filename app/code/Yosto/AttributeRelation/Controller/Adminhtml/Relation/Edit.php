<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AttributeRelation\Controller\Adminhtml\Relation;


use Magento\Framework\App\ResponseInterface;
use Yosto\AttributeRelation\Controller\Adminhtml\Relation;

class Edit extends Relation
{
    public function execute()
    {
        $relationId = $this->getRequest()->getParam('relation_id');
        $model = $this->_relationFactory->create();
        $relationValueModel = $this->_relationValueFactory->create();
        $relationValueArray = [];

        $relationName = "";
        if ($relationId) {
            $model->load($relationId);
            if (!$model->getData('relation_id')) {
                $this->messageManager->addError(__('This slide no longer exists.'));
                $this->_redirect('*/*/');
                return;
            } else{

                $relationValueCollection = $relationValueModel->getCollection()->addFieldToFilter('relation_id',$relationId);
                foreach($relationValueCollection as $item){
                    $relationValueArray[]=$item->getData('child_id');
                }

                $relationName = $model->getData('relation_name');
            }
        }
        // Restore previously entered form data from session
        $data = $this->_session->getRelationData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('customer_attribute_relation_value', $relationValueArray);
        $this->_coreRegistry->register('customer_attribute_relation', $model);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Yosto_CustomerAttribute::main_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('Relation ').$relationName);

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Yosto_CustomerAttribute::new_relation');
    }
}