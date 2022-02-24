define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Ui/js/lib/validation/validator'
], function ($,wrapper,quote,validator) {
    'use strict';

    return function (setShippingInformationAction) {
        
        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            let shippingAddress = quote.shippingAddress();
            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }
            let customAttribute = shippingAddress.customAttributes;
            if( Object.keys(customAttribute)[0] != 0){
                Object.keys(customAttribute).forEach(key => {
                    shippingAddress['extension_attributes'][key] = customAttribute[key].value;
                });
            }else{
                shippingAddress.customAttributes.forEach(function(item, index){
                    shippingAddress['extension_attributes'][item.attribute_code] = item.value;
                });
            }

            return originalAction();
        });
    };
});
