<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AddressAttribute\Controller\Adminhtml\Attribute;


use Magento\Framework\App\ResponseInterface;
use Yosto\AddressAttribute\Controller\Adminhtml\AbstractController;

class Index extends AbstractController
{
    public function execute()
    {

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Address Attributes'));

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Yosto_CustomerAttribute::view_address_attributes');
    }

}