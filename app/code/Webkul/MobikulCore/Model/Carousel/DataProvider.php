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

namespace Webkul\MobikulCore\Model\Carousel;

use Magento\Eav\Model\Config;
use Webkul\MobikulCore\Model\Carousel;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Session\SessionManagerInterface;
use Webkul\MobikulCore\Model\ResourceModel\Carousel\Collection;
use Webkul\MobikulCore\Model\ResourceModel\Carousel\CollectionFactory as CarouselCollectionFactory;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $collection;
    protected $loadedData;
    protected $session;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CarouselCollectionFactory $carouselCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $carouselCollectionFactory->create();
        $this->collection->addFieldToSelect("*");
    }

    protected function getSession()
    {
        if ($this->session === null) {
            $this->session = ObjectManager::getInstance()->get("Magento\Framework\Session\SessionManagerInterface");
        }
        return $this->session;
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $carousel) {
            $result["carousel"] = $carousel->getData();
            $this->loadedData[$carousel->getId()] = $result;
        }
        $data = $this->getSession()->getCarouselFormData();
        if (!empty($data)) {
            $carouselId = $data["mobikul_carousel"]["id"] ?? null;
            $this->loadedData[$carouselId] = $data;
            $this->getSession()->unsCarouselFormData();
        }
        return $this->loadedData;
    }
}
