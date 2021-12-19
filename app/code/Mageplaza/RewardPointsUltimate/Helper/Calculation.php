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

namespace Mageplaza\RewardPointsUltimate\Helper;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Math\CalculatorFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\RewardPoints\Helper\Calculation as HelperCalculation;
use Mageplaza\RewardPoints\Model\TransactionFactory;
use Mageplaza\RewardPointsUltimate\Helper\Data as RewardUltimateData;
use Mageplaza\RewardPointsUltimate\Model\ReferralFactory;
use Mageplaza\RewardPointsUltimate\Model\Source\CustomerActions;
use Mageplaza\RewardPointsUltimate\Model\Source\ReferralActions;

/**
 * Class Calculation
 * @package Mageplaza\RewardPointsUltimate\Helper
 */
class Calculation extends HelperCalculation
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var ReferralFactory
     */
    protected $referralFactory;

    /**
     * @var CollectionFactory
     */
    protected $orderCollection;

    /**
     * @var array
     */
    protected $referRuleToCalculateEarning = [];

    /**
     * Calculation constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param TimezoneInterface $timeZone
     * @param SessionFactory $sessionFactory
     * @param CalculatorFactory $calculatorFactory
     * @param Data $helperData
     * @param ReferralFactory $referralFactory
     * @param CollectionFactory $orderCollection
     * @param TransactionFactory $transactionFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        TimezoneInterface $timeZone,
        SessionFactory $sessionFactory,
        CalculatorFactory $calculatorFactory,
        RewardUltimateData $helperData,
        ReferralFactory $referralFactory,
        CollectionFactory $orderCollection,
        TransactionFactory $transactionFactory
    ) {
        $this->helperData = $helperData;
        $this->referralFactory = $referralFactory;
        $this->orderCollection = $orderCollection;

        parent::__construct(
            $context,
            $objectManager,
            $storeManager,
            $priceCurrency,
            $timeZone,
            $sessionFactory,
            $calculatorFactory,
            $transactionFactory
        );
    }

    /**
     * @param $quote
     * @param $address
     *
     * @return null
     */
    public function getReferralsRule($quote, $address)
    {
        if (!$this->getData('referrals_rule')) {
            if ($mpReferCookie = $this->helperData->getCookieHelper()->get()) {
                $accountHelper = $this->helperData->getAccountHelper();
                $referId = $this->helperData->getCryptHelper()->decrypt($mpReferCookie);
                $referer = $accountHelper->getCustomerById($referId);
                $customer = $accountHelper->getCustomerById($quote->getCustomerId());
                if ($this->canGetRule($customer, $referer)) {
                    $websiteId = $this->storeManager->getStore()->getWebsiteId();
                    $rules = $this->referralFactory->create()->getCollection()
                        ->addFieldToFilter('is_active', 1)
                        ->setValidationFilter($customer->getGroupId(), $websiteId, $referer->getGroupId());
                    if ($rules->getSize() > 0) {
                        $quote->setMpRewardReferralId($referId);
                        $rules = $this->validateStopRule($rules, $address);
                        $this->setData('referrals_rule', $rules);
                    }
                }
            }
        }

        return $this->getData('referrals_rule');
    }

    /**
     * @param $rules
     * @param $address
     *
     * @return array
     */
    public function validateStopRule($rules, $address)
    {
        $finalRules = [];
        foreach ($rules as $rule) {
            if ($rule->canProcessRule($address)) {
                $finalRules[] = $rule;
                if ($rule->getStopRulesProcessing()) {
                    break;
                }
            }
        }

        return $finalRules;
    }

    /**
     * @param $customer
     * @param $referer
     *
     * @return bool
     */
    public function canGetRule($customer, $referer)
    {
        if (!$customer || !$customer->getId() || !$referer->getId() || ($referer->getId() == $customer->getId())) {
            return false;
        }

        $orderCollection = $this->orderCollection->create()
            ->addFieldToFilter('customer_email', $customer->getEmail())
            ->addFieldToFilter('mp_reward_referral_id', ['gteq' => 1]);

        return (bool)!$orderCollection->getSize();
    }

    /**
     * @param $quote
     * @param $items
     * @param $rule
     * @param bool $spending
     * @param bool $isCalculateDiscount
     *
     * @return float|int|mixed
     */
    public function getTotalMatchRule($quote, $items, $rule, $spending = true, $isCalculateDiscount = false)
    {
        $total = 0;
        /** @var Item $item */
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                /** @var Item $child */
                foreach ($item->getChildren() as $child) {
                    $total += $this->getItemTotal($child, $rule, $spending, $isCalculateDiscount);
                }
            } else {
                $total += $this->getItemTotal($item, $rule, $spending, $isCalculateDiscount);
            }
        }

        if ($rule->getCustomerApplyToShipping() && $total) {
            $total += $this->getShippingTotalForDiscount($quote, $isCalculateDiscount, false, $spending);
        }

        return $total;
    }

    /**
     * @param $item
     * @param $rule
     * @param bool $isSpending
     * @param bool $isCalculateDiscount
     *
     * @return int|mixed
     */
    public function getItemTotal($item, $rule, $isSpending = true, $isCalculateDiscount = false)
    {
        if ($rule->validateRule($item)) {
            $item->setIsValidateReferral(true);

            return $this->getItemTotalForDiscount($item, $isSpending, $isCalculateDiscount);
        }

        return 0;
    }

    /**
     * @param $item
     * @param $totalReferer
     * @param $totalCustomer
     * @param $totalRefererEarn
     * @param $totalCustomerEarn
     * @param $mpCustomerEarn
     * @param $mpRefererEarn
     * @param $lastItem
     *
     * @return null
     */
    public function calculateItem(
        $item,
        $totalReferer,
        $totalCustomer,
        $totalRefererEarn,
        $totalCustomerEarn,
        &$mpCustomerEarn,
        &$mpRefererEarn,
        &$lastItem
    ) {
        if ($item->getIsValidateReferral()) {
            $item->setIsValidateReferral(false);
            if ($totalCustomerEarn) {
                $mpCustomerEarn += $this->calculatePointsForCustomer($item, $totalCustomer, $totalCustomerEarn);
            }
            $mpRefererEarn += $this->calculatePointsForReferrer($item, $totalReferer, $totalRefererEarn);

            $lastItem = $item;
        }

        return null;
    }

    /**
     * @param $item
     * @param $totalItem
     * @param $totalCustomerEarn
     *
     * @return float|int
     */
    public function calculatePointsForCustomer($item, $totalItem, $totalCustomerEarn)
    {
        $customerEarn = ($this->getItemTotalForDiscount($item, false) / $totalItem) * $totalCustomerEarn;
        $customerEarn = $this->deltaRoundPoint($customerEarn, 'customer');
        $item->setMpRewardEarn($item->getMpRewardEarn() + $customerEarn);

        return $customerEarn;
    }

    /**
     * @param $item
     * @param $totalItem
     * @param $totalRefererEarn
     *
     * @return mixed
     */
    public function calculatePointsForReferrer($item, $totalItem, $totalRefererEarn)
    {
        $refererEarn = ($this->getItemTotalForDiscount($item, false) / $totalItem) * $totalRefererEarn;
        $refererEarn = $this->deltaRoundPoint($refererEarn, 'referer');
        $item->setMpRewardReferralEarn($item->getMpRewardReferralEarn() + $refererEarn);

        return $refererEarn;
    }

    /**
     * @param $total
     * @param $rule
     *
     * @return float|int
     */
    public function getReferrerPointsByAction($total, $rule)
    {
        $refererEarn = $rule->getReferralPoints();
        if ($rule->getReferralType() == ReferralActions::TYPE_PRICE) {
            $refererEarn = ($total * $rule->getReferralPoints()) / $rule->getReferralMoneyStep();
        }

        return $refererEarn;
    }

    /**
     * @param $total
     * @param $rule
     *
     * @return float|int
     */
    public function getCustomerPointsByAction($total, $rule)
    {
        $customerEarn = 0;
        if ($rule->getCustomerAction() == CustomerActions::TYPE_PRICE) {
            $customerEarn = ($total * $rule->getCustomerPoints()) / $rule->getCustomerMoneyStep();
        } elseif ($rule->getCustomerAction() == CustomerActions::TYPE_FIXED_POINTS) {
            $customerEarn = $rule->getCustomerPoints();
        }

        return $customerEarn;
    }

    /**
     * @param $rule
     * @param $total
     *
     * @return float|int
     */
    public function calculateDiscountForCustomer($rule, $total)
    {
        $discount = $rule->getCustomerDiscount();
        if ($rule->getCustomerAction() == CustomerActions::TYPE_PERCENT) {
            $discount = ($discount * $total) / 100;
        }

        return $discount;
    }

    /**
     * @param $item
     * @param $total
     * @param $discountAmount
     * @param $baseDiscount
     * @param $discount
     */
    public function calculateItemDiscount($item, $total, $discountAmount, &$baseDiscount, &$discount)
    {
        if ($item->getIsValidateReferral()) {
            $item->setIsValidateReferral(false);

            $itemBaseDiscount = ($this->getItemTotalForDiscount($item, true, false) / $total) * $discountAmount;
            $itemBaseDiscount = $this->roundPrice($itemBaseDiscount, 'base_refer');
            $itemDiscount = $this->convertPrice($itemBaseDiscount, false, false, $item->getStoreId());
            $itemDiscount = $this->roundPrice($itemDiscount, 'refer');

            $item->setMpRewardDiscount($item->getMpRewardDiscount() + $itemDiscount)
                ->setMpRewardBaseDiscount($item->getMpRewardBaseDiscount() + $itemBaseDiscount)
                ->setMpRewardInvitedBaseDiscount($item->getMpRewardInvitedBaseDiscount() + $itemBaseDiscount)
                ->setMpRewardInvitedDiscount($item->getMpRewardInvitedDiscount() + $itemDiscount);
            $baseDiscount += $itemBaseDiscount;
            $discount += $itemDiscount;
        }
    }

    /**
     * @param $rule
     * @param $totalRefererEarn
     * @param $mpRefererEarn
     *
     * @return mixed
     */
    public function roundPointForReferer($rule, $totalRefererEarn, &$mpRefererEarn)
    {
        $point = $totalRefererEarn - $mpRefererEarn;
        if ($rule->getReferralType() == ReferralActions::TYPE_PRICE) {
            $point = $this->helperData->getPointHelper()->round($this->getDeltaRoundPoint('referer'));
        }
        $mpRefererEarn += $point;

        return $point;
    }

    /**
     * @param $rule
     * @param $totalCustomerEarn
     * @param $mpCustomerEarn
     * @param bool $isShipping
     *
     * @return mixed
     */
    public function roundPointForCustomer($rule, $totalCustomerEarn, &$mpCustomerEarn, $isShipping = false)
    {
        $point = $totalCustomerEarn - $mpCustomerEarn;
        if ($rule->getCustomerAction() == CustomerActions::TYPE_PRICE) {
            if ($isShipping) {
                $point = $this->helperData->getPointHelper()->round($point);
            } else {
                $point = $this->helperData->getPointHelper()->round($this->getDeltaRoundPoint('customer'));
            }
        }
        $mpCustomerEarn += $point;

        return $point;
    }
}
