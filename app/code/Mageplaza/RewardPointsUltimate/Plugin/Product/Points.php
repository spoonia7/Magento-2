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

namespace Mageplaza\RewardPointsUltimate\Plugin\Product;

use Exception;
use Magento\Bundle\Model\Option;
use Magento\Framework\Pricing\Render\Amount;
use Mageplaza\RewardPointsUltimate\Helper\Data as HelperData;
use Psr\Log\LoggerInterface;

/**
 * Class Points
 * @package Mageplaza\RewardPointsUltimate\Plugin\Product
 */
class Points
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Points constructor.
     *
     * @param HelperData $helperData
     * @param LoggerInterface $logger
     */
    public function __construct(HelperData $helperData, LoggerInterface $logger)
    {
        $this->helperData = $helperData;
        $this->logger = $logger;
    }

    /**
     * @param Amount $subject
     * @param $result
     *
     * @return float|string
     */
    public function afterToHtml(Amount $subject, $result)
    {
        $productOption = $subject->getSaleableItem()->getOption();
        if (($productOption && $productOption instanceof Option)) {
            return $result;
        }
        try {
            if ($this->helperData->isEnabled() && $subject->getSaleableItem()->getMpRewardSellProduct() > 0) {
                if ($subject->getData('price_type') !== 'finalPrice') {
                    return '';
                }
                if ($subject->getData('price_type') === 'finalPrice') {
                    $html = '<span class="price-container price-final_price">
                             <span class="price">'
                        . $this->helperData->getPointHelper()->format(
                            $subject->getSaleableItem()->getMpRewardSellProduct(),
                            false
                        ) . '</span>
                            </span>
                         ';

                    return $html;
                }
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $result;
    }
}
