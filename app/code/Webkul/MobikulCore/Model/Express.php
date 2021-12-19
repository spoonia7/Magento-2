<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_MobikulCore
 * @author    Webkul <support@webkul.com>
 * @copyright 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\MobikulCore\Model;

/**
 * Class Express
 */
class Express extends \Magento\Paypal\Model\Express
{

    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $om->get("Magento\Framework\App\RequestInterface");
        if ($request->getHeader("authKey")) {
            return true;
        }
        parent::authorize($payment, $amount);
    }
}
