<?php

namespace Zkood\CouponsSelling\Block;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Html\Link\Current;
use Magento\Framework\View\Element\Template\Context;
use Zkood\CouponsSelling\Service\SellerOptionService;

/**
 * Class CustomLink
 * @package Zkood\CouponsSelling\Block
 * @method string getRole();
 */

class CustomLink extends Current
{
    const SELLER_CUSTOMER_GROUP = 'Seller';

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $customerSession;
    /**
     * @var \Zkood\CouponsSelling\Service\SellerOptionService
     */
    private $sellerOptionService;

    public function __construct(
        SellerOptionService $sellerOptionService,
        SessionFactory $customerSession,
        Context $context,
        DefaultPathInterface $defaultPath,
        array $data = []
    )
    {
        parent::__construct($context, $defaultPath, $data);
        $this->customerSession = $customerSession;
        $this->sellerOptionService = $sellerOptionService;
    }

    protected function _toHtml()
    {
        /** @var $customer \Magento\Customer\Model\Customer  */
        $customer = $this->customerSession->create()->getCustomer();
        if ($customer->getGroupId() == $this->sellerOptionService->getGroupByCode(static::SELLER_CUSTOMER_GROUP)->getId() && $this->getRole() == 'seller') {
            return parent::_toHtml();
        }
        if ($customer->getGroupId() != $this->sellerOptionService->getGroupByCode(static::SELLER_CUSTOMER_GROUP)->getId() && $this->getRole() == 'customer') {
            return parent::_toHtml();
        }

        return "";
    }
}
