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
use Webkul\MobikulCore\Api\Data\CacheInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Cache
 */
class Cache extends AbstractModel implements CacheInterface, IdentityInterface
{
    const CACHE_TAG = "mobikul_cache";
    const NOROUTE_ID = "no-route";
    protected $_cacheTag = "mobikul_cache";
    protected $_eventPrefix = "mobikul_cache";

    protected function _construct()
    {
        $this->_init("Webkul\MobikulCore\Model\ResourceModel\Cache");
    }

    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteCache();
        }
        return parent::load($id, $field);
    }

    public function noRouteCache()
    {
        return $this->load(self::NOROUTE_ID, $this->getIdFieldName());
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . "_" . $this->getId()];
    }

    public function getId()
    {
        return parent::getData(self::ID);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function getETag()
    {
        return parent::getData(self::E_TAG);
    }

    public function setETag($eTag)
    {
        return $this->setData(self::E_TAG, $eTag);
    }

    public function getCounter()
    {
        return parent::getData(self::COUNTER);
    }

    public function setCounter($counter)
    {
        return $this->setData(self::COUNTER, $counter);
    }

    public function getRequestTag()
    {
        return parent::getData(self::REQUEST_TAG);
    }

    public function setRequestTag($requestTag)
    {
        return $this->setData(self::REQUEST_TAG, $requestTag);
    }
}
