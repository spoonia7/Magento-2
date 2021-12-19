/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/address-converter',
    'jquery'
], function (addressList, addressConverter, $) {
    'use strict';

    return function (addressData) {
        var customAttributes = window.checkoutConfig.custom_address_attributes;
        var address = addressConverter.formAddressDataToQuoteAddress(addressData),
            isAddressUpdated = addressList().some(function (currentAddress, index, addresses) {
                if (currentAddress.getKey() == address.getKey()) { //eslint-disable-line eqeqeq
                    addresses[index] = address;

                    return true;
                }

                return false;
            });

        if (!isAddressUpdated) {
            /**
             * new or reload, address.customAttributes is undefined
             */
            if (!address.customAttributes) {
                address.customAttributes = {};
            }

            /**
             * reload, addressData.customAttributes has data
             * new, address.customAttribute is undefined
             */
            if (!addressData.customAttributes) {
                addressData.customAttributes = {};
            }
            $.each(customAttributes, function (index, code) {

                /**
                 * Refresh page just added address
                 */
                if (addressData[code] && addressData.customAttributes[code]) {

                    address.customAttributes[code] = addressData.customAttributes[code];

                } else {
                    /**
                     * Add new address
                     */
                    $.each(customAttributes, function (index, code) {
                        var item = $('div[name^="shippingAddress"] [name="' + code + '"]');
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

                        address.customAttributes[code] = {
                            attribute_code: code,
                            label: label,
                            value: val
                        };
                        addressData.customAttributes[code] = {
                            attribute_code: code,
                            label: label,
                            value: val
                        };

                    });
                }

            });
            addressList.push(address);
        } else {
            /**
             * update just added address
             */
            if (!address.customAttributes) {
                address.customAttributes = {};
            }
            if (!addressData.customAttributes) {
                addressData.customAttributes = {};
            }
            $.each(customAttributes, function (index, code) {
                var item = $('div[name^="shippingAddress"] [name="' + code + '"]');
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

                address.customAttributes[code] = {
                    attribute_code: code,
                    label: label,
                    value: val
                };
                addressData.customAttributes[code] = {
                    attribute_code: code,
                    label: label,
                    value: val
                }

            });
            addressList.valueHasMutated();
        }

        return address;
    };
});
