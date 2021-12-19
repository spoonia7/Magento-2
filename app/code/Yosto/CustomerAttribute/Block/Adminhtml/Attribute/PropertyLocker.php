<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\CustomerAttribute\Block\Adminhtml\Attribute;

use Magento\Framework\Registry;
use Magento\Eav\Model\Entity\Attribute\Config;

/**
 * Disable form fields
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 * Customized by x-mage2 for customer attributes extension
 */
class PropertyLocker extends \Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker
{
    /**
     * @var Config
     */
    private $attributeConfig;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Registry $registry
     * @param Config $attributeConfig
     * @codeCoverageIgnore
     */
    public function __construct(
        Registry $registry,
        Config $attributeConfig
    ) {
        $this->registry = $registry;
        $this->attributeConfig = $attributeConfig;
    }

    /**
     * @param \Magento\Framework\Data\Form $form
     * @return void
     */
    public function lock(\Magento\Framework\Data\Form $form)
    {
        /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attributeObject */
        $attributeObject = $this->registry->registry('customer_entity_attribute');
        if ($attributeObject->getId()) {
            foreach ($this->attributeConfig->getLockedFields($attributeObject) as $field) {
                if ($element = $form->getElement($field)) {
                    $element->setDisabled(1);
                    $element->setReadonly(1);
                }
            }
        }
    }
}
