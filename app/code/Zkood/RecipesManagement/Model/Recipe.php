<?php

namespace Zkood\RecipesManagement\Model;


class Recipe extends  \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{

    /**
     * Cache tag
     */
    const CACHE_TAG = 'zp';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'Zkood_RecipesManagement';


    protected function _construct()
    {
        $this->_init('Zkood\RecipesManagement\Model\ResourceModel\Recipe');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
