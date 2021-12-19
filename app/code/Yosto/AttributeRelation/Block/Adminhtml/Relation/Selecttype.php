<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AttributeRelation\Block\Adminhtml\Relation;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Form\Container;

/**
 * Class Selecttype
 * @since 2.3.0
 * @package Yosto\AttributeRelation\Block\Adminhtml\Relation
 */
class Selecttype extends  Container
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

    protected function _construct()
    {
        $this->_objectId = 'entity_type_code';
        $this->_controller = 'adminhtml_relation';
        $this->_blockGroup = 'Yosto_AttributeRelation';
        parent::_construct();
        $this->buttonList->remove('save');
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
}