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

namespace Webkul\Mobikul\Model\ResourceModel\OauthToken;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = "entity_id";

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init("Webkul\Mobikul\Model\OauthToken", "Webkul\Mobikul\Model\ResourceModel\OauthToken");
    }
}
