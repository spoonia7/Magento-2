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
 * @package     Mageplaza_RewardPoints
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\RewardPoints\Block;

use Exception;
use Magento\Framework\View\Element\Template;
use Mageplaza\RewardPoints\Helper\Data;

/**
 * Class Highlight
 * @package Mageplaza\RewardPoints\Block
 */
class Highlight extends Template
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Highlight constructor.
     *
     * @param Template\Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function getHighlightTextColor()
    {
        try {
            $storeId = $this->helper->getStore()->getId();
        } catch (Exception $exception) {
            $storeId = null;
        }

        return $this->helper->getHighlightConfig('color', $storeId);
    }

    /**
     * @return bool
     */
    public function checkEnabled()
    {
        return $this->helper->isEnabled();
    }
}
