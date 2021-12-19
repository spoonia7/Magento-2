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
 * Class DownloadTransactionList
 *
 * @category Webkul
 * @package  Webkul_MobikulMp
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class DownloadTransactionList extends AbstractMarketplace
{
    /**
     * Execute function for class DownloadTransactionList
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
            $transactionCollection = $this->transactionCollectionFactory->create()
                ->addFieldToSelect("*")
                ->addFieldToFilter("seller_id", $this->customerId);
            $to   = null;
            $from = null;
            if ($this->dateTo) {
                $todate = date_create($this->dateTo);
                $to = date_format($todate, "Y-m-d 23:59:59");
            }
            if ($this->dateFrom) {
                $fromdate = date_create($this->dateFrom);
                $from = date_format($fromdate, "Y-m-d H:i:s");
            }
            if ($this->transactionId) {
                $transactionCollection->addFieldToFilter("transaction_id", ["like"=>"%".$this->transactionId."%"]);
            }
            $transactionCollection->addFieldToFilter("created_at", ["datetime"=>true, "from"=>$from, "to"=>$to]);
            $transactionCollection->setOrder("created_at", "desc");
            $transactionList = [];
            foreach ($transactionCollection as $transaction) {
                $eachTransaction                        = [];
                $eachTransaction["Date"]                = $this->viewTemplate->formatDate($transaction->getCreatedAt());
                $eachTransaction["Transaction Id"]      = $transaction->getTransactionId();
                $eachTransaction["Comment Message"]     = __("None");
                if ($transaction->getCustomNote()) {
                    $eachTransaction["Comment Message"] = $transaction->getCustomNote();
                }
                $eachTransaction["Transaction Amount"]  = $this->helperCatalog->stripTags(
                    $this->checkoutHelper->formatPrice($transaction->getTransactionAmount())
                );
                $transactionList[] = $eachTransaction;
            }
            if (isset($transactionList[0])) {
                header("Content-Type: text/csv");
                header("Content-Disposition: attachment; filename=transactionlist.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                $outstream = fopen("php://output", "w");
                fputcsv($outstream, array_keys($transactionList[0]));
                foreach ($transactionList as $result) {
                    fputcsv($outstream, $result);
                }
                fclose($outstream);
                $this->emulate->stopEnvironmentEmulation($environment);
                return;
            }
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
        if ($this->getRequest()->getMethod() == "GET" && $this->wholeData) {
            $this->storeId       = $this->wholeData["storeId"]       ?? 0;
            $this->dateTo        = $this->wholeData["dateTo"]        ?? "";
            $this->dateFrom      = $this->wholeData["dateFrom"]      ?? "";
            $this->pageNumber    = $this->wholeData["pageNumber"]    ?? 1;
            $this->transactionId = $this->wholeData["transactionId"] ?? "";
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
