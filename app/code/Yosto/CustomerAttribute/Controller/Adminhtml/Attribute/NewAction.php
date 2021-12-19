<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Controller\Adminhtml\Attribute;

use Magento\Framework\App\ResponseInterface;
use Yosto\CustomerAttribute\Controller\Adminhtml\Attribute;

/**
 * Class NewAction
 * @package Yosto\CustomerAttribute\Controller\Adminhtml\Attribute
 */
class NewAction extends Attribute
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
            ->isAllowed('Yosto_CustomerAttribute::new_attribute');
    }
}