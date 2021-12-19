<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Block\Adminhtml\Attribute\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class Label
 * @package Yosto\CustomerAttribute\Block\Adminhtml\Attribute\Edit\Tab
 */
class Label  extends Generic implements TabInterface
{
    /**
     * @inheritdoc
     */
    public function getTabLabel()
    {
       return __('Attribute Label');
    }
    /**
     * @inheritdoc
     */
    public function getTabTitle()
    {
        return __('Attribute Label');
    }
    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * @inheritdoc
     */
    public function isHidden()
    {
        return false;
    }

}