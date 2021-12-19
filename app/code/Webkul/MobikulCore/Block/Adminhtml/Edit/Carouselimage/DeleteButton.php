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

namespace Webkul\MobikulCore\Block\Adminhtml\Edit\Carouselimage;

use Webkul\MobikulCore\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Delete Button
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Fucntion to get Button Data
     *
     * @return array
     */
    public function getButtonData()
    {
        $carouselimageId = $this->getCarouselimageId();
        $data = [];
        if ($carouselimageId) {
            $data = [
                "label" => __("Delete Image"),
                "class" => "delete",
                "id" => "image-edit-delete-button",
                "data_attribute" => ["url"=>$this->getDeleteUrl()],
                "on_click" => "",
                "sort_order" => 20
            ];
        }
        return $data;
    }

    /**
     * Function to get delete url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl("*/*/delete", ["id" => $this->getCarouselimageId()]);
    }
}
