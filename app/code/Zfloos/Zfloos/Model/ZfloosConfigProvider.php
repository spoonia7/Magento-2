<?php

namespace Zfloos\Zfloos\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\UrlInterface as UrlInterface;

class ZfloosConfigProvider implements ConfigProviderInterface
{
    protected $methodCode = "zfloos";

    protected $method;
    
    protected $urlBuilder;

    public function __construct(PaymentHelper $paymentHelper, UrlInterface $urlBuilder) {
        $this->method = $paymentHelper->getMethodInstance($this->methodCode);
        $this->urlBuilder = $urlBuilder;
    }

    public function getConfig()
    {
        return $this->method->isAvailable() ? [
            'payment' => [
                'zfloos' => [
                    'redirectUrl' => $this->urlBuilder->getUrl('payment/index/index', ['_secure' => true])
                ]
            ]
        ] : [];
    }

    protected function getRedirectUrl()
    {
        return $this->_urlBuilder->getUrl('paypal/ipn/');
    }
    
    protected function getFormData()
    {
        return $this->method->getRedirectUrl();
    }
}
