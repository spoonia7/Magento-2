<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_MobikulApi
 * @author    Webkul <support@webkul.com>
 * @copyright 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\MobikulApi\Controller\Checkout;
use Magento\Sales\Model\Order;
/**
 * Class ChangeOrderStatus
 * change the status of order
 */
class ChangeOrderStatusCustom extends AbstractCheckout
{

    protected $order_id;
    protected $payment_status;
    protected $track_id;
    protected $store_Id;

    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        try {
            $this->verifyRequest();
            $environment = $this->emulate->startEnvironmentEmulation($this->storeId);

            // change order status
            $canceledStatus = $this->helper->getConfigData('payment/zfloos/order_status');
            $completeStatus = $this->helper->getConfigData('payment/zfloos/complete_order_status');
            $pendingStatus = $this->helper->getConfigData('payment/zfloos/pendind_order_status');

            $orderRepo = $objectManager->create('Magento\Sales\Api\OrderRepositoryInterface');

//            $order = $this->orderFactory->create()->loadByIncrementId($this->order_id);
            $order = $orderRepo->get($this->order_id);

            if (!$order->getId()) {
                throw new \Exception(__('Order not found !'));
            }

            $payment = $order->getPayment();
            if (!$payment) {
                throw new \Exception('This order does not has payment!');
            }

            if ($order->getStatus() != $pendingStatus) {
                throw new \Exception(__('This order cannot be changed!'));
            }

            $message = '';

            if ($this->payment_status == 0) {
                $errorMsg = 'Zfloos Transaction Failed ! Transaction was cancelled.';
                $comment = "Payment cancelled by user";
                $this->cancelPayment("Payment cancelled by user", $order);
                $order->setStatus($canceledStatus);
                $order->save();
                $message = 'order canceled.';
                $this->returnArray["success"] = false;
            }

            if ($this->payment_status == 1) {
                $transaction_id = $this->createTransaction($order, $this->track_id);
                $invoice = $objectManager->get('\Magento\Sales\Model\Service\InvoiceService')->prepareInvoice($order);
                $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
                $invoice->register();

                $invoice->setState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
                $invoice->setTransactionId($this->track_id);
                $invoice->save();

                $payment->setTransactionId($this->track_id);
                $payment->setParentTransactionId($payment->getTransactionId());
                $transaction = $payment->addTransaction(
                    \Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH,
                    null,
                    true,
                    ""
                );
                $transaction->setIsClosed(true);
                $successFlag = true;
                $comment =  'Zfloos payment successful, Order ID - ' . $this->order_id . ', Track Id - ' . $this->track_id;
                $order->setStatus($completeStatus);
                $order->setExtOrderId($this->order_id);
                $order->save();
                $message = 'order complete.';
                $this->returnArray["success"] = true;
            }

            $this->returnArray["message"] = $message;

            $this->emulate->stopEnvironmentEmulation($environment);
            $this->helper->log($this->returnArray, "logResponse", $this->wholeData);
            return $this->getJsonResponse($this->returnArray);
        } catch (\Exception $e) {
            $this->returnArray["message"] = $e->getMessage();
            $this->helper->printLog($this->returnArray);
            return $this->getJsonResponse($this->returnArray);
        }
    }

    /**
     * Function to verify request
     *
     * @return void|json
     */
    protected function verifyRequest()
    {
        if (!$this->customerToken && $this->customerToken != "") {
            $this->returnArray["otherError"] = "customerNotExist";
            throw new \Magento\Framework\Exception\LocalizedException(
                __("Customer you are requesting does not exist.")
            );
        }
        if ($this->getRequest()->getMethod() != "POST" || !$this->wholeData) {
            throw new \Exception(__('Invalid Request'));
        }

        if (!isset($this->wholeData["order_id"])) {
            throw new \Exception(__('Please provide order_id'));
        }

        if (!isset($this->wholeData["payment_status"])) {
            throw new \Exception(__('Please provide payment_status'));
        }

        if (!isset($this->wholeData["track_id"])) {
            throw new \Exception(__('Please provide track_id'));
        }

        $this->order_id = $this->wholeData["order_id"];
        $this->payment_status = (int)$this->wholeData["payment_status"];
        $this->track_id = $this->wholeData["track_id"];
        $this->store_Id = $this->wholeData["store_Id"] ?? 1;
    }

    public function createTransaction($order = null, $track_id = '')
    {
        if (is_null($order)) {
            return 0;
        }
        //get payment object from order object
        $payment = $order->getPayment();
        $payment->setLastTransId($track_id);
        $payment->setTransactionId($track_id);

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
            ->setTransactionId($track_id)
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

        return $transaction->save()->getTransactionId();
    }

    /**
     * @param string $comment
     * @param null $order
     * @return void
     */
    public function cancelPayment($comment = '', $order = null)
    {
        if (is_null($order)) {
            return;
        }
        if ($order->getId() && $order->getState() != Order::STATE_CANCELED) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $conf = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
            $get_cancel_order_satus = $conf->getValue('payment/zfloos/order_status');
            $order->registerCancellation($comment)->save();
        }
    }
}
