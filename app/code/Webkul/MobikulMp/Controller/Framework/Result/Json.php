<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_MobikulMp
 * @author    Webkul <support@webkul.com>
 * @copyright 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */
namespace Webkul\MobikulMp\Controller\Framework\Result;

class Json extends \Magento\Framework\Controller\Result\Json
{
    /**
     * Function to getRawData
     * overwritten function of Magento\Framework\Controller\Result\Json
     *
     * @return string jSon
     */
    public function getRawData()
    {
        return $this->json;
    }
}
