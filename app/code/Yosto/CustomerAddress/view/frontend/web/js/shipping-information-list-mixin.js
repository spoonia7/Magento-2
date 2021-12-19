
define([
    'ko',
    'Magento_Checkout/js/model/quote',
    "Magento_Customer/js/customer-data",
    "jquery"
],function (ko, quote, customerData, $) {
    'use strict';

    var mixin = {

        /** @inheritdoc */
        initialize: function () {
            var self = this;

            this._super()
                .initChildren();
            quote.shippingAddress.subscribe(function (address) {
                if (!quote.billingAddress() && address) {
                    if (!$.isEmptyObject(customerData.get('shipping_address_ca')())) {
                        address.customAttributes = customerData.get('shipping_address_ca')();
                    }
                }
                self.createRendererComponent(address);
            });

            return this;
        },
    };

    return function (target) {
        return target.extend(mixin);
    };
});