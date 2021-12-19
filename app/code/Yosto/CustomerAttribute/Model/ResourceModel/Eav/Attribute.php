<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Model\ResourceModel\Eav;

use Magento\Catalog\Model\Attribute\LockValidatorInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;
use Yosto\CustomerAttribute\Api\Data\EavAttibuteInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as CatalogAttribute;

/**
 * Customer attribute
 *
 * Class Attribute
 * @package Yosto\CustomerAttribute\Model\ResourceModel\Eav
 */
class Attribute extends CatalogAttribute
{

    const MODULE_NAME = 'Yosto_CustomerAttribute';

    const ENTITY = 'customer_eav_attribute';

    const ENTITY_TYPE_CODE = 'customer';

    protected $_eventPrefix = 'customer_entity_attribute';

    protected $_customerFlatIndexerProcessor;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_customerSetupFactory = ObjectManager::getInstance()->create('Magento\Framework\Api\AttributeValueFactory');
        $this->_customerFlatIndexerProcessor = ObjectManager::getInstance()->create('Yosto\CustomerAttribute\Model\Indexer\Customer\Flat\Processor');
        $this->_indexerEavProcessor = ObjectManager::getInstance()->create('Yosto\CustomerAttribute\Model\Indexer\Customer\Eav\Processor');
        $this->_init('Magento\Catalog\Model\ResourceModel\Attribute');
    }

    /**
     * Processing object after save data
     *
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function afterSave()
    {
        /**
         * Fix saving attribute in admin
         */
        $this->_eavConfig->clear();

        if ($this->_isOriginalEnabledInFlat() != $this->_isEnabledInFlat()) {
            $this->_customerFlatIndexerProcessor->markIndexerAsInvalid();
        }
        if ($this->_isOriginalIndexable() !== $this->isIndexable()
            || ($this->isIndexable() && $this->dataHasChangedFor(self::KEY_IS_GLOBAL))
        ) {
            $this->_indexerEavProcessor->markIndexerAsInvalid();
        }

        return parent::afterSave();
    }



    /**
     * Is attribute enabled for flat indexing
     *
     * @return bool
     */
    protected function _isEnabledInFlat()
    {
        return $this->getData('is_filterable_in_grid') > 0
        || $this->getData('is_used_in_grid') >0
        || $this->getData('is_searchable_in_grid') == 1;
    }

    /**
     * Is original attribute enabled for flat indexing
     *
     * @return bool
     */
    protected function _isOriginalEnabledInFlat()
    {
        return $this->getOrigData('is_filterable_in_grid') > 0
        || $this->getOrigData('is_used_in_grid') >0
        || $this->getOrigData('is_searchable_in_grid') == 1;
    }


    /**
     * Init indexing process after catalog eav attribute delete commit
     *
     * @return $this
     */
    public function afterDeleteCommit()
    {

        parent::afterDeleteCommit();
        if ($this->_isOriginalEnabledInFlat()) {
            $this->_customerFlatIndexerProcessor->markIndexerAsInvalid();
        }
        if ($this->_isOriginalIndexable()) {
            $this->_indexerEavProcessor->markIndexerAsInvalid();
        }
        return $this;
    }

    /**
     * Get default attribute source model
     *
     * @return string
     */
    public function _getDefaultSourceModel()
    {
        return 'Magento\Eav\Model\Entity\Attribute\Source\Table';
    }

    /**
     * Check is an attribute used in EAV index
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function isIndexable()
    {
        $backendType = $this->getBackendType();
        $frontendInput = $this->getFrontendInput();
        // exclude price attribute
        if ($this->getAttributeCode() == 'price') {
            return false;
        }
        if (!$this->getIsFilterableInGrid() && $this->getIsUsedInGrid()) {
            return false;
        }

        if ($backendType == 'int' && $frontendInput == 'select') {
            return true;
        } elseif ($backendType == 'varchar' && $frontendInput == 'multiselect') {
            return true;
        } elseif ($backendType == 'decimal') {
            return true;
        }

        return false;
    }

    /**
     * Is original attribute config indexable
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _isOriginalIndexable()
    {
        // exclude price attribute
        if ($this->getOrigData('attribute_code') == 'price') {
            return false;
        }

        if (!$this->getOrigData('is_filterable_in_grid')
            && !$this->getOrigData('is_searchable_in_grid')
            && !$this->getOrigData('is_used_in_grid')
        ) {
            return false;
        }

        $backendType = $this->getOrigData('backend_type');
        $frontendInput = $this->getOrigData('frontend_input');

        if ($backendType == 'int' && $frontendInput == 'select') {
            return true;
        } elseif ($backendType == 'varchar' && $frontendInput == 'multiselect') {
            return true;
        } elseif ($backendType == 'decimal') {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsUsedInGrid()
    {
        return (bool)$this->getData(self::IS_USED_IN_GRID);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsVisibleInGrid()
    {
        return (bool)$this->getData(self::IS_VISIBLE_IN_GRID);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsFilterableInGrid()
    {
        return (bool)$this->getData(self::IS_FILTERABLE_IN_GRID);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsVisible()
    {
        return $this->getData(self::IS_VISIBLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsFilterableInGrid($isFilterableInGrid)
    {
        return $this->setData(self::IS_FILTERABLE_IN_GRID, $isFilterableInGrid);
    }


    /**
     * Set whether attribute is visible on frontend.
     *
     * @param bool $isVisible
     * @return $this
     */
    public function setIsVisible($isVisible)
    {
        return $this->setData(self::IS_VISIBLE, $isVisible);
    }

    /**
     * @inheritdoc
     */
    public function __sleep()
    {
        $this->unsetData('entity_type');
        return array_diff(
            parent::__sleep(),
            ['_indexerEavProcessor', '_customerFlatIndexerProcessor', 'attrLockValidator']
        );
    }

    /**
     * @inheritdoc
     */
    public function __wakeup()
    {
        parent::__wakeup();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_indexerEavProcessor = $objectManager->get(\Yosto\CustomerAttribute\Model\Indexer\Customer\Flat\Processor::class);
        $this->_customerFlatIndexerProcessor = $objectManager->get(
            \Yosto\CustomerAttribute\Model\Indexer\Customer\Eav\Processor::class
        );
        $this->attrLockValidator = $objectManager->get(LockValidatorInterface::class);
    }
}