<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AddressAttribute\Block\Adminhtml\Attribute;


use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Edit attribute
 *
 * Class Edit
 * @package Yosto\AddressAttribute\Block\Adminhtml\Attribute
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_blockGroup = "Yosto_AddressAttribute";
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
        parent::_construct();
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
            ]
        );
        $this->buttonList->update('save', 'label', __('Save Attribute'));
        $this->buttonList->update('save', 'class', 'save primary');
        $this->buttonList->update(
            'save',
            'data_attribute',
            ['mage-init' => ['button' => ['event' => 'save', 'target' => '#edit_form']]]
        );
        $attributeRegistry = $this->_coreRegistry->registry('address_entity_attribute');
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
        $attributeRegistry = $this->_coreRegistry->registry('address_entity_attribute');
        if ($attributeRegistry->getId()) {
            $attributeTitle = $this->escapeHtml($attributeRegistry->getAttributeCode());
            return __("Edit Attribute '%1'", $attributeTitle);
        } else {
            return __('Add Attribute');
        }
    }

}