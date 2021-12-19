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

namespace Webkul\MobikulCore\Model\ResourceModel;

/**
 * Class Bannerimage
 */
class Bannerimage extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $store = null;

    protected function _construct()
    {
        $this->_init("mobikul_bannerimage", "id");
    }

    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && $field === null) {
            $field = "identifier";
        }
        return parent::load($object, $value, $field);
    }

    public function setStore($store)
    {
        $this->store = $store;
        return $this;
    }

    public function getStore()
    {
        return $this->_storeManager->getStore($this->store);
    }
}
