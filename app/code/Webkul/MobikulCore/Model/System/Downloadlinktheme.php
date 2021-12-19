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

namespace Webkul\MobikulCore\Model\System;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Downloadlinktheme
 */
class Downloadlinktheme implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $options = [];
        array_push($options, ["value"=>"mk-lightTheme", "label"=>"Light Theme"]);
        array_push($options, ["value"=>"mk-darkTheme", "label"=>"Dark Theme"]);
        return $options;
    }
}
