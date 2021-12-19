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

namespace Webkul\MobikulApi\Block\Sales\Order\Creditmemo;

/**
 * Class Totals
 */
class Totals extends \Magento\Sales\Block\Order\Creditmemo\Totals
{
    /**
     * Function _initTotals
     *
     * @return object this
     */
    public function _initTotals()
    {
        parent::_initTotals();
        $this->removeTotal("base_grandtotal");
        if ((double)$this->getSource()->getAdjustmentPositive()) {
            $total = new \Magento\Framework\DataObject(
                [
                    "code" => "adjustment_positive",
                    "value" => $this->getSource()->getAdjustmentPositive(),
                    "label" => __("Adjustment Refund")
                ]
            );
            $this->addTotal($total);
        }
        if ((double)$this->getSource()->getAdjustmentNegative()) {
            $total = new \Magento\Framework\DataObject(
                [
                    "code" => "adjustment_negative",
                    "value" => $this->getSource()->getAdjustmentNegative(),
                    "label" => __("Adjustment Fee")
                ]
            );
            $this->addTotal($total);
        }
        return $this;
    }
}
