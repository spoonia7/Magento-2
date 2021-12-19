define(['jquery'], function ($) {
    'use strict';

    return function (config, element) {
        $.get(config.baseUrl + 'customer/block?handle=purchased_coupons', function (result) {
            element.innerHTML = result;
        })
    }
});
