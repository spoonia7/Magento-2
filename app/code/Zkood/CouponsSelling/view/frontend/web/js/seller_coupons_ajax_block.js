define(['jquery'], function ($) {
    'use strict';

    return function (config, element) {
        $.get(config.baseUrl + 'customer/block?handle=seller_coupons', function (result) {
            element.innerHTML = result;
        })
    }
});
