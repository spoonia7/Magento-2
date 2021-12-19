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

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class DownloadAllShipping
 *
 * @category Webkul
 * @package  Webkul_MobikulMp
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class DownloadAllShipping extends AbstractMarketplace
{
    /**
     * Execute function for class DownloadAllShipping
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
            $to          = date_format(date_create($this->dateTo), "Y-m-d H:i:s");
            $from        = date_format(date_create($this->dateFrom), "Y-m-d H:i:s");
            $customer    = $this->customer->load($this->customerId);
            $shipmentIds = [];
            $this->customerSession->setCustomer($customer);
            $this->customerSession->setCustomerId($customerId);
            try {
                $collection = $this->marketplaceSaleList
                    ->getCollection()
                    ->addFieldToFilter("seller_id", $this->customerId)
                    ->addFieldToFilter("created_at", ["datetime"=>true, "from"=>$from, "to"=>$to])
                    ->addFieldToSelect("order_id")
                    ->distinct(true);
                $shippingColl = $this->marketplaceOrders
                    ->getCollection()
                    ->addFieldToFilter("order_id", $collection->getData())
                    ->addFieldToFilter("seller_id", $this->customerId);
                $shipmentIds = $shippingColl->getData();
                if (!empty($shipmentIds)) {
                    $shipments = $this->shipmentCollection
                        ->addAttributeToSelect("*")
                        ->addAttributeToFilter("entity_id", ["in"=>$shipmentIds])
                        ->load();
                    if (!$shipments->getSize()) {
                        $this->returnArray["message"] = __(
                            "There are no printable documents related to selected date range."
                        );
                        $this->emulate->stopEnvironmentEmulation($environment);
                        $this->helper->log($this->returnArray, "logResponse", $this->wholeData);
                        return $this->getJsonResponse($this->returnArray);
                    }
                    $pdf = $this->shipmentPdf->getPdf($shipments);
                    $date = $this->dateTime->date("Y-m-d_H-i-s");
                    return $this->fileFactory->create(
                        "packingslip".$date.".pdf",
                        $pdf->render(),
                        DirectoryList::VAR_DIR,
                        "application/pdf"
                    );
                } else {
                    $this->returnArray["message"] = __(
                        "There are no printable documents related to selected date range."
                    );
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->returnArray["message"] = $e->getMessage();
            } catch (\Exception $e) {
                $this->returnArray["message"] = __("We can't print the shipment right now.");
            }
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
            $this->dateTo        = $this->wholeData["dateTo"]        ?? "";
            $this->dateFrom      = $this->wholeData["dateFrom"]      ?? "";
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
