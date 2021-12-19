define(['jquery'], function ($) {
    'use strict';

    return function (config, element) {
        $.get('/customer/block?handle=recipes_data', function (result) {
            element.innerHTML = result;
        })
    }
});
