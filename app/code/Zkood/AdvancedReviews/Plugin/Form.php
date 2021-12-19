<?php

namespace Zkood\AdvancedReviews\Plugin;

use Magento\Customer\Model\SessionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Framework\Registry;

class Form
{
    /**
     * Customer Session Factory
     *
     * @var SessionFactory
     */
    protected $customerSession;

    /**
     * Order Collection Factory
     *
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    public function __construct(
        SessionFactory $customerSession,
        OrderCollectionFactory $orderCollectionFactory,
        Registry $registry
    )
    {
        $this->customerSession = $customerSession;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->registry = $registry;
    }


    public function before__call(\Magento\Review\Block\Form $subject, $method, $args)
    {
        if ($method == 'getAllowWriteReviewFlag') {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/malaz.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($method);
            $logger->info(json_encode($args));
        }
        return null;
    }

    public function afterGetAllowWriteReviewFlag(\Magento\Review\Block\Form $subject, $result)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/malaz.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Your text message');
        return false;
        if (!$this->getCurrentCustomerId()) {
//            return $result;
        }

        if (!$this->isCurrentCustomerPurchasedThisProduct()) {
//            return false;
        }

//        return $result;

    }

    public function getCurrentCustomerId()
    {
        return $this->customerSession->create()->getCustomer()->getId();
    }

    public function isCurrentCustomerPurchasedThisProduct(): bool
    {
        $product_ids = [];
        foreach ($this->getCustomerOrders() as $order) {
            foreach ($order->getAllVisibleItems() as $item) {
                $product_ids[$item->getProductId()] = $item->getProductId();
            }
        }

        if (in_array($this->getCurrentProduct()->getId(), $product_ids)) {
            return true;
        } else {
            return false;
        }
    }

    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getCustomerOrders()
    {
        $orders = $this->orderCollectionFactory->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'customer_id',
            $this->getCurrentCustomerId()
        );

        return $orders;
    }
}
