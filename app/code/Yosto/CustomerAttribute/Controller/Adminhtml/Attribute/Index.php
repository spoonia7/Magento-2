<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Controller\Adminhtml\Attribute;

use Magento\Framework\App\ResponseInterface;
use Yosto\CustomerAttribute\Controller\Adminhtml\Attribute;

/**
 * Class Index
 * @package Yosto\CustomerAttribute\Controller\Adminhtml\Attribute
 */
class Index extends Attribute
{
    /**
     * Init page, title and set active menu.
     * If request is ajax, then forward to grid action.
     *
     * @return \Magento\Backend\Model\View\Result\Page|void
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Yosto_CustomerAttribute::view_attributes');
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Customer Attributes'));

        return $resultPage;
    }

}