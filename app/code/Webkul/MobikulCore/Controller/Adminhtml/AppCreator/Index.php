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

namespace Webkul\MobikulCore\Controller\Adminhtml\AppCreator;

/**
 * Class Index for AppCreator
 */
class Index extends \Webkul\MobikulCore\Controller\Adminhtml\AppCreator
{

    /**
     * Execute Fucntion
     *
     * @return jSon
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu("Webkul_MobikulCore::appcreator");
        $resultPage->getConfig()->getTitle()->prepend(__("Manage AppCreator"));
        return $resultPage;
    }

    /**
     * Fucntion to check if this controller can be accessed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed("Webkul_MobikulCore::appcreator");
    }
}
