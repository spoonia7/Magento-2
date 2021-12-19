<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
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

namespace Mageplaza\RewardPoints\Model\Klarna\Orderline;

use Klarna\Core\Api\BuilderInterface;
use Klarna\Core\Model\Checkout\Orderline\AbstractLine;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\AbstractModel;

/**
 * Class MpRewardPoints
 * @package Mageplaza\RewardPoints\Model\Klarna\Orderline
 */
class MpRewardPoints extends AbstractLine
{
    /**
     * @param BuilderInterface $checkout
     *
     * @return AbstractLine|void
     */
    public function collect(BuilderInterface $checkout)
    {
        /** @var AbstractModel|Quote $object */
        $object  = $checkout->getObject();
        $address = $this->getAddress($object);
        $totals  = $address->getTotals();

        if (is_array($totals) && isset($totals['mp_reward_discount'])) {
            $mpRewardPoints = $this->processMpRewardDiscountFromTotals($totals);
            $checkout->addData($mpRewardPoints);
        } elseif ($object->getMpRewardDiscount() !== 0) {
            $mpRewardPoints = $this->processMpRewardDiscountWithoutTotals($object);
            $checkout->addData($mpRewardPoints);
        }
    }

    /**
     * @param $object
     *
     * @return array
     */
    private function processMpRewardDiscountWithoutTotals($object)
    {
        $mpRewardPoints = __("Reward Points");
        $amount         = $object->getMpRewardDiscount();
        /** @noinspection IsEmptyFunctionUsageInspection */
        if (empty($amount) && !empty($object->getMpRewardDiscount())) {
            $amount = $object->getBaseSubtotal() + $object->getMpRewardDiscount();
        }

        return [
            'mp_reward_discount_unit_price'   => -abs($this->helper->toApiFloat($amount)),
            'mp_reward_discount_tax_rate'     => 0,
            'mp_reward_discount_total_amount' => -abs($this->helper->toApiFloat($amount)),
            'mp_reward_discount_tax_amount'   => 0,
            'mp_reward_discount_title'        => $mpRewardPoints,
            'mp_reward_discount_reference'    => 'mp_reward_discount',
            'mp_reward_discount'              => $amount
        ];
    }

    /**
     * @param $totals
     *
     * @return array
     */
    private function processMpRewardDiscountFromTotals($totals)
    {
        $total  = $totals['mp_reward_discount'];
        $amount = $total->getValue();

        return [
            'mp_reward_discount_unit_price'   => $this->helper->toApiFloat($amount),
            'mp_reward_discount_tax_rate'     => 0,
            'mp_reward_discount_total_amount' => $this->helper->toApiFloat($amount),
            'mp_reward_discount_tax_amount'   => 0,
            'mp_reward_discount_title'        => (string)$total->getTitle(),
            'mp_reward_discount_reference'    => $total->getCode(),
            'mp_reward_discount'              => $amount
        ];
    }

    /**
     * @param $object
     *
     * @return mixed
     */
    private function getAddress($object)
    {
        $address = $object->getShippingAddress();
        if ($address) {
            return $address;
        }
        return $object->getBillingAddress();
    }

    /**
     * Add order details to checkout request
     *
     * @param BuilderInterface $checkout
     *
     * @return $this
     */
    public function fetch(BuilderInterface $checkout)
    {
        if ($checkout->getMpRewardDiscount() !== 0) {
            $checkout->addOrderLine(
                [
                    'type'             => 'discount',
                    'reference'        => $checkout->getMpRewardDiscountReference() ? : 'mp_reward_discount',
                    'name'             => $checkout->getMpRewardDiscountTitle() ? : __("Reward Points"),
                    'quantity'         => 1,
                    'unit_price'       => $checkout->getMpRewardDiscountUnitPrice() ? : 0,
                    'tax_rate'         => $checkout->getMpRewardDiscountTaxRate() ? : 0,
                    'total_amount'     => $checkout->getMpRewardDiscountTotalAmount() ? : 0,
                    'total_tax_amount' => $checkout->getMpRewardDiscountTaxAmount() ? : 0
                ]
            );
        }

        return $this;
    }
}
