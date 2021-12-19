<?php

namespace Zkood\RecipesManagement\Model\ResourceModel;


class Recipe extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('zkood_recipes_entity', 'entity_id');
    }
}
