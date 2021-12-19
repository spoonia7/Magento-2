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

namespace Mageplaza\RewardPointsUltimate\Block\Milestone;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\RewardPointsUltimate\Block\Account\TierDashboard;
use Mageplaza\RewardPointsUltimate\Helper\Data;
use Mageplaza\RewardPointsUltimate\Model\Milestone;
use Mageplaza\RewardPointsUltimate\Model\ResourceModel\Milestone\Collection;
use Mageplaza\RewardPointsUltimate\Model\Source\ProgressType;

/**
 * Class Tier
 * @package Mageplaza\RewardPointsUltimate\Block\Milestone
 */
class Tier extends TierDashboard
{
    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getAllTier()
    {
        try {
            $customerGroup = $this->ultimateData->getGroupIdByCustomerId($this->getAccount()->getCustomerId());
        } catch (LocalizedException $e) {
            $customerGroup = 0;
        }

        /** @var Collection $collection */
        $collection = $this->ultimateData->getTierCollectionByCustomerGroup(
            $customerGroup,
            $this->getAccount()->getTotalOrder()
        );
        $items = [];
        foreach ($collection->getItems() as $key => $item) {
            $items[$item->getMinPoint()] = $item;
        }
        if (count($items) > 4 && !$this->isAdvanceProgressType()) {
            krsort($items);
        } else {
            ksort($items);
        }

        return $items;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getEndTierId()
    {
        $listTier = $this->getAllTier();

        if (count($listTier) > 4 && !$this->isAdvanceProgressType()) {
            $endTier = array_first($listTier);
        } else {
            $endTier = end($listTier);
        }

        return $endTier->getId();
    }

    /**
     * @param Milestone $tier
     * @param int $milestonePoint
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function checkIsPassStep($tier, $milestonePoint)
    {
        return $tier->getMinPoint() <= $milestonePoint
            && $milestonePoint > 0
            && $tier->getId() !== $this->getEndTierId();
    }

    /**
     * @param $tierNumber
     *
     * @return string
     */
    public function getTierClass($tierNumber)
    {
        if ($this->isAdvanceProgressType()) {
            return 'mp-reward-slide';
        }

        if ($tierNumber > 4) {
            return 'mp-reward-vertical';
        }

        return 'mp-reward-horizontal';
    }

    /**
     * @return bool
     */
    public function isAdvanceProgressType()
    {
        return (int)$this->ultimateData->getMilestoneConfig('type') === ProgressType::ADVANCED;
    }

    /**
     * @param Milestone $currentTier
     * @param Milestone $upTier
     *
     * @return float
     */
    public function getBarPercent($currentTier, $upTier)
    {
        $accountPoint = $this->getMilestonePoint();

        if ($upTier->getMinPoint() - $currentTier->getMinPoint() === 0) {
            return 0;
        }

        return ($accountPoint - $currentTier->getMinPoint()) / ($upTier->getMinPoint() - $currentTier->getMinPoint());
    }

    /**
     * @return string
     */
    public function getMilestonePoint()
    {
        return $this->getAccount()->getMilestoneTotalEarningPoints(
            $this->ultimateData->getSourceMilestoneAction(),
            $this->ultimateData->getPeriodDate()
        );
    }

    /**
     * @return string
     */
    public function getTierBackGround()
    {
        return $this->ultimateData->getMilestoneConfig('background_color');
    }

    /**
     * @return string
     */
    public function getTierColor()
    {
        return $this->ultimateData->getMilestoneConfig('range_color');
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAllDescriptions()
    {
        $descriptionAr = [];
        foreach ($this->getAllTier() as $tier) {
            $descriptionAr[$tier->getId()] = $tier->getDescription();
        }

        return Data::jsonEncode($descriptionAr);
    }

    /**
     * @return bool
     */
    public function isDashboard()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getMilestoneUrl()
    {
        return '#';
    }
}
