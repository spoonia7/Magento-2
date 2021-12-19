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
 * Class ViewCreditMemo
 *
 * @category Webkul
 * @package  Webkul_MobikulMp
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class ViewCreditMemo extends AbstractMarketplace
{
    /**
     * Execute function for class ViewCreditMemo
     *
     * @throws LocalizedException
     *
     * @return json | void
     */
    public function execute()
    {
        try {
            $this->verifyRequest();
            $cacheString = "VIEWCREDITMEMO".$this->storeId.$this->incrementId.$this->creditmemoId;
            $cacheString .= $this->customerToken.$this->customerId;
            if ($this->helper->validateRequestForCache($cacheString, $this->eTag)) {
                return $this->getJsonResponse($this->returnArray, 304);
            }
            $environment = $this->emulate->startEnvironmentEmulation($this->storeId);
            $this->customerSession->setCustomerId($this->customerId);
            $order = $this->order->loadByIncrementId($this->incrementId);
            $orderId = $order->getId();
            $creditmemo = $this->creditmemoRepository->get($this->creditmemoId);
            $creditmemoStatus = '';
            if ($creditmemo->getState()==1) {
                $creditmemoStatus = __('Pending');
            } elseif ($creditmemo->getState()==2) {
                $creditmemoStatus = __('Refunded');
            } elseif ($creditmemo->getState()==3) {
                $creditmemoStatus = __('Canceled');
            }
            $paymentCode = '';
            $paymentMethod = '';
            if ($order->getPayment()) {
                $paymentCode = $order->getPayment()->getMethod();
                $paymentMethod = $order->getPayment()->getMethodInstance()->getTitle();
            }
            $this->returnArray['mainHeading'] = __('View Memo');
            $this->returnArray['subHeading'] = __('Creditmemo #%1', $creditmemo->getIncrementId());
            $this->returnArray['creditmemoStatus'] = $creditmemoStatus;
            $this->returnArray['creditmemoDate'] = $this->viewTemplate->formatDate(
                $creditmemo->getCreatedAt(),
                \IntlDateFormatter::MEDIUM,
                true,
                $this->viewTemplate->getTimezoneForStore(
                    $order->getStore()
                )
            );
            $this->returnArray['buttons'] = [
                [
                    'label'   => "Send Email",
                    'title'   => __("Send Email To Customer"),
                    'confirm' => __("Are you sure you want to send credit memo email to customer?")
                ],
                [
                    'label' => __("Print"),
                    'title' => __("Print")
                ]
            ];
            $this->returnArray['orderInfoHeading'] = __("Order Information");
            $this->returnArray['orderHeading'] = __('Order # %1', $order->getIncrementId());
            $this->returnArray['orderId'] = $order->getId();
            $this->returnArray['orderStatusHeading'] = __("Order Status");
            $this->returnArray['orderStatus'] = $order->getStatus();
            $this->returnArray['orderDateHeading'] =  __("Order Date");
            $this->returnArray['orderDate'] =  $this->viewTemplate->formatDate(
                $order->getCreatedAt(),
                \IntlDateFormatter::MEDIUM,
                true,
                $this->viewTemplate->getTimezoneForStore(
                    $order->getStore()
                )
            );
            if ($this->marketplaceHelper->getSellerProfileDisplayFlag()) {
                $this->returnArray['buyerInfoHeading'] = __("Buyer Information");
                $this->returnArray['customerNameHeading'] = __("Customer Name");
                $this->returnArray['customerName'] = $order->getCustomerName();
                $this->returnArray['customerEmailHeading'] = __("Email");
                $this->returnArray['customerEmail'] = $order->getCustomerEmail();
                $this->returnArray['AddressInfoHeading'] = __("Address Information");
                $this->returnArray['billingAddressHeading'] = __("Billing Address");
                $this->returnArray["billingAddress"] = $this->orderViewBlock->getFormattedAddress(
                    $order->getBillingAddress()
                );
                if ($this->orderViewBlock->isOrderCanShip($order)) {
                    $this->returnArray['shippingAddressHeading'] = __("Shipping Address");
                    $this->returnArray["shippingAddress"] = $this->orderViewBlock->getFormattedAddress(
                        $order->getShippingAddress()
                    );
                }
            }
            //payment and shipping info
            $this->returnArray['paymentandshippingHeading'] = __("Payment & Shipping Method");
            $this->returnArray['paymentMethodHeading'] = __("Payment Information:");
            $this->returnArray['paymentMethod'] = $paymentMethod;

            if ($this->orderViewBlock->isOrderCanShip($order)) {
                $this->returnArray['shipAndTrackInfoHeading'] = __("Shipping and Tracking Information");
                if ($order->getShippingDescription()) {
                    $this->returnArray["shippingMethod"] = $this->viewTemplate->escapeHtml(
                        $order->getShippingDescription()
                    );
                } else {
                    $this->returnArray["shippingMethod"] = __("No shipping information available");
                }
            }
            //Items Refunded
            $this->returnArray['itemsRefundedHeading'] = __("Items Refunded");
            $this->returnArray['productNameHeading'] = __("Product Name");
            $this->returnArray['priceHeading']       = __("Price");
            $this->returnArray['qtyHeading']         = __("Qty");
            $this->returnArray['subtotalHeading']    = __("Subtotal");
            $this->returnArray['codChargeHeading']   = __("COD Charges");
            $this->returnArray['taxAmtHeading']      = __("Tax Amount");
            $this->returnArray['discountAmtHeading'] = __("Discount Amount");
            $this->returnArray['rowTotalHeading']    = __("Row Total");

            $_items = $order->getItemsCollection();
            $i = 0;
            $_count = $_items->count();
            $subtotal = 0;
            $vendor_subtotal =0;
            $totaltax = 0;
            $admin_subtotal =0;
            $shippingamount = 0;
            $couponamount = 0;
            $codcharges_total = 0;
            $creditmemo_items = $this->orderViewBlock->getCreditmemoItemsCollection($this->creditmemoId);
            $itemList         = [];
            foreach ($creditmemo_items as $_item) {
                foreach ($_items as $_ordereditem) {
                    $eachItem     = [];
                    if ($_ordereditem->getProductType()!='bundle') {
                        if ($_item->getOrderItemId() != $_ordereditem->getItemId()) {
                            continue;
                        }
                        $row_total = 0;
                        $available_seller_item = 0;
                        $shippingcharges = 0;
                        $couponcharges = 0;
                        $itemPrice = 0;
                        $seller_item_cost = 0;
                        $totaltax_peritem = 0;
                        $codcharges_peritem = 0;
                        $seller_item_commission = 0;
                        $orderid = $orderId;

                        $seller_orderslist = $this->orderViewBlock->getSellerOrdersList(
                            $orderid,
                            $_item->getProductId(),
                            $_item->getOrderItemId()
                        );
                        foreach ($seller_orderslist as $seller_item) {
                            $parentitem_falg = 0;
                            $available_seller_item = 1;
                            $totalamount = $seller_item->getTotalAmount();
                            $seller_item_cost = $seller_item->getActualSellerAmount();
                            $seller_item_commission = $seller_item->getTotalCommission();
                            $shippingcharges = $seller_item->getShippingCharges();
                            $couponcharges = $seller_item->getAppliedCouponAmount();
                            $itemPrice = $seller_item->getMageproPrice();
                            $totaltax_peritem = $seller_item->getTotalTax();
                            if ($paymentCode=='mpcashondelivery') {
                                $codcharges_peritem = $seller_item->getCodCharges();
                            }
                        }
                        if ($available_seller_item == 1) {
                            $i++;
                            $seller_item_qty = $_item->getQty();
                            $row_total=$itemPrice*$seller_item_qty;
                            $vendor_subtotal=$vendor_subtotal+$seller_item_cost;
                            $subtotal=$subtotal+$row_total;
                            $admin_subtotal = $admin_subtotal +$seller_item_commission;
                            $totaltax=$totaltax+$totaltax_peritem;
                            $codcharges_total=$codcharges_total+$codcharges_peritem;
                            $shippingamount = $shippingamount+$shippingcharges;
                            $couponamount = $couponamount+$couponcharges;

                            $result = [];
                            if ($options = $_ordereditem->getProductOptions()) {
                                if (isset($options['options'])) {
                                    $result = array_merge($result, $options['options']);
                                }
                                if (isset($options['additional_options'])) {
                                    $result = array_merge($result, $options['additional_options']);
                                }
                                if (isset($options['attributes_info'])) {
                                    $result = array_merge($result, $options['attributes_info']);
                                }
                            }
                            if ($_ordereditem->getParentItem()) {
                                continue;
                            }
                            $eachItem["productName"]             = $this->viewTemplate->escapeHtml($_item->getName());
                            $eachItem["customOption"]            = [];
                            $eachItem["downloadableOptionLable"] = "";
                            $eachItem["downloadableOptionValue"] = [];
                            if ($_item->getProductType() == "downloadable") {
                                if ($options = $result) {
                                    $customOption = [];
                                    foreach ($options as $option) {
                                        $eachOption = [];
                                        $eachOption["label"] = $this->viewTemplate->escapeHtml($option["label"]);
                                        if (!$this->viewTemplate->getPrintStatus()) {
                                            $formatedOptionValue = $this->orderViewBlock->getFormatedOptionValue(
                                                $option
                                            );
                                            if (isset($formatedOptionValue["full_view"])) {
                                                $eachOption["value"] = $formatedOptionValue["full_view"];
                                            } else {
                                                $eachOption["value"] = $formatedOptionValue["value"];
                                            }
                                        } else {
                                            $eachOption["value"] = $this->viewTemplate->escapeHtml(
                                                (isset(
                                                    $option["print_value"]
                                                ) ? $option["print_value"] : $option["value"])
                                            );
                                        }
                                        $customOption[] = $eachOption;
                                    }
                                    $eachItem["customOption"] = $customOption;
                                }
                                // downloadable /////////////////////////////////////////////////////////////////
                                if ($links = $this->orderViewBlock->getDownloadableLinks($_item->getId())) {
                                    $eachItem["downloadableOptionLable"] = $this->orderViewBlock->getLinksTitle(
                                        $_item->getId()
                                    );
                                    foreach ($links->getPurchasedItems() as $link) {
                                        $eachItem["downloadableOptionValue"][] = $this->viewTemplate->escapeHtml(
                                            $link->getLinkTitle()
                                        );
                                    }
                                }
                            } else {
                                if ($options = $result) {
                                    $customOption = [];
                                    foreach ($options as $option) {
                                        $eachOption = [];
                                        $eachOption["label"] = $this->viewTemplate->escapeHtml($option["label"]);
                                        if (!$this->viewTemplate->getPrintStatus()) {
                                            $formatedOptionValue = $this->orderViewBlock->getFormatedOptionValue(
                                                $option
                                            );
                                            if (isset($formatedOptionValue["full_view"])) {
                                                $eachOption["value"] = $formatedOptionValue["full_view"];
                                            } else {
                                                $eachOption["value"] = $formatedOptionValue["value"];
                                            }
                                        } else {
                                            $eachOption["value"] = $this->viewTemplate->escapeHtml(
                                                (isset(
                                                    $option["print_value"]
                                                ) ? $option["print_value"] : $option["value"])
                                            );
                                        }
                                        $customOption[] = $eachOption;
                                    }
                                    $eachItem["customOption"] = $customOption;
                                }
                            }
                            $eachItem["sku"]   = $_item->getSku();
                            $eachItem["price"] = $this->helperCatalog->stripTags(
                                $order->formatPrice($_item->getPrice())
                            );
                            $eachItem["qty"] = (int)$_item->getQty();
                            $eachItem["subTotal"] = $this->helperCatalog->stripTags(
                                $order->formatPrice($_item->getRowTotal())
                            );
                            $eachItem["subTotal"] = $this->helperCatalog->stripTags(
                                $order->formatPrice($_item->getRowTotal())
                            );
                            if ($paymentCode == 'mpcashondelivery') {
                                $eachItem["mpcodprice"] = $this->helperCatalog->stripTags(
                                    $order->formatPrice(
                                        $this->dashboardHelper->getOrderedPricebyorder($order, $codcharges_peritem)
                                    )
                                );
                            }
                            $eachItem["totalTax"] = $this->helperCatalog->stripTags(
                                $order->formatPrice($_item->getTaxAmount())
                            );
                            $eachItem["discountTotal"] = $this->helperCatalog->stripTags(
                                $order->formatPrice(-$_item->getDiscountAmount())
                            );
                            $eachItem["rowTotal"] = $this->helperCatalog->stripTags(
                                $order->formatPrice(
                                    $_item->getRowTotal()+
                                    $_item->getTaxAmount()+$this->dashboardHelper->getOrderedPricebyorder(
                                        $order,
                                        $codcharges_peritem
                                    )-$_item->getDiscountAmount()
                                )
                            );
                        }
                    } else {
                        // for bundle product
                        if ($_ordereditem->getChildrenItems()) {
                            $bundleitems = array_merge([$_ordereditem], $_ordereditem->getChildrenItems());
                        } else {
                            $bundleitems = [$_ordereditem];
                        }
                        $_count = count($bundleitems);
                        $_index = 0;
                        $_prevOptionId = '';
                        foreach ($bundleitems as $_bundleitem) {
                            if ($_item->getOrderItemId() != $_bundleitem->getItemId()) {
                                continue;
                            }
                            $attributes_option = $this->orderViewBlock->getSelectionAttribute($_bundleitem);
                            if ($_bundleitem->getParentItem()) {
                                $attributes = $attributes_option;
                                if ($_prevOptionId != $attributes['option_id']) {
                                    $eachItem["productName"] = $attributes['option_label'];
                                    $_prevOptionId = $attributes['option_id'];
                                }
                            }
                            if (!$_bundleitem->getParentItem()) {
                                $eachItem["productName"] = $this->viewTemplate->escapeHtml($_bundleitem->getName());
                                $eachItem["sku"] = $_bundleitem->getSku();
                                $eachItem["price"] = $order->formatPrice($_item->getPrice());
                            } else {
                                if ($_bundleitem->getQtyRefunded() == 0) {
                                    continue;
                                }
                                $row_total = 0;
                                $available_seller_item = 0;
                                $shippingcharges = 0;
                                $couponcharges = 0;
                                $itemPrice = 0;
                                $seller_item_cost = 0;
                                $totaltax_peritem = 0;
                                $codcharges_peritem = 0;
                                $seller_item_commission = 0;
                                $orderid = $orderId;
                                $seller_orderslist = $this->orderViewBlock->getSellerOrdersList(
                                    $orderid,
                                    $_bundleitem->getProductId(),
                                    $_bundleitem->getItemId()
                                );
                                foreach ($seller_orderslist as $seller_item) {
                                    $parentitem_falg = 0;
                                    $available_seller_item = 1;
                                    $totalamount = $seller_item->getTotalAmount();
                                    $seller_item_cost = $seller_item->getActualSellerAmount();
                                    $seller_item_commission = $seller_item->getTotalCommission();
                                    $shippingcharges = $seller_item->getShippingCharges();
                                    $couponcharges = $seller_item->getAppliedCouponAmount();
                                    $itemPrice = $seller_item->getMageproPrice();
                                    $totaltax_peritem = $seller_item->getTotalTax();
                                    if ($paymentCode=='mpcashondelivery') {
                                        $codcharges_peritem = $seller_item->getCodCharges();
                                    }
                                }
                                $i++;
                                $seller_item_qty = $_bundleitem->getQtyRefunded();
                                $row_total=$itemPrice*$seller_item_qty;
                                $vendor_subtotal=$vendor_subtotal+$seller_item_cost;
                                $subtotal=$subtotal+$row_total;
                                $admin_subtotal = $admin_subtotal +$seller_item_commission;
                                $totaltax=$totaltax+$totaltax_peritem;
                                $codcharges_total=$codcharges_total+$codcharges_peritem;
                                $shippingamount = $shippingamount+$shippingcharges;
                                $couponamount = $couponamount+$couponcharges;
                                $eachItem["productName"] = $this->orderViewBlock->getValueHtml($_bundleitem);
                                $addInfoBlock = $this->orderViewBlock->getOrderItemAdditionalInfoBlock();
                                if ($addInfoBlock) {
                                    $eachItem["productNameAdditional"] = $this->helperCatalog->stripTags(
                                        $addInfoBlock->setItem($_bundleitem)->toHtml()
                                    );
                                }
                                $eachItem["sku"] = $_bundleitem->getSku();
                                $eachItem["price"] = $this->helperCatalog->stripTags(
                                    $order->formatPrice($_bundleitem->getPrice())
                                );
                                $eachItem["qty"] = (int)$_item->getQty();
                                $eachItem["subTotal"] = $this->helperCatalog->stripTags(
                                    $order->formatPrice($_item->getRowTotal())
                                );
                                if ($paymentCode == 'mpcashondelivery') {
                                    $eachItem["mpcodprice"] = $this->helperCatalog->stripTags(
                                        $order->formatPrice(
                                            $this->dashboardHelper->getOrderedPricebyorder($order, $codcharges_peritem)
                                        )
                                    );
                                }
                                $eachItem["totalTax"] = $this->helperCatalog->stripTags(
                                    $order->formatPrice($_item->getTaxAmount())
                                );
                                $eachItem["discountTotal"] = $this->helperCatalog->stripTags(
                                    $order->formatPrice(-$_item->getDiscountAmount())
                                );
                                $eachItem["rowTotal"] = $this->helperCatalog->stripTags(
                                    $order->formatPrice(
                                        (
                                            $_item->getRowTotal()+
                                            $_item->getTaxAmount()+
                                            $this->dashboardHelper->getOrderedPricebyorder(
                                                $order,
                                                $codcharges_peritem
                                            )-
                                            $_item->getDiscountAmount()
                                        )
                                    )
                                );
                            }
                        }
                    }
                    if (!empty($eachItem)) {
                        $itemList[] = $eachItem;
                    }
                }
            }
            $this->returnArray["itemList"] = $itemList;
            $marketplaceOrders = $this->orderViewBlock->getSellerOrderInfo($orderId);
            foreach ($marketplaceOrders as $tracking) {
                $shippingamount = $tracking->getShippingCharges();
            }
            $this->returnArray["totals"] = [];
            $subTotalAmount = $this->helperCatalog->stripTags($order->formatPrice($creditmemo->getSubtotal()));
            $subTotal = [
                'code' => 'subTotal',
                'label' => __("Subtotal"),
                'value' => $creditmemo->getSubtotal(),
                'formattedValue' => $subTotalAmount
            ];
            $this->returnArray["totals"][] = $subTotal;

            $discountAmount = $this->helperCatalog->stripTags($order->formatPrice($creditmemo->getDiscountAmount()));
            $discount = [
                'code' => 'discount',
                'label' => __("Discount"),
                'value' => $creditmemo->getDiscountAmount(),
                'formattedValue' => $discountAmount
            ];
            $this->returnArray["totals"][] = $discount;

            $totalTaxAmount = $this->helperCatalog->stripTags($order->formatPrice($creditmemo->getTaxAmount()));
            $tax = [
                'code' => 'tax',
                'label' => __("Total Tax"),
                'value' => $creditmemo->getTaxAmount(),
                'formattedValue' => $totalTaxAmount
            ];
            $this->returnArray["totals"][] = $tax;

            $totalShippingAmount = $this->helperCatalog->stripTags(
                $order->formatPrice($creditmemo->getShippingAmount())
            );
            $totalShipping = [
                'code' => 'shipping',
                'label' => __("Shipping & Handling"),
                'value' => $creditmemo->getShippingAmount(),
                'formattedValue' => $totalShippingAmount
            ];
            $this->returnArray["totals"][] = $totalShipping;

            if ($paymentCode == 'mpcashondelivery') {
                $this->returnArray["codHeading"] = __("Total COD Charges");
                $codAmount = $this->helperCatalog->stripTags($order->formatPrice(0));
                $cod = [
                    'code' => 'cod',
                    'label' => __("Total COD Charges"),
                    'value' => $order->formatPrice(0),
                    'formattedValue' => $codAmount
                ];
                $this->returnArray["totals"][] = $cod;
            }

            $adjustmentAmt = $this->helperCatalog->stripTags($order->formatPrice($creditmemo->getAdjustmentPositive()));
            $adjustment = [
                'code' => 'adjustmentRefund',
                'label' => __("Adjustment Refund"),
                'value' => $creditmemo->getAdjustmentNegative(),
                'formattedValue' => $adjustmentAmt
            ];
            $this->returnArray["totals"][] = $adjustment;
            
            $adjustmentFeeAmount = $this->helperCatalog->stripTags(
                $order->formatPrice($creditmemo->getAdjustmentNegative())
            );
            $adjustmentFee = [
                'code' => 'adjustmentFee',
                'label' => __("Adjustment Fee"),
                'value' => $creditmemo->getAdjustmentNegative(),
                'formattedValue' => $adjustmentFeeAmount
            ];
            $this->returnArray["totals"][] = $adjustmentFee;
            
            $grandTotalAmt = $this->helperCatalog->stripTags($order->formatPrice($creditmemo->getGrandTotal()));
            $grandTotal = [
                'code' => 'grandTotalAmt',
                'label' => __("Grand Total"),
                'value' => $creditmemo->getGrandTotal(),
                'formattedValue' => $grandTotalAmt
            ];
            $this->returnArray["totals"][] = $grandTotal;

            $this->returnArray["success"]   = true;
            $this->emulate->stopEnvironmentEmulation($environment);
            $this->helper->log($this->returnArray, "logResponse", $this->wholeData);
            $this->checkNGenerateEtag($cacheString);
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
            $this->eTag          = $this->wholeData["eTag"]          ?? "";
            $this->storeId       = $this->wholeData["storeId"]       ?? 0;
            $this->incrementId   = $this->wholeData["incrementId"]   ?? 0;
            $this->creditmemoId  = $this->wholeData["creditmemoId"]  ?? 0;
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
