<?php

namespace Zfloos\Zfloos\Controller;

abstract class Zfloos extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Tap\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $_quote = false;

    protected $_zfloosHelper;

    protected $_orderHistoryFactory;

    protected $_storeManagerInterface;

    protected $orderSender;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Zfloos\Zfloos\Helper\Data $zfloosHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory,
        \Zfloos\Zfloos\Helper\Data $zfloosHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_logger = $logger;
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->_orderHistoryFactory = $orderHistoryFactory;
        $this->_zfloosHelper = $zfloosHelper;
        $this->orderSender = $orderSender;
        parent::__construct($context);
    }

    /**
     * Cancel order, return quote to customer
     *
     * @param string $errorMsg
     * @param null $order
     * @return false|string
     */
    public function _cancelPayment($errorMsg = '', $order = null)
    {
        $gotoSection = false;
        $this->_zfloosHelper->cancelCurrentOrder($errorMsg, $order);

        if ($this->_checkoutSession->restoreQuote()) {
            //Redirect to payment step
            $gotoSection = 'paymentMethod';
        }

        return $gotoSection;
    }

    /**
     * Get order object
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function getOrderById($order_id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->get('Magento\Sales\Model\Order');
        $order_info = $order->loadByIncrementId($order_id);
        return $order_info;
    }

    protected function refund()
    {
        exit;
    }
    /**
     * Get order object
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function getOrder()
    {
        return $this->_orderFactory->create()->loadByIncrementId(
            $this->_checkoutSession->getLastRealOrderId()
        );
    }

    protected function addOrderHistory($order, $comment)
    {
        $history = $this->_orderHistoryFactory->create()
            ->setComment($comment)
            ->setEntityName('order')
            ->setOrder($order);
        $history->save();
        return true;
    }

    protected function getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }

    protected function getCheckoutSession()
    {
        return $this->_checkoutSession;
    }

    protected function getCustomerSession()
    {
        return $this->_customerSession;
    }

    protected function getStoreManagerInterface()
    {
        return $this->_storeManagerInterface;
    }

    protected function getZfloosHelper()
    {
        return $this->_zfloosHelper;
    }

    public function curlRequest($method, $is_post = false, $data = [])
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $conf = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $token = $conf->getValue('payment/zfloos/token');
        //$token = "XtYSqveeLpzEm8KIDwVfqhbYQzDA8n9fbucdFApfdXCmzwMUJPBaDq7JU2Pg";
        $iso_code = 'en';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer $token",
            "Language: $iso_code"
        ]);

        if ($is_post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");
        curl_setopt($curl, CURLOPT_URL, "https://zfloos.com/api/v1/" . $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }
}
