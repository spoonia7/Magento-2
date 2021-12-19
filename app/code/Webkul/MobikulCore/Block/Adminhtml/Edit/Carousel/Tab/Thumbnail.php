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

namespace Webkul\MobikulCore\Block\Adminhtml\Edit\Carousel\Tab;

/**
 * Class Thumbnail
 */
class Thumbnail extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Function to Render data rows
     *
     * @param \Magento\Framework\DataObject $row row
     *
     * @return html
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $imageHelperFactory = $objectManager->create("\Magento\Catalog\Helper\ImageFactory");
        $imageUrl = $imageHelperFactory->create()->init($row, "product_thumbnail_image")->getUrl();
        $html  = '<img src="'.$imageUrl.'"/>';
        return $html;
    }
}
