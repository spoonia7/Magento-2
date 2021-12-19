/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/model/quote',
    "Magento_Customer/js/customer-data",
    'Magento_Checkout/js/checkout-data',
    'jquery'
], function (addressConverter, quote, customerData, checkoutData, $) {
    'use strict';

    return function (addressData) {

        var addData =  addressConverter.formAddressDataToQuoteAddress(addressData);

        /**
         * ===========Start=================
         */

        /**
         * set custom attributes data for billing address if refresh
         * payment page
         */
        if (!addData.customAttributes) {
            if (!$.isEmptyObject(customerData.get('billing_address_ca')())) {
                addData.customAttributes = customerData.get('billing_address_ca')();
            } else {
                addData.customAttributes = {};
            }

        }


        if ($('#payment .payment-method._active div[name^="billingAddress"]').length
            && $('#payment .payment-method._active div[name^="billingAddress"]').is(":visible")
        ) {


            var customAttributes = window.checkoutConfig.custom_address_attributes;

            /**
             * Get custom attribute value
             */
            $.each(customAttributes, function (index, code) {
                var item = $('#payment .payment-method._active div[name^="billingAddress"] [name="' + code + '"]');
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

                    addData.customAttributes[code] = {
                        attribute_code: code,
                        value: val,
                        label: label

                    };
                }


            });

            /**
             * update data again
             */
            if (!$.isEmptyObject(addData.customAttributes)) {
                customerData.set('billing_address_ca', addData.customAttributes)
            }

            /**
             * Know the button is clicked "same as shipping address"
             */
            var selectedPaymentMethod = checkoutData.getSelectedPaymentMethod();

            var addressCheckbox = $("#billing-address-same-as-shipping-" + selectedPaymentMethod);
            if (addressCheckbox.length > 0 && addressCheckbox.is(':visible')) {

                    var shippingIsSameAsBilling = addressCheckbox.prop("checked");
                    if (!shippingIsSameAsBilling) {
                        customerData.set("is_same_as_shipping", 0);
                    } else {
                        customerData.set("is_same_as_shipping", 1);
                    }

                    addressCheckbox.on('click', function (e) {
                        if (addressCheckbox.prop("checked")) {
                            customerData.set("is_same_as_shipping", 1);
                        }
                    })

            }

        }
        /**
         * ===========End=================
         */
        return addData;

    };
});
