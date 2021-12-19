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

namespace Webkul\MobikulCore\Controller\Adminhtml\Notification;

/**
 * GetProductGridHtml Class
 */
class GetProductGridHtml extends \Webkul\MobikulCore\Controller\Adminhtml\Notification
{
    public function execute()
    {
        $block = $this->_view->getLayout()->createBlock(
            \Webkul\MobikulCore\Block\Adminhtml\Notification\Edit\Tab\productGrid::class
        );
        $this->getResponse()->setBody($block->toHtml());
    }
}
