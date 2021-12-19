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
namespace Webkul\MobikulMp\Observer;

class ProductApprovalNotification extends AbstractObserver
{
    /**
     * Execute function for Observer OrderStatusNotification
     *
     * @param \Magento\Framework\Event\Observer $observer contatins all the dispatched data
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $mpProduct = $observer->getProduct();
        $product   = $this->magentoProduct->create()->load($mpProduct->getMageproductId());
        $message   = [
            "id"            => $product->getId(),
            "body"          => __("Congratulations, your product has been approved now."),
            "sound"         => "default",
            "title"         => __("Product approved."),
            "message"       => __("Congratulations, your product has been approved now."),
            "productId"     => $product->getId(),
            "banner_url"    => $this->imageHelperFactory->create()->init($product, "product_thumbnail_image")->getUrl(),
            "productName"      => $product->getName(),
            "notificationType" => "productApproval"
        ];
        $url     = "https://fcm.googleapis.com/fcm/send";
        $authKey = $this->helper->getConfigData("mobikul/notification/apikey");
        $headers = [
            "Authorization: key=".$authKey,
            "Content-Type: application/json",
        ];
        if ($authKey != "") {
            $tokenCollection = $this->deviceToken->getCollection()->addFieldToFilter(
                "customer_id",
                $mpProduct->getSellerId()
            );
            foreach ($tokenCollection as $eachToken) {
                $fields = [
                    "to"                => $eachToken->getToken(),
                    "data"              => $message,
                    "priority"          => "high",
                    "notification"      => $message,
                    "time_to_live"      => 30,
                    "delay_while_idle"  => true,
                    "content_available" => true
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->jsonHelper->jsonEncode($fields));
                $result = curl_exec($ch);
                curl_close($ch);
                if ($this->isJson($result)) {
                    $result = $this->jsonHelper->jsonDecode($result);
                    if ($result["success"] == 0 && $result["failure"] == 1) {
                        $eachToken->delete();
                    }
                }
            }
        }
    }
}
