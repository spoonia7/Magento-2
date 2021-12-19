<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_MobikulApi
 * @author    Webkul <support@webkul.com>
 * @copyright 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\MobikulApi\Controller\Index;

/**
 * Upload ProfilePic Class
 */
class UploadProfilePic extends \Webkul\MobikulApi\Controller\ApiController
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Webkul\MobikulCore\Helper\Upload
     */
    protected $uploadHelper;

    /**
     * Construct Function for Class UploadProfilePic
     *
     * @param \Webkul\MobikulCore\Helper\Data       $helper        helper
     * @param \Magento\Store\Model\App\Emulation    $emulate       emulate
     * @param \Webkul\MobikulCore\Helper\Upload     $uploadHelper  uploadHelper
     * @param \Webkul\MobikulCore\Helper\Catalog    $helperCatalog helperCatalog
     * @param \Magento\Framework\App\Action\Context $context       context
     * @param \Magento\Framework\Json\Helper\Data   $jsonHelper    jsonHelper
     *
     * @return void
     */
    public function __construct(
        \Webkul\MobikulCore\Helper\Data $helper,
        \Magento\Store\Model\App\Emulation $emulate,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\MobikulCore\Helper\Upload $uploadHelper,
        \Webkul\MobikulCore\Helper\Catalog $helperCatalog
    ) {
        $this->helper = $helper;
        $this->jsonHelper = $jsonHelper;
        $this->uploadHelper = $uploadHelper;
        parent::__construct($helper, $context, $jsonHelper);
    }

    /**
     * Execute Function for UploadProfilePic Class
     *
     * @return void|array
     */
    public function execute()
    {
        $this->files = (array) $this->getRequest()->getFiles();
        if (isset($this->files)) {
            $this->wholeData = $this->getRequest()->getParams();
            $this->width = $this->wholeData["width"] ?? 1000;
            $this->mFactor = $this->wholeData["mFactor"] ?? 1;
            $this->mFactor = $this->helper->calcMFactor($this->mFactor);
            $this->customerToken = $this->wholeData["customerToken"] ?? "";
            $this->customerId = $this->helper->getCustomerByToken($this->customerToken) ?? 0;
            // Checking customer token //////////////////////////////////////////////
            if (!$this->customerId && $this->customerToken != "") {
                $this->returnArray["message"] = __("As customer you are requesting does not exist, so you need to logout.");
                $this->returnArray["otherError"] = "customerNotExist";
                $this->customerId = 0;
            }
            // End checking customer token //////////////////////////////////////////
            try {
                $this->uploadHelper->uploadPicture($this->files, $this->customerId, $this->customerId."-profile", "profile");
                $this->returnArray = $this->uploadHelper->resizeAndCache($this->width, $this->customerId, $this->mFactor, "profile");
            } catch (\Exception $e) {
                $this->returnArray["message"] = $e->getMessage();
                return $this->getJsonResponse($this->returnArray);
            }
            $this->returnArray["success"] = true;
            return $this->getJsonResponse($this->returnArray);
        } else {
            $this->returnArray["message"] = __("Invalid Image.");
            return $this->getJsonResponse($this->returnArray);
        }
    }
}
