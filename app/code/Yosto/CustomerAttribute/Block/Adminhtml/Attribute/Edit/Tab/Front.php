<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Block\Adminhtml\Attribute\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Config\Model\Config\Source\Yesno;
use Yosto\CustomerAttribute\Model\FormFactory as CustomerFormAttributeFactory;
use Yosto\CustomerAttribute\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Config visible in forms for customer/address attribute
 *
 * Class Front
 * @package Yosto\CustomerAttribute\Block\Adminhtml\Attribute\Edit\Tab
 */
class Front extends Generic
{
    /**
     * @var Yesno
     */
    protected $_yesNo;

    /**
     * @var PropertyLocker
     */
    private $propertyLocker;

    /**
     * @var CustomerFormAttributeFactory
     */
    protected $_customerFormAttributeFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Yesno $yesNo
     * @param PropertyLocker $propertyLocker
     * @param CustomerFormAttributeFactory $customerFormAttributeFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesNo,
        PropertyLocker $propertyLocker,
        CustomerFormAttributeFactory $customerFormAttributeFactory,
        array $data = []
    )
    {
        $this->_yesNo = $yesNo;
        $this->propertyLocker = $propertyLocker;
        $this->_customerFormAttributeFactory = $customerFormAttributeFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var Attribute $attributeObject */
        $attributeObject = $this->_coreRegistry->registry('customer_entity_attribute');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $formModel = $this->_customerFormAttributeFactory->create();
        $selectedForms = [];
        if ($attributeObject) {
            $usedInForms = $formModel->getCollection()->distinct(false)
                ->addFieldToFilter(
                    'attribute_id',
                    $attributeObject->getData('attribute_id')
                );
            foreach ($usedInForms as $item) {
                $selectedForms[] = $item['form_code'];
            }

        }

        $yesno = $this->_yesNo->toOptionArray();

        $fieldset = $form->addFieldset(
            'front_fieldset',
            ['legend' => __('Storefront Properties'), 'collapsable' => $this->getRequest()->has('popup')]
        );
        $fieldset->addField(
            'entity_type_id',
            'hidden',
            [
                'name' => 'entity_type_id',
                'value' => $this->getRequest()->getParam('entity_type_id')
            ]
        );

        $fieldset->addField(
            'is_visible',
            'select',
            [
                'name' => 'is_visible',
                'label' => __('Visible'),
                'values' => $yesno,
                'note' => "Select 'yes' to show attribute on the admin page and the frontend page"
            ]
        );

        $availableForms = [
            [
                'label' =>  __('Show on admin customer page'),
                'value' => 'adminhtml_customer'
            ],
            [
                'label' =>  __('Show on the admin checkout page'),
                'value' => 'adminhtml_checkout'
            ],
            [
                'label' =>  __('Show on the registration page'),
                'value' => 'customer_account_create'
            ],
            [
                'label' => __('Show on the Account edit page'),
                'value' => __('customer_account_edit')
            ]

        ];


        $fieldset->addField(
            'use_in_forms',
            'multiselect',
            [
                'name' => 'use_in_forms',
                'label' => __('Use in forms'),
                'values' => $availableForms,
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sorting Order'),
                'note' => __('The order to display field on frontend'),
            ]
        );

        $data = $attributeObject->getData();
        $data['use_in_forms'] = $selectedForms;
        $form->setValues($data);
        $this->setForm($form);
        $this->propertyLocker->lock($form);
        return parent::_prepareForm();

    }
}