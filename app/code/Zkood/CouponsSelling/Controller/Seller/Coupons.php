<?php

namespace Zkood\CouponsSelling\Controller\Seller;

use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\SessionFactory;
use Zkood\CouponsSelling\Service\SellerOptionService;
use \Zkood\CouponsSelling\Service\CouponService;

class Coupons extends AbstractAccount
{

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
private $couponService;
    public function __construct(
        SellerOptionService $sellerOptionService,
        SessionFactory $sessionFactory,
        PageFactory $resultPageFactory,
        Context $context,
        \Zkood\CouponsSelling\Service\CouponService $couponService
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->sessionFactory = $sessionFactory;
        $this->sellerOptionService = $sellerOptionService;
        $this->couponService=$couponService;
    }

    public function execute()
    {
        $customer = $this->sessionFactory->create()->getCustomer();
        if ($customer->getGroupId() != $this->sellerOptionService->getGroupByCode('Seller')->getId()) {
            return $this->_redirect('customer/account');
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Coupons Details'));
        return $resultPage;
    }
}
