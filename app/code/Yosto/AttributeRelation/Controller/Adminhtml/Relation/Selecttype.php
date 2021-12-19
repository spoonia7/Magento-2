<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AttributeRelation\Controller\Adminhtml\Relation;

use Yosto\AttributeRelation\Controller\Adminhtml\Relation;

class Selecttype extends Relation
{
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Yosto_AttributeRelation::new_relation');
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Select attribute type'));

        return $resultPage;
    }


    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Yosto_CustomerAttribute::new_relation');
    }

}