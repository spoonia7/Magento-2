<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AttributeRelation\Controller\Adminhtml\Relation;

use Magento\Framework\App\ResponseInterface;
use Yosto\AttributeRelation\Controller\Adminhtml\Relation;
class NewAction extends Relation
{
    public function execute()
    {
        $this->_forward('edit');
    }

    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Yosto_CustomerAttribute::new_relation');
    }
}