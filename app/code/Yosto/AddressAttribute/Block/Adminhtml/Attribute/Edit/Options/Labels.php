<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AddressAttribute\Block\Adminhtml\Attribute\Edit\Options;

use Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Labels as OptionsLabels;

/**
 * Class Labels
 * @package Yosto\AddressAttribute\Block\Adminhtml\Attribute\Edit\Options
 */
class Labels extends OptionsLabels
{
    protected $_template = 'Yosto_AddressAttribute::address/attribute/labels.phtml';

    /**
     * Retrieve frontend labels of attribute for each store
     *
     * @return array
     */
    public function getLabelValues()
    {
        $values = (array)$this->getAttributeObject()->getFrontend()->getLabel();
        $storeLabels = $this->getAttributeObject()->getStoreLabels();
        foreach ($this->getStores() as $store) {
            if ($store->getId() != 0) {
                $values[$store->getId()] = isset($storeLabels[$store->getId()]) ? $storeLabels[$store->getId()] : '';
            }
        }
        return $values;
    }

    /**
     * Retrieve attribute object from registry
     *
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     * @codeCoverageIgnore
     */
    private function getAttributeObject()
    {
        return $this->_registry->registry('address_entity_attribute');
    }
}