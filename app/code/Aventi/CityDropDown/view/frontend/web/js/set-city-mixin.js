define([
    'jquery',
    'mage/utils/wrapper',
    'mage/validation',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'Magento_Checkout/js/model/cart/cache'
], function ($, wrapper, validation, quote, rateRegistry,getTotalsAction,defaultTotal,cartCache) {
    'use strict';
    $(document).ready(function () {

        $(document).on('change', '[name="shippingAddress.region_id"]', function (e) {
            let region = e.target.value;
            if (region !== '') {
                loadCitiesByRegion(region,0);
            }
        });

        $(document).on('change', '[name="billingAddress.region_id"]', function (e) {
            let region = e.target.value;
            if (region !== '') {
                loadCitiesByRegion(region,1);
            }
        });

        $(document).on('change', '[name="shippingAddress.city"]', function (e) {
            let city = e.target.value;
            let $postalcode = $('[name="shippingAddress.postcode"]')[0].children[1].children.postcode;

            if (city !== '') {
                let postalCode = $(this).find(":selected").data('postal');
                $postalcode.value = postalCode;
                let street =  $('.form-shipping-address [name="street[0]"]').val();
                reload(street,  e.target.value, postalCode);
            } else {
                $postalcode.value = '';
            }
            $('input[name="postcode"]').prop('readonly', true).trigger("change");
        });

        $(document).on('change', '.form-shipping-address [name="street[0]"]', function (e) {
            let street = $('.form-shipping-address [name="street[0]"]').val();
            let postcode = $('[name="shippingAddress.postcode"] [name="postcode"]').val();
            let city = $('[name="shippingAddress.city"] [name="city"]').val();
            reload(street, city, postcode);
        });

        $(document).on('change', '[name="billingAddress.city"]', function (e) {
            let city = e.target.value;
            let $postalcode = $('[name="billingAddress.postcode"]')[0].children[1].children.postcode;
            if (city !== '') {
                $postalcode.value = $(this).find(":selected").data('postal');;
            } else {
                $postalcode.value = '';
            }
            $('input[name="postcode"]').prop('readonly', true).trigger("change");
        });
    });

    function loadCitiesByRegion(region, form)
    {
        $('body').trigger('processStart');

        let nameCity = form === 0 ? 'shippingAddress' : 'billingAddress';
        $('[name="'+nameCity+'.postcode"]')[0].children[1].children.postcode.value = '';
        var cities = $('[name="'+nameCity+'.city"]')[0].children[1].children[0];
        cities.innerHTML = '<option value="">Seccione una ciudad</option>';

        $.ajax({
            url: BASE_URL + 'citydropdown/index/index',
            type: "post",
            dataType: "json",
            data: {region_id: region},
            cache: false
        }).done(function (json) {
            json.forEach(function (city) {
                cities.innerHTML += "<option data-postal='" +
                    city.postalCode +
                    "' value='" +
                    city.name +
                    "' " +
                    //((attribute.name == this.val()) ? 'selected' : '')
                    " >" + city.name + "</option>";
            });
        });
        $('body').trigger('processStop');
    }

    function reload(street, city, postcode)
    {
        setTimeout(function(){
            let address = quote.shippingAddress();
            address.postcode = postcode;
            address.street = [street];
            address.city = city;
            rateRegistry.set(address.getKey(), null);
            rateRegistry.set(address.getCacheKey(), null);
            quote.shippingAddress(address);
            //address.trigger_reload = new Date().getTime();
        }, 2000);
    }

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            return originalAction();
        });
    }

});
