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

namespace Webkul\MobikulCore\Model\Bannerimage;

use Magento\Framework\Convert\ConvertArray;
use Webkul\MobikulCore\Api\Data\BannerimageInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

/**
 * Class Mapper
 */
class Mapper
{
    private $_extensibleDataObjectConverter;

    public function __construct(
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->_extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    public function toFlatArray(BannerimageInterface $bannerimage)
    {
        $flatArray = $this->_extensibleDataObjectConverter->toNestedArray(
            $bannerimage,
            [],
            "\Webkul\MobikulCore\Api\Data\BannerimageInterface"
        );
        return ConvertArray::toFlatArray($flatArray);
    }
}
