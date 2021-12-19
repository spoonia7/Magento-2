<?php

namespace Zkood\CouponsSelling\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Zkood\CouponsSelling\Model\ResourceModel\Coupon\CollectionFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

class CustomerCoupons extends Template
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

    public function __construct(
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
    }

    public function isScopePrivate()
    {
        return true;
    }

    public function getCustomerCoupons()
    {
        $customer = $this->sessionFactory->create()->getCustomer();
        $coupons = $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $customer->getId())
            ->setOrder('created_at', 'DESC')
            ->load();
        foreach ($coupons as $coupon) {
            $coupon->setProduct($this->productRepository->getById($coupon->getProductId()));
        }
        return $coupons;
    }
}
