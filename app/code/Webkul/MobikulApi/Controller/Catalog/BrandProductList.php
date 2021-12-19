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

namespace Webkul\MobikulApi\Controller\Catalog;

/**
 * Class BrandProductList
 */
class BrandProductList extends AbstractCatalog
{
    /**
     * Execute function for class BrandProductList
     *
     * @return json
     */
    public function execute()
    {
        try {
            $this->verifyRequest();
            $cacheString = "BRANDPRODUCTLIST".$this->width.$this->storeId.$this->brandId.$this->mFactor.$this->pageNumber.
            $this->customerToken.$this->currency;
            if ($this->helper->validateRequestForCache($cacheString, $this->eTag)) {
                return $this->getJsonResponse($this->returnArray, 304);
            }
            $environment = $this->emulate->startEnvironmentEmulation($this->storeId);
            $this->sortData = $this->jsonHelper->jsonDecode($this->sortData);
            $this->filterData = $this->jsonHelper->jsonDecode($this->filterData);
            // Setting currency /////////////////////////////////////////////////////////////////////////////
            $this->store->setCurrentCurrencyCode($this->currency);
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $brand = $objectManager->create(\Mageplaza\Shopbybrand\Model\Brand::class);
            $brandCollection = $brand->getCollection();
            $brands = $brandCollection->addFIeldToFilter('brand_id', ['in' => $this->brandId]);
            $optionId = "";
            foreach ($brands as $brand) {
                $optionId = $brand['option_id'];
            }
            $productCollection = $this->productCollection->create();
            $productCollection = $productCollection->addAttributeToSelect('*');
            if ($this->brandId) {
                $productCollection = $productCollection
                ->addAttributeToFilter("manufacturer", ["eq" => $optionId])
                ->addAttributeToSelect("as_featured")
                ->addAttributeToSelect("visibility")
                ->addStoreFilter()
                ->addAttributeToFilter("status", ["in"=>$this->productStatus->getVisibleStatusIds()])
                ->setVisibility($this->productVisibility->getVisibleInSiteIds());
                $productList = [];
                if ($productCollection->getSize()) {
                    if ($this->pageNumber >= 1) {
                        if ($this->productCollection) {
                            $this->returnArray["totalCount"] = $productCollection->getSize();
                        } else {
                            $this->returnArray["totalCount"] = 0;
                        }
                        $pageSize = $this->helperCatalog->getPageSize();
                        if ($productCollection) {
                            $productCollection->setPageSize($pageSize)->setCurPage($this->pageNumber);
                        }
                    }
                    $i = 0;
                    foreach ($productCollection as $eachProduct) {
                        $productList[] = $this->helperCatalog->getOneProductRelevantData($eachProduct, $this->storeId, $this->width, $this->customerId);
                        // $productList[$i]['thumbNail'] = "https://aswaqi.com/pub/media/catalog/product/cache/2f978e79010b2f85244f297d35e48d9c/".$eachProduct->getSmallImage();
                        $i++;
                    }
                    $this->returnArray["productList"] = $productList;
                } else {
                    $this->returnArray["message"] = __("Sorry, Products not found.");
                }
            }
            $this->returnArray["success"] = true;
            $this->customerSession->setCustomerId(null);
            $this->emulate->stopEnvironmentEmulation($environment);
            $this->checkNGenerateEtag($cacheString);
            return $this->getJsonResponse($this->returnArray);
        } catch (\Exception $e) {
            $this->returnArray["message"] = __($e->getMessage());
            $this->helper->printLog($this->returnArray);
            return $this->getJsonResponse($this->returnArray);
        }
    }

    /**
     * Function to verify request
     *
     * @return json|void
     */
    public function verifyRequest()
    {
        if ($this->getRequest()->getMethod() == "GET" && $this->wholeData) {
            $this->brandId = $this->wholeData["brandId"] ?? "";
            $this->eTag = $this->wholeData["eTag"] ?? "";
            $this->width = $this->wholeData["width"] ?? 1000;
            $this->storeId = $this->wholeData["storeId"] ?? 0;
            $this->mFactor = $this->wholeData["mFactor"] ?? 1;
            $this->mFactor = $this->helper->calcMFactor($this->mFactor);
            $this->pageNumber = $this->wholeData["pageNumber"] ?? 1;
            $this->sortData = $this->wholeData["sortData"] ?? "[]";
            $this->filterData = $this->wholeData["filterData"] ?? "[]";
            $this->customerToken = $this->wholeData["customerToken"] ?? "";
            $this->currency = $this->wholeData["currency"] ?? $this->store->getBaseCurrencyCode();
            $this->customerId = $this->helper->getCustomerByToken($this->customerToken) ?? 0;
            // Checking customer token //////////////////////////////////////////////
            if (!$this->customerId && $this->customerToken != "") {
                $this->returnArray["message"] = __("Customer you are requesting does not exist, so you need to logout.");
                $this->returnArray["otherError"] = "customerNotExist";
                $this->customerId = 0;
            } elseif ($this->customerId != 0) {
                $this->customerSession->setCustomerId($this->customerId);
            }
        } else {
            throw new \Exception(__("Invalid Request"));
        }
    }
}
