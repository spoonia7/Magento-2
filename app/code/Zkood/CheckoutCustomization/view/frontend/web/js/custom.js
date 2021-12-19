require(['jquery'], function ($) {
    'use strict';

    $(document).ready(function () {
        setTimeout(() => {
            $('input[name="city"]').val("Kuwait").trigger("change");
        }, 5000)
    })
})
