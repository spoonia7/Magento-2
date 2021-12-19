<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AddressAttribute\Controller\Adminhtml\Attribute;

use Magento\Framework\App\ResponseInterface;
use Yosto\AddressAttribute\Controller\Adminhtml\AbstractController;

/**
 * Class NewAction
 * @package Yosto\AddressAttribute\Controller\Adminhtml\Attribute
 */
class NewAction extends AbstractController
{
    /**
     * Forward to edit form
     */
    public function execute()
    {
        $this->_forward('edit');
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