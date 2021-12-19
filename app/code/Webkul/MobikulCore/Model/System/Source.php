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
 * Class Source
 */
class Source implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $options = [];
        array_push($options, ["value"=>1, "label"=>"Red-Green"]);
        array_push($options, ["value"=>2, "label"=>"Light Green"]);
        array_push($options, ["value"=>3, "label"=>"Deep Purple-Pink"]);
        array_push($options, ["value"=>4, "label"=>"Blue-Orange"]);
        array_push($options, ["value"=>5, "label"=>"Light Blue-Red"]);
        return $options;
    }
}
