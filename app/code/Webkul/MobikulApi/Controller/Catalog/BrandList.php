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
 * BrandList Class
 */
class BrandList extends AbstractCatalog
{
    /**
     * Execute function for BrandList Class
     *
     * @return array
     */
    public function execute()
    {
        try {
            $this->verifyRequest();
            if (!$this->customerId && $this->customerToken != ""){
                return $this->getJsonResponse($this->returnArray);
            }
            $cacheString = "BRANDLISTDATA".$this->brandGroupName.$this->width.$this->storeId.$this->mFactor.$this->websiteId.$this->customerToken;
            if ($this->helper->validateRequestForCache($cacheString, $this->eTag)) {
                return $this->getJsonResponse($this->returnArray, 304);
            }
            $environment = $this->emulate->startEnvironmentEmulation($this->storeId);
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            if ($this->helper->getConfigData("mobikul/configuration/carousel_brand")) {
                $category = $objectManager->create(\Mageplaza\Shopbybrand\Model\Category::class);
                $categoryCollection = $category->getCollection()
                    ->addFieldToFilter('name', ['eq' => $this->brandGroupName])
                    ->addFieldToFilter('store_ids', ['eq' => $this->storeId])
                    ->addFieldToFilter('status', 1);
                $catId = [];
                $categoryBrands = [];
                if ($categoryCollection->getSize()) {
                    foreach ($categoryCollection as $categoryModel) {
                        $sql = 'main_table.cat_id IN (' . $categoryModel['cat_id'] . ')';
                        $categoryBrands[] = $category->getCategoryCollection($sql, null)->getData();
                        $catId[] = $categoryModel['cat_id'];
                    }
                    $eachBrand = [];
                    if ($categoryBrands) {
                        foreach ($categoryBrands as $values) {
                            $brands = [];
                            $optionIds = [];
                            foreach ($values as $value => $item) {
                                if (in_array($item['cat_id'], $catId)) {
                                    $optionIds[] = $item['option_id'];
                                }
                            }
                            $optionIds = array_unique($optionIds);
                            $brands = $this->getBrand($optionIds);
                            $brandData['brand_group_name'] = $values[0]['name'];
                            $brandData['brand_list'] = $brands;
                            $eachBrand[] = $brandData;
                        }
                        $groupBrand = $eachBrand;
                        $this->returnArray['brandCarousel'] = $groupBrand;
                    } else {
                        $this->returnArray["message"] = __("Sorry, Brands not found for current store id: ". $this->storeId);
                    }
                } else {
                    $this->returnArray["message"] = __("Sorry, Brands not found of ". $this->brandGroupName);
                }
                $this->returnArray["success"] = true;
                $this->customerSession->setCustomerId(null);
                $this->emulate->stopEnvironmentEmulation($environment);
                $this->checkNGenerateEtag($cacheString);
                return $this->getJsonResponse($this->returnArray);
            }
        } catch (\Exception $e) {
            $this->returnArray["message"] = __($e->getMessage());
            $this->helper->printLog($this->returnArray);
            return $this->getJsonResponse($this->returnArray);
        }
    }

    /**
     * Function verify Request to authenticate the request
     * Authenticates the request and logs the result for invalid requests
     *
     * @return Json
     */
    public function verifyRequest()
    {
        if ($this->getRequest()->getMethod() == "GET" && $this->wholeData) {
            $this->eTag = $this->wholeData["eTag"] ?? "";
            $this->storeId = $this->wholeData["storeId"] ?? 0;
            $this->websiteId = $this->wholeData["websiteId"] ?? 1;
            $this->width = $this->wholeData["width"] ?? 1000;
            $this->mFactor = $this->wholeData["mFactor"] ?? 1;
            $this->brandGroupName = $this->wholeData["brandGroupName"] ?? "";
            $this->customerToken = $this->wholeData["customerToken"] ?? "";
            $this->customerId = $this->helper->getCustomerByToken($this->customerToken) ?? 0;
            if ($this->websiteId == 0 && $this->storeId == 0) {
                throw new \Exception(__("Invalid Website Id"));
            }
            if (!$this->customerId && $this->customerToken != "") {
                $this->returnArray["message"] = __("Customer you are requesting does not exist, so you need to logout.");
                $this->returnArray["success"] = false;
                $this->returnArray["otherError"] = "customerNotExist";
                $this->customerId = 0;
            } elseif ($this->customerId != 0) {
                $this->customer = $this->customerFactory->create()->load($this->customerId);
                $this->customerSession->setCustomerId($this->customerId);
            }
        } else {
            throw new \Exception(__("Invalid Request"));
        }
    }

    /**
     * Get Brands Data
     * 
     * @param array
     * @return array
     */
    public function getBrand($optionIds)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $brandHelper = $objectManager->create(\Mageplaza\Shopbybrand\Helper\Data::class);
        $brand = $objectManager->create(\Mageplaza\Shopbybrand\Model\Brand::class);
        $brandCollection = $brand->getCollection();
        $brands = $brandCollection->addFIeldToFilter('option_id', ['in' => $optionIds]);
        $brandList = [];
        foreach ($brands as $brand) {
            $eachBrand = [];
            $eachBrand['brand_id'] = $brand['brand_id'];
            $eachBrand['page_title'] = $brand['page_title'];
            $eachBrand['brand_url']  = $brandHelper->getBrandUrl($brand);
            $eachBrand['brand_image'] = $brandHelper->getBrandImageUrl($brand);
            $brandList[] = $eachBrand;
        }
        return $brandList;
    }
}