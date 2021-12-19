<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_RewardPointsUltimate
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\RewardPointsUltimate\Block\Adminhtml\Referral\Grid;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Rules
 * @package Mageplaza\RewardPointsUltimate\Block\Adminhtml\Referral\Grid
 */
class Rules extends Container
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_controller = 'referral_index';
        $this->_headerText = __('Refer Friends Rules');
        $this->_addButtonLabel = __('Add New Rule');
        parent::_construct();
    }
}
