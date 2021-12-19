<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_MobikulMp
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\MobikulMp\Plugin\Controller\Catalog;
use \Magento\Framework\Controller\ResultFactory;

/**
 * HomePageData class plugin for seller data
 */
class HomePageData
{
    /**
     * $helper
     */
    protected $helper;
    
    /**
     * $seller
     */
    protected $seller;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    protected $carouselFactory;

    protected $mpHelper;

    /**
     * Constructor function for dependency management
     *
     * @param \Webkul\MobikulCore\Helper\Data $helper
     * @param \Webkul\Marketplace\Model\Seller $seller
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        \Webkul\MobikulCore\Helper\Data $helper,
        \Webkul\Marketplace\Model\Seller $seller,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Webkul\MobikulCore\Model\CarouselFactory $carouselFactory,
        \Webkul\Marketplace\Helper\Data $mpHelper,
        ResultFactory $resultFactory
    ){
        $this->helper        = $helper;
        $this->seller        = $seller;
        $this->customerFactory = $customerFactory;
        $this->carouselFactory = $carouselFactory;
        $this->mpHelper = $mpHelper;
        $this->resultFactory = $resultFactory;
    }

    /**
     * AfterExecute function
     *
     * @param \Webkul\MobikulApi\Controller\Catalog\HomePageData $subject
     * @param [type] $result
     * @return void
     */
    public function afterExecute(
        \Webkul\MobikulApi\Controller\Catalog\HomePageData $subject,
        $result
    ) {
        if ($result && $result->getRawData()) {

            $request = $subject->getRequest()->getParams();
            $response = json_decode($result->getRawData());
            if ($response->success == true) {
                $customerToken = $request["customerToken"] ?? '';
                $storeId = $request["storeId"] ?? 1;
                $collection = $this->carouselFactory->create()->getCollection()
                ->addFieldToFilter("status", 1)
                ->addFieldToFilter([
                    'store_id',
                    'store_id'
                ],[
                    ["finset" => 0],
                    ["finset" => $storeId]
                ]
                )
                ->setOrder("sort_order", "ASC");
                if ($this->helper->getConfigData("mobikul/configuration/carousel_seller")) {
                    foreach ($collection as $eachCarousel) {
                        $sellers = [];
                        if ($eachCarousel->getType() == 3) {
                            $sellerList = [];
                            $oneCarousel = [];
                            $oneCarousel["id"] = $eachCarousel->getId();
                            $oneCarousel["type"] = "seller";
                            $oneCarousel["label"] = $eachCarousel->getTitle();
                            $oneCarousel["seller_carousel_limit"] = $this->helper->getConfigData("mobikul/configuration/seller_carousel_configuration");
                            if ($eachCarousel->getColorCode()) {
                                $oneCarousel["color"] = $eachCarousel->getColorCode();
                            }
                            if ($eachCarousel->getFilename()) {
                                $filePath = $this->helper->getUrl("media")."mobikul/carousel/".$eachCarousel->getFilename();
                                $dominantColorPath = $this->helper->getBaseMediaDirPath()."mobikul/carousel/".$eachCarousel
                                    ->getFilename();
                                $oneCarousel["image"] = $filePath;
                                $oneCarousel["dominantColor"] = $this->helper->getDominantColor($dominantColorPath);
                            }
                            $selectedSellerIds = explode(",", $eachCarousel->getSellerIds());
                            $sellerCollection = $this->seller->getCollection()->addFieldToFilter("entity_id", ["in"=>$selectedSellerIds]);
                            $joinConditions = 'main_table.seller_id = customer_grid_flat.entity_id';
                            $sellerCollection->getSelect()->join(
                                ['customer_grid_flat'],
                                $joinConditions,
                                [
                                    'seller_name' => 'name',
                                    'seller_email' => 'email'
                                ]
                            );
                            $sellersList = [];
                            foreach ($sellerCollection as $eachSeller) {
                                $oneSeller = [];
                                $oneSeller['seller_id'] = $eachSeller['seller_id'];
                                $oneSeller['name'] = $eachSeller['seller_name'];
                                $oneSeller['email'] = $eachSeller['seller_email'];
                                $shopUrl = $this->mpHelper->getRewriteUrl(
                                    'marketplace/seller/profile/shop/'.$eachSeller['shop_url']
                                );
                                $oneSeller['shop_url'] = $shopUrl;
                                $sellerLogoPic = $this->getSellerLogo($eachSeller['logo_pic']);
                                $oneSeller['logo'] = $sellerLogoPic;
                                $sellerCollectionUrl = $this->mpHelper->getRewriteUrl(
                                    'marketplace/seller/collection/shop/'.$eachSeller['shop_url']
                                );
                                $oneSeller['collection_url'] = $sellerCollectionUrl;
                                $oneSeller['shop_title'] = $eachSeller['shop_title'];
                                $sellersList[] = $oneSeller;
                            }
                            $oneCarousel["sellers"] = $sellersList;
                            if (count($oneCarousel["sellers"])) {
                                $response->carousel[] = $oneCarousel;
                            }
                        }
                    }
                }
                $customerId  = $this->helper->getCustomerByToken($customerToken) ?? 0;
                $customer = $this->customerFactory->create()->load($customerId);
        
                if ($customer->getEmail() == $this->helper->getConfigData("mobikulmp/admin/email")) {
                    $response->isAdmin = true;
                } else {
                    $response->isAdmin = false;
                }
                $collection = $this->seller->getCollection()
                    ->addFieldToFilter("seller_id", $customerId)
                    ->addFieldToFilter("store_id", $storeId);
                // If seller data doesn't exist for current store //////////////////////////////
                if (!$collection->getSize()) {
                    $collection = $this->seller->getCollection()
                        ->addFieldToFilter("seller_id", $customerId)
                        ->addFieldToFilter("store_id", 0);
                }
                foreach ($collection as $record) {
                    $response->isSeller = true;
                    if ($record->getIsSeller() == 0) {
                        $response->isPending = true;
                    } else {
                        $response->isPending = false;
                    }
        
                }
                $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $resultJson->setData($response);
                return $resultJson;
            } else {
                $resultJson  = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $resultJson->setData($response);
                return $resultJson;
            }
        }
        return $result;
    }

    public function getSellerLogo($logoPic)
    {
        if ($logoPic) {
            return $this->mpHelper
            ->getMediaUrl().'avatar/'.$logoPic;
        } else {
            return $this->mpHelper
            ->getMediaUrl().'avatar/noimage.png';
        }
    }
}
