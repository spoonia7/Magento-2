<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AddressAttribute\Block\Adminhtml\Attribute\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Config\Model\Config\Source\Yesno;
use Yosto\AddressAttribute\Model\EavAttribute;
use Yosto\CustomerAttribute\Model\FormFactory as CustomerFormAttributeFactory;
use Yosto\AddressAttribute\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Config visible in forms for customer/address attribute
 *
 * Class Front
 * @package Yosto\AddressAttribute\Block\Adminhtml\Attribute\Edit\Tab
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
        /** @var EavAttribute $attributeObject */
        $attributeObject = $this->_coreRegistry->registry('address_entity_attribute');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );


        $formModel = $this->_customerFormAttributeFactory->create();
        $selectedForm = [];
        if ($attributeObject) {
            $usedInForms = $formModel->getCollection()->distinct(false)
                ->addFieldToFilter(
                    'attribute_id',
                    $attributeObject->getData('attribute_id')
                );
            foreach ($usedInForms as $item) {
                $selectedForm[] = $item->getData('form_code');
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
                'label' => __('Show on admin customer address tab'),
                'value' => 'adminhtml_customer_address'
            ],
            [
                'label' => __('Show on billing & shipping address form'),
                'value' => 'customer_register_address'
            ],
            [
                'label' => __('Show on edit address form'),
                'value' => 'customer_address_edit'
            ]

        ];

        $fieldset->addField(
          'use_in_forms',
          'multiselect',
          [
            'name' => 'use_in_forms',
            'label' => __('Able to show on forms'),
            'values' => $availableForms
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
        $data['use_in_forms'] = $selectedForm;
        $form->setValues($data);
        
        $this->setForm($form);
        $this->propertyLocker->lock($form);
        return parent::_prepareForm();

    }
}