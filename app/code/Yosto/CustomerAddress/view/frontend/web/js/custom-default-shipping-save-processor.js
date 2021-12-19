/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'mage/storage',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/model/shipping-save-processor/payload-extender',
        'jquery',
        "Magento_Customer/js/customer-data"
    ], function (
    ko,
    quote,
    resourceUrlManager,
    storage,
    paymentService,
    methodConverter,
    errorProcessor,
    fullScreenLoader,
    checkoutDataResolver,
    selectBillingAddressAction,
    payloadExtender,
    $,
    customerData
    ) {
        'use strict';

        return {
            saveShippingInformation: function () {
                var customAttributes = window.checkoutConfig.custom_address_attributes;

                if (!quote.shippingAddress().customAttributes) {
                    quote.shippingAddress().customAttributes = {};
                }

                var jsonObj = [];
                $.each(customAttributes, function (index, code) {
                    var item = $('div[name^="shippingAddress"] [name="' + code + '"]');
                    if (item.length && item.is(":visible")) {
                        var val = item.val();
                        var label = val;
                        if (item.hasClass('checkbox')) {
                            if (item.prop('checked')) {
                                val = 1;
                            } else {
                                val = 0;
                            }
                        } else if (item.hasClass('select')) {
                            label = $("#" + item.attr('id') + " option:selected").attr('data-title');
                        }
                        var elementObj = {};

                        elementObj['name'] = code;
                        elementObj['value'] = val;

                        jsonObj.push(elementObj);
                        quote.shippingAddress().customAttributes[code] = {
                            attribute_code: code,
                            label: label,
                            value: val
                        }
                    }

                });

                var custom_data = JSON.stringify(jsonObj);
                var payload;
                if (!quote.billingAddress() && quote.shippingAddress().canUseForBilling()) {
                    selectBillingAddressAction(quote.shippingAddress());

                }
                /**
                 * update changes for shipping address
                 */
                if (
                    quote.billingAddress().getCacheKey() == quote.shippingAddress().getCacheKey()
                ) {
                    quote.billingAddress().customAttributes = quote.shippingAddress().customAttributes;
                }


                customerData.set("shipping_address_ca", quote.shippingAddress().customAttributes);

                quote.shippingAddress(quote.shippingAddress());
                quote.billingAddress(quote.billingAddress());

                payload = {
                    addressInformation: {
                        shipping_address: quote.shippingAddress(),
                        billing_address: quote.billingAddress(),
                        shipping_method_code: quote.shippingMethod().method_code,
                        shipping_carrier_code: quote.shippingMethod().carrier_code,
                        extension_attributes: {
                            custom_data: custom_data
                        }
                    }
                };

                //payloadExtender(payload);

                fullScreenLoader.startLoader();

                return storage.post(
                    resourceUrlManager.getUrlForSetShippingInformation(quote),
                    JSON.stringify(payload)
                ).done(
                    function (response) {
                        quote.setTotals(response.totals);
                        paymentService.setPaymentMethods(methodConverter(response['payment_methods']));
                        fullScreenLoader.stopLoader();
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                        fullScreenLoader.stopLoader();
                    }
                );
            }
        };
    }
);
