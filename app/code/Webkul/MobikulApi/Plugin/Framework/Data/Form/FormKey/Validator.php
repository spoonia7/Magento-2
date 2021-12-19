<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Webkul\MobikulApi\Plugin\Framework\Data\Form\FormKey;

/**
 * @api
 * @since 100.0.2
 */
class Validator
{
    /**
     * Validate form key
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function aroundValidate(
        \Magento\Framework\Data\Form\FormKey\Validator $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        if (strpos($_SERVER["REQUEST_URI"], "mobikulhttp") !== false || strpos($_SERVER["REQUEST_URI"], "mobikulmphttp") !== false) {
            return true;
        }
        return $proceed($request);
    }
}
