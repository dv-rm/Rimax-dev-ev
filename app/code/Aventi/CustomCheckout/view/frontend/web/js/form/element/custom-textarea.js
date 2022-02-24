define([
    'Magento_Ui/js/form/element/abstract',
    'Aventi_CustomCheckout/js/custom-class',
], function (AbstractField,Aventi) {
    'use strict';

    return AbstractField.extend({

        initialize: function () {
            this._super();
            return this;
        },

        onchange: function () {
            Aventi();
        }

    });
});
