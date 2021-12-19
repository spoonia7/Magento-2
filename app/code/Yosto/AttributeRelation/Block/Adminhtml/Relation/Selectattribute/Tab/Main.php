<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AttributeRelation\Block\Adminhtml\Relation\Selectattribute\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Yosto\CustomerAttribute\Model\System\Config\AttributeOptionArray;

/**
 * Select an attribute before create a relation base on that attribute
 *
 * Class Main
 * @since 2.3.0
 * @package Yosto\AttributeRelation\Block\Adminhtml\Relation\Selectattribute\Tab
 */
class Main extends Generic implements TabInterface
{
    /**
     * @var AttributeOptionArray
     */
    protected $_optionArray;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param AttributeOptionArray $optionArray
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        AttributeOptionArray $optionArray,
        array $data = []
    ) {
        $this->_optionArray = $optionArray;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {

        $entityTypeCode = $this->getRequest()->getParam('entity_type_code');

        $form = $this->_formFactory->create();
        //$form->setHtmlIdPrefix('selectattribute_');
       // $form->setFieldNameSuffix('selectattribute');
        $fieldset = $form->addFieldset(
            'attribute_fieldset',
            ['legend' => __('Select a attribute')]
        );

        $fieldset->addField(
            'attribute_id',
            'select',
            [
                'name' => 'attribute_id',
                'label' => __('Attribute'),
                'values' =>$this->_optionArray->toOptionArray($entityTypeCode),
                'note' => __('Only attributes which are select type'),
                'required' => true
            ]
        );

       $fieldset->addField(
            'entity_type_code',
            'hidden',
            [
                'name' => 'entity_type_code',
                'value' => $entityTypeCode
            ]
        );

        $continueButton = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'label' => __('Continue'),
                'onclick' => "setSettings('" . $this->getContinueUrl() . "', 'entity_type_code', 'attribute_id')",
                'class' => 'save',
            ]
        );
        $fieldset->addField('continue_button', 'note', ['text' => $continueButton->toHtml()]);

        $this->setForm($form);
    }

    /**
     * Return url for continue button
     *
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->getUrl(
            'yosto_attribute_relation/relation/new',
            [
                '_current' => true,
                'entity_type_code' => '<%- data.entity_type_code %>',
                'attribute_id' => '<%- data.attribute_id %>',
                '_escape_params' => false
            ]
        );
    }
    public function getTabLabel()
    {
        return __('Select Attribute');
    }

    public function getTabTitle()
    {
        return __('Select Attribute');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }


}