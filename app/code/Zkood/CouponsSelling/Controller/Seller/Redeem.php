<?php

namespace Zkood\CouponsSelling\Controller\Seller;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\SessionFactory;
use Zkood\CouponsSelling\Service\SellerOptionService;
use Zkood\CouponsSelling\Model\ResourceModel\Coupon\CollectionFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class Redeem extends AbstractAccount implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var PageFactory
     */
    private $resultPageFactory;
    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;
    /**
     * @var \Zkood\CouponsSelling\Service\SellerOptionService
     */
    private $sellerOptionService;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        JsonFactory  $resultJsonFactory,
        CollectionFactory $collectionFactory,
        SellerOptionService $sellerOptionService,
        SessionFactory $sessionFactory,
        PageFactory $resultPageFactory,
        Context $context
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->sessionFactory = $sessionFactory;
        $this->sellerOptionService = $sellerOptionService;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        $customer = $this->sessionFactory->create()->getCustomer();
        if ($customer->getGroupId() != $this->sellerOptionService->getGroupByCode('Seller')->getId()) {
            return $result->setData([
                'error' => 1,
                'status' => 401,
                'message' => 'Not Authorized'
            ]);
        }
        $coupon = $this->collectionFactory->create()
            ->addFieldToFilter('coupon_code', $this->getRequest()->getParam('coupon_code'))
            ->load()
            ->getFirstItem();

        if (is_null($coupon->getCouponCode())) {
            return $result->setData([
                'error' => 1,
                'status' => 200,
                'message' => 'Invalid Coupon Code!'
            ]);
        }

        if ($coupon->getIsRedeemed()) {
            return $result->setData([
                'error' => 1,
                'status' => 200,
                'message' => 'Coupon Already Used'
            ]);
        }

        if ($coupon->getValidTo() < date("Y-m-d H:i:s")) {
            return $result->setData([
                'error' => 1,
                'status' => 200,
                'message' => 'Coupon Expired'
            ]);
        }

        if ($coupon->getSellerId() != $customer->getId()) {
            return $result->setData([
                'error' => 1,
                'status' => 200,
                'message' => 'Invalid Coupon Code.'
            ]);
        }

        $coupon->setIsRedeemed(1);
        $coupon->save();

        return $result->setData([
            'error' => 0,
            'status' => 200,
            'message' => 'Coupon Redeemed!'
        ]);
    }
}
