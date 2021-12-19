<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AttributeRelation\Block\Adminhtml\Relation\Selecttype\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Yosto\CustomerAttribute\Model\System\Config\EntityType;

/**
 * Class Main
 * @since 2.3.0
 * @package Yosto\AttributeRelation\Block\Adminhtml\Relation\Selecttype\Tab
 */
class Main extends Generic implements TabInterface
{

    protected $_entityType;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        EntityType $entityType,
        array $data = []
    ) {
        $this->_entityType = $entityType;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
      //  $form->setHtmlIdPrefix('selecttype_');
    //  $form->setFieldNameSuffix('selecttype');
        $fieldset = $form->addFieldset(
            'attribute_type_fieldset',
            ['legend' => __('Select a attribute type')]
        );

        $fieldset->addField(
            'entity_type_code',
            'select',
            [
                'name' => 'entity_type_code',
                'values' =>$this->_entityType->getCustomerAndAddressEntityType()
            ]
        );

        $continueButton = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'label' => __('Continue'),
                'onclick' => "setSettings('" . $this->getContinueUrl() . "', 'entity_type_code')",
                'class' => 'save',
            ]
        );
        $fieldset->addField('continue_button', 'note', ['text' => $continueButton->toHtml()]);

        $this->setForm($form);
        return parent::_prepareForm();
    }
    /**
     * Return url for continue button
     *
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->getUrl(
            'yosto_attribute_relation/relation/selectattribute',
            [
                '_current' => true,
                'entity_type_code' => '<%- data.entity_type_code %>',
                '_escape_params' => false
            ]
        );
    }
    public function getTabLabel()
    {
        return __('Attribute Type');
    }

    public function getTabTitle()
    {
        return __('Attribute Type');
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