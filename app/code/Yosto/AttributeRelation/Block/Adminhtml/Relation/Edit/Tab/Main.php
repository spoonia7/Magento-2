<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AttributeRelation\Block\Adminhtml\Relation\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Yosto\CustomerAttribute\Model\EavAttributeFactory;
use Yosto\AttributeRelation\Model\RelationValueFactory;
use Yosto\AttributeRelation\Model\RelationFactory;
use Yosto\CustomerAttribute\Model\System\Config\AttributeOptionArray;
use Yosto\CustomerAttribute\Model\ResourceModel\EavAttribute\CollectionFactory as EavAttributeCollectionFactory;

/**
 * Edit relation
 *
 * Class Main
 * @since 2.3.0
 * @package Yosto\AttributeRelation\Block\Adminhtml\Relation\Edit\Tab
 */
class Main extends Generic implements TabInterface
{
    /**
     * @var Yesno
     */
    protected $_yesNo;

    /**
     * @var FormFactory
     */
    protected $_formFactory;


    /**
     * @var RelationFactory
     */
    protected $_relationFactory;

    /**
     * @var EavAttributeFactory
     */
    protected $_eavAttributeFactory;


    /**
     * @var AttributeOptionArray
     */
    protected $_attributeOptionArray;

    /**
     * @var EavAttributeCollectionFactory
     */
    protected $_eavAttributeCollectionFactory;

    /**
     * @var RelationValueFactory
     */
    protected $_relationValueFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Yesno $yesNo
     * @param RelationFactory $relationFactory
     * @param RelationValueFactory $relationValueFactory
     * @param AttributeOptionArray $attributeOptionArray
     * @param EavAttributeFactory $eavAttributeFactory
     * @param EavAttributeCollectionFactory $eavAttributeCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesNo,
        RelationFactory $relationFactory,
        RelationValueFactory $relationValueFactory,
        AttributeOptionArray $attributeOptionArray,
        EavAttributeFactory $eavAttributeFactory,
        EavAttributeCollectionFactory $eavAttributeCollectionFactory,
        array $data = []
    ) {
        $this->_attributeOptionArray = $attributeOptionArray;
        $this->_eavAttributeFactory = $eavAttributeFactory;
        $this->_relationFactory = $relationFactory;
        $this->_relationValueFactory = $relationValueFactory;
        $this->_formFactory = $formFactory;
        $this->_yesNo = $yesNo;
        $this->_eavAttributeCollectionFactory = $eavAttributeCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Hidden fields: parent_id, relation_id
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('customer_attribute_relation');
        $entityTypeCode = $this->getRequest()->getParam('entity_type_code');
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $conditionValue = null;

        $parentId = null;
        if ($model->getData('parent_id') != null) {
            $parentId = $model->getData('parent_id');
            $entityTypeCode = $this->_eavAttributeCollectionFactory->create()->getAttributeTypeById($parentId)->getFirstItem()->getData('entity_type_code');
            $conditionValue = $this->_relationValueFactory->create()
                ->getCollection()
                ->addFieldToFilter('relation_id', $model->getData('relation_id'))
                ->getFirstItem()->getData('condition_value');
        } elseif ($attributeId != null) {
            $parentId = $attributeId;
        }
        $attribute = $this->_eavAttributeFactory->create()->load($parentId);
        $frontendInput = $attribute->getData('frontend_input');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('relation_');
        $form->setFieldNameSuffix('relation');
        $fieldset = $form->addFieldset(
            'edit_relation_fieldset',
            ['legend' => __('Relation info')]
        );
        if ($model->getData('relation_id')) {
            $fieldset->addField(
                'relation_id',
                'hidden',
                ['name' => 'relation_id']
            );
        }

        $fieldset->addField(
            'relation_name',
            'text',
            [
                'name' => 'relation_name',
                'label' => __('Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Enable'),
                'values' => $this->_yesNo->toOptionArray(),
            ]
        );




        if ($frontendInput == 'boolean') {
            $fieldset->addField(
                'condition_value',
                'checkbox',
                [
                    'name' => 'condition_value',
                    'label' => $attribute->getData('frontend_label'),
                    'required' => true
                ]
            );
        } elseif ($frontendInput == 'select') {
            $optionsArray[] = null;
            $options = null;

            if ($entityTypeCode == 'customer') {
                $options = ObjectManager::getInstance()->create('Yosto\CustomerAttribute\Block\Widget\Customer\Select')->setAttributeCode($parentId)->getSelectOptions();
            } elseif ($entityTypeCode == 'customer_address') {
                $options = ObjectManager::getInstance()->create('Yosto\CustomerAddress\Block\Widget\Address\Select')->setAttributeCode($parentId)->getSelectOptions();
            }

            foreach($options  as $option) {
                $optionsArray[] = ['label' => $option->getLabel(), 'value' => $option->getValue()];
            }



            $fieldset->addField(
                'condition_value',
                'select',
                [
                    'name' => 'condition_value',
                    'label' => $attribute->getData('frontend_label'),
                    'values' => $optionsArray,
                    'required' => true
                ]
            );


            $selectedAttributes = $this->_coreRegistry->registry('customer_attribute_relation_value');
            $fieldset->addField(
                'value',
                'multiselect',
                [
                    'name' => 'value',
                    'label' => __('Select child attribute'),
                    'values' => $this->_attributeOptionArray->toOptionArrayForMultiSelect($entityTypeCode, $parentId),
                    'note' => __('Attribute code : Attribute frontend label')
                ]
            );

        }
        $fieldset->addField(
            'parent_id',
            'hidden',
            [
                'name' => 'parent_id',
            ]
        );
        $data = $model->getData();
        if (!$model->getData('relation_id')) {
            $data['status'] = 1;
            $data['parent_id'] = $parentId;
        }
        $data['condition_value'] = $conditionValue;
        $data['value']  = $selectedAttributes;
        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }


    public function getTabLabel()
    {
        return __('Relation Information');
    }

    public function getTabTitle()
    {
        return __('Relation Information');
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