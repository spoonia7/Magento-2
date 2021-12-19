<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AttributeRelation\Block\Adminhtml\Relation\Selecttype;

use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Class Form
 * @package Yosto\AttributeRelation\Block\Adminhtml\Relation\Selecttype
 */
class Form extends Generic
{
    protected function _prepareForm()
    {

        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'selecttype_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                ]
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}