/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
var config = {
    map: {
        '*': {
            "Magento_Checkout/js/action/place-order":
                "Yosto_CustomerAddress/js/custom-place-order-action",
            "Magento_Checkout/js/action/create-shipping-address":
                "Yosto_CustomerAddress/js/custom-create-shipping-address",
            "Magento_Checkout/js/action/create-billing-address":
                "Yosto_CustomerAddress/js/custom-create-billing-address",
            "Magento_Checkout/template/shipping-address/address-renderer/default.html":
                "Yosto_CustomerAddress/template/shipping-address/address-renderer/default.html",
            "Magento_Checkout/template/shipping-information/address-renderer/default.html":
                "Yosto_CustomerAddress/template/shipping-information/address-renderer/default.html",
            "Magento_Checkout/template/billing-address/details.html":
                "Yosto_CustomerAddress/template/billing-address/details.html"

        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping-information/list': {
                'Yosto_CustomerAddress/js/shipping-information-list-mixin': true
            },
            'Yosto_CustomerAddress/js/custom-place-order-action': {
                'Magento_CheckoutAgreements/js/model/place-order-mixin': true
            },
        }
    }
};
