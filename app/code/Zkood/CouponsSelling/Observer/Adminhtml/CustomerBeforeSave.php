<?php

namespace Zkood\CouponsSelling\Observer\Adminhtml;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Zkood\CouponsSelling\Service\SellerOptionService;

class CustomerBeforeSave implements ObserverInterface
{
    const SELLER_CUSTOMER_GROUP = 'Seller';

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var \Zkood\CouponsSelling\Service\SellerOptionService
     */
    private $sellerOptionService;

    /**
     * CustomerBeforeSave constructor.
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SellerOptionService $sellerOptionService
    )
    {
        $this->customerRepository = $customerRepository;
        $this->sellerOptionService = $sellerOptionService;
    }

    /**
     * @param Observer $observer
     * @return $this
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $sellerGroup = $this->sellerOptionService->getGroupByCode(static::SELLER_CUSTOMER_GROUP);

        if (!$sellerGroup) {
            return $this;
        }
        /**
         * @var \Magento\Customer\Model\Data\Customer $customer
         */
        $customer = $observer->getCustomer();
        if ($customer->getGroupId() !== $sellerGroup->getId()) {
            if ($customer->getCustomAttribute('seller_code')) {
                $customer->setGroupId($this->sellerOptionService->getGroupByCode(static::SELLER_CUSTOMER_GROUP)->getId());
            }
            return $this;
        }
        $this->sellerOptionService->generateSellerAttributeOption($customer);
        return $this;
    }
}
