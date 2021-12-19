<?php

namespace Zfloos\Zfloos\Controller\Index;

use Magento\Sales\Model\Order\Payment\Transaction;

// use Magento\Sales\Model\Service\InvoiceService;

//use Magento\Framework\DB\Transaction;

class Response extends \Zfloos\Zfloos\Controller\Zfloos
{
    public function createTransaction($order = null, $paymentData = [])
    {
        try {
            //get payment object from order object
            $payment = $order->getPayment();
            $payment->setLastTransId($paymentData['track_id']);
            $payment->setTransactionId($paymentData['track_id']);
            $payment->setAdditionalInformation(
                [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $paymentData]
            );
            $formatedPrice = $order->getBaseCurrency()->formatTxt(
                $order->getGrandTotal()
            );

            $message = __('The authorized amount is %1.', $formatedPrice);
            //get the object of builder class
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $transactionBuilder = $objectManager->get('\Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface');
            $trans = $transactionBuilder;
            $transaction = $trans->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($paymentData['track_id'])
            ->setAdditionalInformation(
                [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $paymentData]
            )
            ->setFailSafe(true)
            //build method creates the transaction and returns the object
            ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);

            $payment->addTransactionCommentsToOrder(
                $transaction,
                $message
            );
            $payment->setParentTransactionId(null);
            $payment->save();
            $order->save();

            return  $transaction->save()->getTransactionId();
        } catch (\Exception $e) {
            //log errors here
        }
    }

    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $conf = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $get_cancel_order_satus = $conf->getValue('payment/zfloos/order_status');
        $get_complete_order_satus = $conf->getValue('payment/zfloos/complete_order_status');
        $get_pending_order_satus = $conf->getValue('payment/zfloos/pendind_order_status');
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $baseUrl = $storeManager->getStore()->getBaseUrl();

        $orderid = '';
        if (isset($_COOKIE['order_custom_id'])) {
            $orderid = $_COOKIE['order_custom_id'];
        }

        $order = $this->getOrderById($orderid);
        $payment = $order->getPayment();
        $comment = "";
        $successFlag=   false;
        $errorMsg = '';

        if (isset($_REQUEST['track_id'])) {
            $ref = $_REQUEST['track_id'];
            $trackId = "payments/" . $_REQUEST['track_id'];
            $track = $this->curlRequest($trackId);

            $orderId = str_replace('cart_', '', $track->reference->id);

            $order = $this->getOrderById($orderId);
            $payment = $order->getPayment();

            if (($track->status == 'failed') || ($track->status == '')) {
                $errorMsg = 'Zfloos Transaction Failed ! Transaction was cancelled.';
                $comment .=  "Payment cancelled by user";
                $this->_cancelPayment("Payment cancelled by user", $order);
                $order->setStatus($get_cancel_order_satus);
                //$order->save();
                //$returnUrl = $this->getZfloosHelper()->getUrl('payment/index/fail/?order='.$orderid);
                $returnUrl = $baseUrl . 'payment/index/fail/?order=' . $orderid; ?>

            <?php
            } else {
                $transaction_id = $this->createTransaction($order, $_REQUEST);

                $objectManager2 = \Magento\Framework\App\ObjectManager::getInstance();
                $invioce = $objectManager2->get('\Magento\Sales\Model\Service\InvoiceService')->prepareInvoice($order);
                $invioce->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
                $invioce->register();

                $invioce->setState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
                $invioce->setTransactionId($ref);
                $invioce->save();

                $payment->setTransactionId($ref);
                $payment->setParentTransactionId($payment->getTransactionId());
                $transaction = $payment->addTransaction(
                    \Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH,
                    null,
                    true,
                    ""
                );
                $transaction->setIsClosed(true);

                $successFlag = true;
                $comment .=  '<br/><b>Zfloos payment successful</b><br/><br/>Order ID - ' . $orderid . '<br/><br/>Track Id - ' . $_REQUEST['track_id'];
                $order->setStatus($get_complete_order_satus);
                $order->setExtOrderId($orderid);
                //$returnUrl = $this->getZfloosHelper()->getUrl('payment/index/success/order_id/'.$orderid);
                $returnUrl = $baseUrl . 'payment/index/success/order_id/' . $orderid;
            }
        } else {
            $errorMsg = 'Zfloos Transaction Failed ! Fraud has been detected ! Not Getting Any Response From Curl Api...';
            $comment .=  "Fraud Deducted";
            $order->setStatus($order::STATUS_FRAUD);
            //$returnUrl = $this->getZfloosHelper()->getUrl('payment/index/fail/?order='.$orderid);
            $returnUrl = $baseUrl . 'payment/index/fail/?order=' . $orderid;
        }

        $this->addOrderHistory($order, $comment);
        $order->save();
        if ($successFlag) {
            $this->messageManager->addSuccess(__('Zfloos transaction has been successful.'));
            $this->orderSender->send($order);
        } else {
            $this->messageManager->addError(__($errorMsg));
        }
        $this->getResponse()->setRedirect($returnUrl);
    }
}
