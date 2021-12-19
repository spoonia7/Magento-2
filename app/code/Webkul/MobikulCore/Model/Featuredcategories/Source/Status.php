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

namespace Webkul\MobikulCore\Model\Featuredcategories\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 */
class Status implements OptionSourceInterface
{

    protected $mobikulFeaturedcategories;

    public function __construct(
        \Webkul\MobikulCore\Model\Featuredcategories $mobikulFeaturedcategories
    ) {
        $this->mobikulFeaturedcategories = $mobikulFeaturedcategories;
    }

    public function toOptionArray()
    {
        $availableOptions = $this->mobikulFeaturedcategories->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                "label" => $value,
                "value" => $key
            ];
        }
        return $options;
    }
}
