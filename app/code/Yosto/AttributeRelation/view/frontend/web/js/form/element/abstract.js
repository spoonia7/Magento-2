/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
define([
    'underscore',
    'mageUtils',
    'uiLayout',
    'uiElement',
    'Magento_Ui/js/lib/validation/validator',
    "jquery"
], function (_, utils, layout, Element, validator, $) {
    'use strict';

        var mixin = {
            validate: function () {
                var value = this.value(),
                    result = validator(this.validation, value, this.validationParams),
                    message = !this.disabled() && this.visible() ? result.message : '',
                    isValid = this.disabled() || !this.visible() || $('#' + this.uid).hasClass('ignore-validate') || result.passed;

                this.error(message);
                this.bubble('error', message);

                //TODO: Implement proper result propagation for form
                if (!isValid) {
                    this.source.set('params.invalid', true);
                }

                return {
                    valid: isValid,
                    target: this
                };
            }
        };

        return function (target) {
            return target.extend(mixin);
        };
    });