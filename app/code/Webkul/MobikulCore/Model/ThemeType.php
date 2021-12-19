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

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Cmspages
 */
class ThemeType implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $returnData[] =  [
            "value" => 0,
            "label" => "Theme One"
        ];
        $returnData[] =  [
            "value" => 1,
            "label" => "Theme Two"
        ];
        return $returnData;
    }
}
