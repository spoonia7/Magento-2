<?php

namespace Zfloos\Zfloos\Controller\Index;

class Index extends \Zfloos\Zfloos\Controller\Zfloos
{
    public function execute()
    {
        $order = $this->getOrder();
        if ($order->getBillingAddress()) {
    //        $this->addOrderHistory($order, '<br/>The customer was redirected to Zfloos');
            $this->buildZfloosRequest($order);
        } else {
            $this->_cancelPayment();
            $this->_checkoutSession->restoreQuote();
            $this->getResponse()->setRedirect(
                $this->getZfloosHelper()->getUrl('checkout')
            );
        }
    }

    public function buildZfloosRequest($order)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $conf = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $get_cancel_order_satus = $conf->getValue('payment/zfloos/order_status');
        $get_complete_order_satus = $conf->getValue('payment/zfloos/complete_order_status');
        $get_pending_order_satus = $conf->getValue('payment/zfloos/pendind_order_status');

        if ($order->getCustomerEmail()) {
            $guestEmail = $order->getCustomerEmail();
        } else {
            $guestEmail = @$_COOKIE['gustuser'];
        }

        $returnUrl = $this->getZfloosHelper()->getUrl('checkout');

        $shipping_amount = $order->getBaseShippingAmount();
        $discount_amount = $order->getBaseDiscountAmount();
        $freight_costs = $order->getGrandTotal() - $order->getSubtotal();
        $billingAddress = $order->getBillingAddress();
        $getCartItem = $order->getAllVisibleItems();
        if ($billingAddress['email'] != '') {
            $email = $billingAddress['email'];
        } else {
            $email = $guestEmail;
        }
        $cart_id = $order->getRealOrderId();
        $data['amount'] = $order->getSubtotal();
        $data['currency'] = 'KWD';
        $data['reference']['type'] = 'id_cart';
        $data['reference']['id'] = 'cart_' . $cart_id;
        $data['metadata']['secure_key'] = 'ca97aa5e815b6711e37b329e3eaf3e6d';
        $data['customer']['name'] = $billingAddress['firstname'] . " " . $billingAddress['lastname'];
        $data['customer']['email'] = $email;
        $data['customer']['mobile']['country_code'] = '';
        $data['customer']['mobile']['number'] = $billingAddress['telephone'];
        $data['order']['freight_costs'] = $shipping_amount;
        $data['order']['discount'] = abs($discount_amount);
        foreach ($getCartItem as $key=>$item) {
            $data['order']['items'][$key]['sku'] = $item->getSku();
            $data['order']['items'][$key]['name'] = $item->getName();
            $data['order']['items'][$key]['quantity'] = $item->getQtyOrdered();
            $data['order']['items'][$key]['discount'] = 0;
            $data['order']['items'][$key]['price'] = $item->getPrice();
        }

        $data['redirect_url'] = $this->getZfloosHelper()->getUrl('payment/index/response');

        $url = $this->curlRequest('payments', 1, $data);

        if ($url != '') {
            if (array_key_exists("url", $url)) {

                    //$this->_redirect($url->url);
                $returnUrl = $url->url;
            } else {
                print_r($url);
            }
        } else {
            //$error = "<p>Not Getting Any Response From Curl Api...</p>";
            die('Not Getting Any Response From Curl Api...');
        }

        $order->setStatus($get_pending_order_satus);

	$this->addOrderHistory($order, '<br/>The customer was redirected to Zfloos<br/>' . '<a href="' . $returnUrl . '">Payment Link</a>');

        $order->save();
        if (isset($_COOKIE['order_custom_id'])) {
            unset($_COOKIE['order_custom_id']);
        }

        $cookie_name = "order_custom_id";
        $orderId = $order->getRealOrderId();
        setcookie($cookie_name, $orderId, time() + (86400 * 30), "/"); // 86400 = 1 day
        $this->getResponse()->setRedirect($returnUrl);
    }
}
