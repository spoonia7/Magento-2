<?php

namespace Zkood\CouponsSelling\Block;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\View\Element\Template;
use Zkood\CouponsSelling\Model\ResourceModel\Coupon\CollectionFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

class SellerCoupons extends Template
{
    /**
     * @var CollectionFactory
     */

    private $collectionFactory;
    /**
     * @var SessionFactory
     */
    private $sessionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        ProductRepositoryInterface $productRepository,
        SessionFactory $sessionFactory,
        CollectionFactory $collectionFactory,
        Template\Context $context,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->sessionFactory = $sessionFactory;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
    }

    public function isScopePrivate()
    {
        return true;
    }

    public function getRedeemedCoupons()
    {
        $customer = $this->sessionFactory->create()->getCustomer();
        return $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('seller_id', $customer->getId())
            ->addFieldToFilter('is_redeemed', 1);
    }

    public function getExpiredCoupons()
    {
        $customer = $this->sessionFactory->create()->getCustomer();
        return $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('seller_id', $customer->getId())
            ->addFieldToFilter('valid_to', ['lt' => date("Y-m-d H:i:s")])
            ->addFieldToFilter('is_redeemed', 0)
            ->load();
    }

    public function getAvailableCoupons()
    {
        $customer = $this->sessionFactory->create()->getCustomer();
        return $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('seller_id', $customer->getId())
            ->addFieldToFilter('valid_to', ['gt' => date("Y-m-d H:i:s")])
            ->addFieldToFilter('is_redeemed', 0)
            ->load();
    }

    public function getTotalCoupons()
    {
        $customer = $this->sessionFactory->create()->getCustomer();
        return $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('seller_id', $customer->getId())
            ->load();
    }

    public function getRedeemedCouponsData()
    {
        $customer = $this->sessionFactory->create()->getCustomer();
        $coupons = $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('seller_id', $customer->getId())
            ->addFieldToFilter('is_redeemed', 1)
            ->load();

        foreach ($coupons as $coupon) {
            try {
                $coupon->setProduct($this->productRepository->getById($coupon->getProductId()));
                if ($customerId = $coupon->getCustomerId()) {
                    $coupon->setCustomer($this->customerRepository->getById($customerId));
                }
            }catch (\Exception $e) {
            }
        }

        return $coupons;
    }
}
