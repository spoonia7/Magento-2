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

namespace Webkul\MobikulCore\Block\Adminhtml\Edit\Categoryimages;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Webkul\MobikulCore\Block\Adminhtml\Edit\GenericButton;

/**
 * Class SaveButton.
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Function to get Button Data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [
            "label" => __("Save Category Image"),
            "class" => "save primary",
            "data_attribute" => [
                "mage-init" => ["button"=>["event"=>"save"]],
                "form-role" => "save"
            ],
            "sort_order" => 90
        ];
        return $data;
    }
}
