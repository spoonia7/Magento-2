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

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class OrderPurchasePoint Model.
 */
class OrderPurchasePoint extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = "mobikul_appcreator";
    const NOROUTE_ID = "no-route";
    protected $_cacheTag = "mobikul_orderpurchasepoint";
    protected $_eventPrefix = "mobikul_orderpurchasepoint";

    protected function _construct()
    {
        $this->_init(\Webkul\MobikulCore\Model\ResourceModel\OrderPurchasePoint::class);
    }
}
