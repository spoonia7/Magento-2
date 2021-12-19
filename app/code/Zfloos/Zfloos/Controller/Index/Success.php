<?php
/**
 * Checkout plugin for Magento
 *
 * @package     Yireo_Checkout
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

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
class Success extends Action
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
        // Check access
        if ($this->moduleHelper->hasAccess() == false) {
            throw new ForbiddenAccess('Access denied');
        }

        // Fetch the order
        $order = $this->getOrder();

        // Fail when there is no valid order
        if (!$order->getEntityId()) {
            throw new InvalidOrderId('Invalid order ID');
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle('checkouttester_index_index');

        // Register this order
        $this->registerOrder($order);
        
        return $resultPage;
    }

    /**
     * Method to fetch the current order
     *
     * @return OrderInterface
     */
    protected function getOrder() : OrderInterface
    {
        $orderIdFromUrl = (int)$this->getRequest()->getParam('order_id');
        $order = $this->orderHelper->getOrderById($orderIdFromUrl);
        if ($order->getEntityId()) {
            return $order;
        }

        $orderIdFromConfig = (int)$this->moduleHelper->getOrderIdFromConfig();
        $order = $this->orderHelper->getOrderById($orderIdFromConfig);
        if ($order->getEntityId()) {
            return $order;
        }

        $lastOrderId = $this->orderHelper->getLastInsertedOrderId();
        $order = $this->orderHelper->getOrderById($lastOrderId);

        if ($order->getEntityId()) {
            return $order;
        }

        return $this->orderHelper->getEmptyOrder();
    }

    /**
     * Method to register the order in this session
     *
     * @param OrderInterface $order
     */
    protected function registerOrder(OrderInterface $order)
    {
        // Register this order as the current order
        $currentOrder = $this->registry->registry('current_order');
        if (empty($currentOrder)) {
            $this->registry->register('current_order', $order);
        }

        // Load the session with this order
        $this->checkoutSession->setLastOrderId($order->getEntityId())
            ->setLastRealOrderId($order->getIncrementId());

        // Optionally dispatch an event
        $this->dispatchEvents($order);
    }

    /**
     * Method to optionally dispatch order-related events
     *
     * @param OrderInterface $order
     */
    public function dispatchEvents(OrderInterface $order)
    {
        if ($this->moduleHelper->allowDispatchCheckoutOnepageControllerSuccessAction()) {
            $eventData = array('order_ids' => array($order->getEntityId()));
            $this->_eventManager->dispatch('checkout_onepage_controller_success_action', $eventData);
        }
    }
}
