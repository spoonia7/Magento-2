<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AttributeRelation\Controller\Adminhtml\Relation;


use Yosto\AttributeRelation\Controller\Adminhtml\Relation;

class Delete extends Relation
{
    public function execute()
    {
        $relationId=$this->getRequest()->getParam('relation_id');
        if($relationId) {
            $relationModel = $this->_relationFactory->create();
            $relationModel->load($relationId);
            if (!$relationModel->getRelationId()) {
                $this->messageManager->addErrorMessage(__('Relation is no longer exist'));
            } else {
                try {
                    $relationModel->delete();
                    $this->messageManager->addSuccessMessage(__('Deleted Successfully!'));
                    $this->_redirect('*/*/');
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    $this->_redirect('*/*/edit', ['id' => $relationModel->getId()]);
                }
            }
        }
    }
}