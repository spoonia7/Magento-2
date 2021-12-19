<?php
/**
 * Checkout plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Zfloos\Zfloos\Helper;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;

/**
 * Class \Zfloos\Zfloos\Helper\Data
 */
class Data extends AbstractHelper
{

    /**
     * @var Session
     */
    protected $session;

    /**
     * Data constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context,
        Session $session
    ) {
        $this->session = $session;
        return parent::__construct($context);
    }

    /**
     * Switch to determine whether this extension is enabled or not
     *
     * @return bool
     */
    public function enabled() : bool
    {
        if ((bool)$this->getConfigValue('enabled')) {
            return true;
        }

        return false;
    }

    /**
     * Method to determine whether the current user has access to this page
     *
     * @return bool
     */
    public function hasAccess() : bool
    {
        $ip = (string)$this->getConfigValue('ip');
        if (!is_null($ip)) {
            $ip = trim($ip);
        }

        $realIp = $this->getIpAddress();

        if (!empty($ip) && $realIp) {
            $ips = explode(',', $ip);

            foreach ($ips as $ip) {
                $ip = trim($ip);

                if (empty($ip)) {
                    continue;
                }

                if ($ip == $realIp) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }

    /**
     * Get the current IP address
     *
     * @return string
     */
    public function getIpAddress() : string
    {
        $ip = $this->_request->getClientIp();
        $forwarded = explode(', ', $ip);
        return ($forwarded ? $forwarded[0] : $ip);
    }

    /**
     * Return the order ID
     *
     * @return int
     */
    public function getOrderIdFromConfig() : int
    {
        return (int)$this->getConfigValue('order_id');
    }

    /**
     * Check whether the module is enabled
     *
     * @return bool
     */
    public function allowDispatchCheckoutOnepageControllerSuccessAction() : bool
    {
        return (bool)$this->getConfigValue('checkout_onepage_controller_success_action', false);
    }

    /**
     * Return a configuration value
     *
     * @param string $key
     * @param mixed $defaultValue
     * @param bool $prefix
     *
     * @return mixed
     */
    public function getConfigValue(string $key = '', $defaultValue = null, $prefix = true)
    {
        if ($prefix) {
            $key = 'checkouttester2/settings/' . $key;
        }

        $value = $this->scopeConfig->getValue(
            $key,
            ScopeInterface::SCOPE_STORE
        );

        if (empty($value)) {
            $value = $defaultValue;
        }

        return $value;
    }

    public function cancelCurrentOrder($comment, $order = null): bool
    {
        if (is_null($order)) {
            $order = $this->session->getLastRealOrder();
        }
        if ($order->getId() && $order->getState() != Order::STATE_CANCELED) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $conf = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
            $get_cancel_order_satus = $conf->getValue('payment/zfloos/order_status');
            $order->registerCancellation($comment)->save();
            return true;
        }
        return false;
    }

    public function restoreQuote()
    {
        return $this->session->restoreQuote();
    }

    public function getUrl($route, $params = [])
    {
        return $this->_getUrl($route, $params);
    }
}
