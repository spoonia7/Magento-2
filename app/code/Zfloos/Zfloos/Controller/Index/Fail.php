<?php
namespace Zfloos\Zfloos\Controller\Index;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Zfloos\Zfloos\Exception\ForbiddenAccess;
use Zfloos\Zfloos\Exception\InvalidOrderId;
use Zfloos\Zfloos\Helper\Data;
use Zfloos\Zfloos\Helper\Order;

/**
 * CheckoutTester frontend controller
 *
 * @category    Checkout
 * @package Zfloos\Zfloos\Controller\Index
 */
class Fail extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var Data
     */
    protected $moduleHelper;


    /**
     * @var Order
     */
    protected $orderHelper;

    /**
     * Success constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param Session $checkoutSession
     * @param Data $moduleHelper
     * @param Order $orderHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Session $checkoutSession,
        Data $moduleHelper,
        Order $orderHelper
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->checkoutSession = $checkoutSession;
        $this->moduleHelper = $moduleHelper;
        $this->orderHelper = $orderHelper;
    }

    /**
     * Success page action
     *
     * @return Page
     *
     * @throws ForbiddenAccess
     * @throws InvalidOrderId
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle('zfloos_index_fail');
        
        return $resultPage;
    }
}
