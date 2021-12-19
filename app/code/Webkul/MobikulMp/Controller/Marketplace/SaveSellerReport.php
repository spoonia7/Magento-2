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
namespace Webkul\MobikulMp\Controller\Marketplace;

/**
 * Class SaveReview
 *
 * @category Webkul
 * @package  Webkul_MobikulMp
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class SaveSellerReport extends AbstractMarketplace
{
    /**
     * Execute function for class SaveReview
     *
     * @throws LocalizedException
     *
     * @return json | void
     */
    public function execute()
    {
        try {
            $this->verifyRequest();
            $environment = $this->emulate->startEnvironmentEmulation($this->storeId);
            $sellerCollection = $this->marketplaceHelper->getSellerCollection()
                ->addFieldToFilter("seller_id", $this->sellerId)
                ->setPagesize(1)
                ->getFirstItem();
            if(!$sellerCollection->getSellerId()){
                $this->returnArray["success"] = false;
                $this->returnArray["message"] = __("The seller is not available.");
                return $this->getJsonResponse($this->returnArray);
            }
            $data                       = [];
            $data["seller_id"]           = $this->sellerId;
            $data["name"]           = $this->customerName;
            $data["email"]          = $this->customerEmail;
            $data["created_at"]         = $this->date->gmtDate();
            if($this->isOtherReason){
                $data["reason"] = $this->otherReason;
            }else{
                $reason = $this->sellerFlagReason->getCollection()->addFieldToFilter('entity_id',$this->reasonId);
                $data['reason'] = $reason->getFirstItem()->getReason();
            }
            $this->sellerFlags->setData($data)->save();
            $this->returnArray["message"] = __("Your Report successfully saved.");
            $this->returnArray["success"] = true;
            $this->emulate->stopEnvironmentEmulation($environment);
            $this->helper->log($this->returnArray, "logResponse", $this->wholeData);
            return $this->getJsonResponse($this->returnArray);
        } catch (\Exception $e) {
            $this->returnArray["message"] = __($e->getMessage());
            $this->helper->printLog($this->returnArray, 1);
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
        if ($this->getRequest()->getMethod() == "POST" && $this->wholeData) {
            $this->storeId       = $this->wholeData["storeId"]       ?? 0;
            $this->sellerId      = $this->wholeData["sellerId"]      ?? 0;
            $this->otherReason   = $this->wholeData["otherReason"]   ?? "";
            $this->isOtherReason   = $this->wholeData["isOtherReason"]   ?? 0;
            $this->reasonId   = $this->wholeData["reasonId"]   ?? 0;
            $this->customerEmail = $this->wholeData["customerEmail"] ?? "";
            $this->customerName = $this->wholeData["customerName"] ?? "";
            $this->customerToken = $this->wholeData["customerToken"] ?? '';
            $this->customerId    = $this->helper->getCustomerByToken($this->customerToken) ?? 0;
            if (!$this->customerId && $this->customerToken != "") {
                $this->returnArray["otherError"] = "customerNotExist";
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("Customer you are requesting does not exist.")
                );
            } elseif ($this->customerId != 0) {
                $this->customerSession->setCustomerId($this->customerId);
            }
        } else {
            throw new \Exception(__("Invalid Request"));
        }
    }
}
