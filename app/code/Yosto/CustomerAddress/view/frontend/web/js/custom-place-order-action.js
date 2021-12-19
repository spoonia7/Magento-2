/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/place-order',
        'jquery',
        'Magento_Checkout/js/checkout-data',
        "Magento_Customer/js/customer-data"
    ],
    function (quote, urlBuilder, customer, placeOrderService, $, checkoutData, customerData) {
        'use strict';

        return function (paymentData, messageContainer) {
            var serviceUrl, payload;

            var customAttributes = window.checkoutConfig.custom_address_attributes;

            var jsonObj = [];

            $.each(customAttributes, function (index, code) {
                var item = $('div[name^="billingAddresscheckmo"] [name="' + code + '"]');

                if ($("#payment .payment-method").length > 0){
                    item = $('#payment .payment-method._active div[name^="billingAddress"] [name="' + code + '"]');
                }

                var val = item.val();
                if (item.hasClass('checkbox')) {
                    if (item.prop('checked')) {
                        val = 1;
                    } else {
                        val = 0;
                    }
                }
                var elementObj =  {};

                elementObj['name'] = code;
                elementObj['value'] = val;

                jsonObj.push(elementObj);

            });
            var custom_data = JSON.stringify(jsonObj);
            var billingAddress = quote.billingAddress();
            billingAddress.extension_attributes = {custom_data: custom_data};

            var selectedPaymentMethod = checkoutData.getSelectedPaymentMethod();
            var addressCheckbox = $("#billing-address-same-as-shipping-" + selectedPaymentMethod);
            if (addressCheckbox.length > 0) {
                var shippingIsSameAsBilling = addressCheckbox.prop("checked");
                if (shippingIsSameAsBilling) {
                    billingAddress.same_as_billing = 1;
                }
            }

            customerData.set("shipping_address_ca", {});

            customerData.set("billing_address_ca", {});

            payload = {
                cartId: quote.getQuoteId(),
                billingAddress: billingAddress,
                paymentMethod: paymentData
            };

            if (customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/carts/mine/payment-information', {});
            } else {
                serviceUrl = urlBuilder.createUrl('/guest-carts/:quoteId/payment-information', {
                    quoteId: quote.getQuoteId()
                });
                payload.email = quote.guestEmail;
            }

            return placeOrderService(serviceUrl, payload, messageContainer);
        };
    }
);
