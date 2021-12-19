<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_MobikulCore
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\MobikulCore\Model\ResourceModel\Bannerimage;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = "id";

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategyInterface,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $loggerInterface,
            $fetchStrategyInterface,
            $eventManager,
            $connection,
            $resource
        );
    }

    protected function _construct()
    {
        $this->_init("Webkul\MobikulCore\Model\Bannerimage", "Webkul\MobikulCore\Model\ResourceModel\Bannerimage");
        $this->_map["fields"]["id"] = "main_table.id";
    }

    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag("store_filter_added")) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    public function setBannerimageData($condition, $attributeData)
    {
        return $this->getConnection()->update(
            $this->getTable("mobikul_bannerimage"),
            $attributeData,
            $where = $condition
        );
    }
}
