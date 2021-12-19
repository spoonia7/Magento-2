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

namespace Mageplaza\RewardPointsUltimate\Setup;

use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Store\Model\StoreRepository;
use Mageplaza\RewardPointsUltimate\Model\MilestoneFactory;

/**
 * Class UpgradeData
 * @package Mageplaza\RewardPointsUltimate\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var MilestoneFactory
     */
    protected $_milestone;

    /**
     * @var Collection
     */
    protected $customerGroup;

    /**
     * @var StoreRepository
     */
    protected $storeRepository;

    /**
     * UpgradeData constructor
     *
     * @param MilestoneFactory $milestone
     * @param Collection $customerGroup
     * @param StoreRepository $storeRepository
     */
    public function __construct(
        MilestoneFactory $milestone,
        Collection $customerGroup,
        StoreRepository $storeRepository
    ) {
        $this->_milestone = $milestone;
        $this->customerGroup = $customerGroup;
        $this->storeRepository = $storeRepository;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $customerGroup = [];
            $websiteIds = [];

            foreach ($this->customerGroup->toOptionArray() as $item) {
                if ($item['value'] !== '0') {
                    $customerGroup[] = $item['value'];
                }
            }

            foreach ($this->storeRepository->getList() as $store) {
                $websiteIds[] = $store['website_id'];
            }

            $data = [
                'name' => __('Base'),
                'status' => 1,
                'customer_group_ids' => implode(',', $customerGroup),
                'website_ids' => implode(',', $websiteIds)
            ];
            $post = $this->_milestone->create();
            $post->addData($data)->save();
        }
    }
}
