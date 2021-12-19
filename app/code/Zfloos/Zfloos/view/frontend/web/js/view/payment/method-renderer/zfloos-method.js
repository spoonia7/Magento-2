/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/url',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function ($, Component, url, placeOrderAction, selectPaymentMethodAction, customer, checkoutData, additionalValidators) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Zfloos_Zfloos/payment/zfloos'
            },
            
            placeOrder: function(data, event) {
                //var linkUrl = url.build('payment/index/index');
                //window.location.href = linkUrl;
                if (event) {
                    event.preventDefault();
                }
                var self = this,
                    placeOrder,
                    emailValidationResult = customer.isLoggedIn(),
                    loginFormSelector = 'form[data-role=email-with-possible-login]';
                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }
                if (emailValidationResult && this.validate() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);
                    placeOrder = placeOrderAction(this.getData(), false, this.messageContainer);

                    $.when(placeOrder).fail(function () {
                        self.isPlaceOrderActionAllowed(true);
                    }).done(this.afterPlaceOrder.bind(this));
                    return true;
                }
                return false;
            },
            afterPlaceOrder: function () {
                //alert("hii");
                $.mage.redirect(window.checkoutConfig.payment.zfloos.redirectUrl);
            }
            /** Returns send check to info */
            /*getMailingAddress: function() {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },*/

           
        });
    }
);
