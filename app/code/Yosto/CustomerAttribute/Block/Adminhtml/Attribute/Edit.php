<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\CustomerAttribute\Block\Adminhtml\Attribute;


use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Edit attribute
 *
 * Class Edit
 * @package Yosto\CustomerAttribute\Block\Adminhtml\Attribute
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'attribute_id';
        $this->_controller = 'adminhtml_attribute';
        $this->_blockGroup = 'Yosto_CustomerAttribute';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete'));
        $attributeRegistry = $this->_coreRegistry->registry('customer_entity_attribute');
        if($attributeRegistry['is_system'] == 1){
            $this->buttonList->remove('delete');
            $this->buttonList->remove('saveandcontinue');
            $this->buttonList->remove('save');
        }
    }

    /**
     * Retrieve text for header element depending on loaded news
     *
     * @return string
     */
    public function getHeaderText()
    {
        $attributeRegistry = $this->_coreRegistry->registry('customer_entity_attribute');
        if ($attributeRegistry->getId()) {
            $attributeTitle = $this->escapeHtml($attributeRegistry->getAttributeCode());
            return __("Edit Attribute '%1'", $attributeTitle);
        } else {
            return __('Add Attribute');
        }
    }

}