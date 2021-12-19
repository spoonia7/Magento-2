<?php

namespace Meetanshi\Knet\Controller\Payment;

use Magento\Framework\UrlInterface;
use Meetanshi\Knet\Controller\Main;
use Magento\Sales\Model\Order;

/**
 * Class Redirect
 * @package Meetanshi\Knet\Controller\Payment
 */
class Redirect extends Main
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $orderIncrementId = $this->checkoutSession->getLastRealOrderId();

        $order = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);

        $storeId = $order->getStoreId();
        $successUrl = $this->helper->getResponseUrl($storeId);
        $errorUrl = $this->helper->getErrorUrl($storeId);

        $tranTrackid = $order->getIncrementId();
        $translID = $this->helper->getTransportalId();
        $transportalID = "id=" . $this->helper->getTransportalId();
        $transportalPass = "password=" . $this->helper->getTransportalPassword();
        $amount = "amt=" . round($order->getGrandTotal(), 3);
        $trackID = "trackid=" . $tranTrackid;
        $currency = "currencycode=414";
        $language = "langid=" . $this->helper->getPaymentLanguage();
        $action = "action=1";
        $responseURL = "responseURL=" . $successUrl;
        $errorURL = "errorURL=" . $errorUrl;

        $paymentUrl = $this->helper->getGatewayUrl();

        $termResourceKey = $this->helper->getResourceKey();

        $param = $transportalID . "&" . $transportalPass . "&" . $action . "&" . $language . "&" . $currency . "&" . $amount . "&" . $responseURL . "&" . $errorURL . "&" . $trackID;
        $params = $this->helper->encryptAES($param, $termResourceKey) . "&tranportalId=" . $translID . "&responseURL=" . $successUrl . "&errorURL=" . $errorURL;
        $url = $paymentUrl . "&trandata=" . $params;

        $resultRedirect = $this->resultRedirectFactory->create();

        $message = 'Customer is redirected to Knet';

        $order->setState(Order::STATE_NEW, true, $message);
        $order->setStatus(Order::STATE_PENDING_PAYMENT);
        $order->save();

        return $resultRedirect->setUrl($url);

    }
}
