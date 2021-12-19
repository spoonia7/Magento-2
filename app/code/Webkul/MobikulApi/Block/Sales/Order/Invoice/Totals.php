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

namespace Webkul\MobikulApi\Block\Sales\Order\Invoice;

/**
 * Class Totals
 */
class Totals extends \Magento\Sales\Block\Order\Invoice\Totals
{
    /**
     * Function to remove grandTotals
     *
     * @return object this
     */
    public function _initTotals()
    {
        parent::_initTotals();
        $this->removeTotal("base_grandtotal");
        return $this;
    }
}
