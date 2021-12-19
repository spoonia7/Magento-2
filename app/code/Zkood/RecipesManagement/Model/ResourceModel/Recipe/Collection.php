<?php

namespace Zkood\RecipesManagement\Model\ResourceModel\Recipe;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(
            'Zkood\RecipesManagement\Model\Recipe',
            'Zkood\RecipesManagement\Model\ResourceModel\Recipe');
    }
}
