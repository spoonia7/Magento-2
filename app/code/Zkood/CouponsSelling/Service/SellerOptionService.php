<?php

namespace Zkood\CouponsSelling\Service;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\Data\Customer;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Zkood\CouponsSelling\Helper\Data;

class SellerOptionService
{
    const SELLER_ATTRIBUTE_CODE = 'seller';

    /**
     * @var \Zkood\CouponsSelling\Helper\Data
     */
    private $helper;
    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    private $groupRepository;

    public function __construct(
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GroupRepositoryInterface $groupRepository,
        Data $helper
    )
    {
        $this->helper = $helper;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->groupRepository = $groupRepository;
    }

    public function generateSellerAttributeOption(Customer $customer)
    {
        $customerFullName = $customer->getFirstname() . " " . $customer->getLastname();

        if ($sellerCodeAttribute = $customer->getCustomAttribute('seller_code')) {
            $label = $sellerCodeAttribute->getValue() . " (" . $customerFullName . ")";
            $this->helper->createOrGetId(static::SELLER_ATTRIBUTE_CODE, $label);
            return;
        }
        $sellerCode = strtoupper(str_shuffle($this->helper->generateRandomString(5)));
        $customer->setCustomAttribute('seller_code', $sellerCode);
        $label = $sellerCode . " (" . $customerFullName . ")";
        $this->helper->createOrGetId(static::SELLER_ATTRIBUTE_CODE, $label);
    }

    public function getGroupByCode($code)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilters([
            $this->filterBuilder->setField('customer_group_code')->setValue($code)->create()
        ]);
        $items = $this->groupRepository->getList($searchCriteria->create())->getItems();
        $sellerGroup = array_shift($items);
        if (!$sellerGroup) {
            return false;
        }
        return $sellerGroup;
    }
}
