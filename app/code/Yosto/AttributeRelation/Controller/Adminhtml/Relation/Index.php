<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AttributeRelation\Controller\Adminhtml\Relation;


use Magento\Framework\App\ResponseInterface;
use Yosto\AttributeRelation\Controller\Adminhtml\Relation;

class Index extends Relation
{
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Yosto_CustomerAttribute::view_relations');
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Attribute Relations'));

        return $resultPage;
    }

}