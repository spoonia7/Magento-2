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
 * Class Layer
 */
class Layer extends \Magento\Catalog\Model\Layer
{
    public $customCollection;

    public function getProductCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $wholeData = $objectManager->create("\Magento\Framework\App\Request\Http")->getPostValue();
        if (isset($wholeData["custom"]) && $wholeData["customCollection"] == 1) {
            $this->prepareProductCollection($this->customCollection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $this->customCollection;
            return $this->customCollection;
        } else {
            if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
                $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
            } else {
                $collection = $this->collectionProvider->getCollection($this->getCurrentCategory());
                $this->prepareProductCollection($collection);
                $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
            }
            return $collection;
        }
    }
}
