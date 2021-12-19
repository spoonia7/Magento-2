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

namespace Webkul\MobikulApi\Controller\Customer;

/**
 * DownloadProduct class for Customer Controller
 */
class DownloadProduct extends AbstractCustomer
{
    /**
     * Execure Function for DownlodableProduct Class
     */
    public function execute()
    {
        try {
            $this->verifyRequest();
            $linkPurchasedItem = $this->purchasedLinkItem->load($this->hash, "link_hash");
            if (!$linkPurchasedItem->getId()) {
                $this->returnArray["message"] = __("Requested link does not exist.");
                return $this->getJsonResponse($this->returnArray);
            }
            $downloadableHelper = $this->downloadableHelper;
            if (!$downloadableHelper->getIsShareable($linkPurchasedItem)) {
                $linkPurchased = $this->purchasedlink->load($linkPurchasedItem->getPurchasedId());
                if ($linkPurchased->getCustomerId() != $this->customerId) {
                    $this->returnArray["message"] = __("Requested link does not exist.");
                    return $this->getJsonResponse($this->returnArray);
                }
            }
            $status = $linkPurchasedItem->getStatus();
            $downloadsLeft = $linkPurchasedItem->getNumberOfDownloadsBought() - $linkPurchasedItem->getNumberOfDownloadsUsed();
            $downloadableHelperFile = $this->downloadableFileHelper;
            if ($status == \Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_AVAILABLE && ($downloadsLeft || $linkPurchasedItem->getNumberOfDownloadsBought() == 0)) {
                $resource = "";
                $resourceType = "";
                if ($linkPurchasedItem->getLinkType() == \Magento\Downloadable\Helper\Download::LINK_TYPE_URL) {
                    $this->returnArray["url"] = $linkPurchasedItem->getLinkUrl();
                    $buffer = file_get_contents($this->returnArray["url"]);
                    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
                    $this->returnArray["mimeType"] = $fileInfo->buffer($buffer);
                    $fileArray = explode(DS, $this->returnArray["url"]);
                    $this->returnArray["fileName"] = end($fileArray);
                } elseif ($linkPurchasedItem->getLinkType() == \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE) {
                    $linkFile = $downloadableHelperFile->getFilePath($this->downloadableLink->getBasePath(), $linkPurchasedItem->getLinkFile());
                    $linkFile = $this->helperCatalog->getBasePath().DS.$linkFile;
                    if (file_exists($linkFile)) {
                        $this->returnArray["mimeType"] = mime_content_type($linkFile);
                        $this->returnArray["url"] = $this->storeManager->getStore()->getUrl("mobikulhttp/download/index", ["hash"=>$this->hash]);
                        $linkFileArr = explode(DS, $linkFile);
                        $this->returnArray["fileName"] = end($linkFileArr);
                    } else {
                        throw new \Exception(__("An error occurred while getting the requested content. Please contact the store owner."));
                    }
                }
            } elseif ($status == \Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_EXPIRED) {
                throw new \Exception(__("The link has expired."));
            } elseif ($status == \Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_PENDING || $status == \Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_PAYMENT_REVIEW) {
                throw new \Exception(__("The link is not available."));
            } else {
                throw new \Exception(__("An error occurred while getting the requested content. Please contact the store owner."));
            }
            $this->returnArray["success"] = true;
            return $this->getJsonResponse($this->returnArray);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->returnArray["message"] = __($e->getMessage());
            $this->helper->printLog($this->returnArray);
            return $this->getJsonResponse($this->returnArray);
        } catch (\Exception $e) {
            $this->returnArray["message"] = __($e->getMessage());
            $this->helper->printLog($this->returnArray);
            return $this->getJsonResponse($this->returnArray);
        }
    }

    /**
     * Verify Request function to verify Customer and Request
     *
     * @throws Exception customerNotExist
     * @return json | void
     */
    protected function verifyRequest()
    {
        if ($this->getRequest()->getMethod() == "GET" && $this->wholeData) {
            $this->hash = $this->wholeData["hash"] ?? "";
            $this->customerToken = $this->wholeData["customerToken"] ?? "";
            $this->customerId = $this->helper->getCustomerByToken($this->customerToken);
            if (!$this->customerId && $this->customerToken != "") {
                $this->returnArray["otherError"] = "customerNotExist";
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("As customer you are requesting does not exist, so you need to logout.")
                );
            }
        } else {
            throw new \Exception(__("Invalid Request"));
        }
    }
}
