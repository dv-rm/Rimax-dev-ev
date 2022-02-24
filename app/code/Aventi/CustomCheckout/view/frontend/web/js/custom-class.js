define([
    'uiRegistry'
], function (registry) {
    'use strict';
    function getLabelSelected (valueSelected,options){
        let returnValue = '';
        options.forEach(function(row) {
            if(row.value==valueSelected){
                returnValue = row.label;
            }
        })
        return returnValue;
    }

    return function () {
        const routeComponents = 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.';
        let id_number = registry.get(routeComponents+'id_number');
        let vat_id = registry.get(routeComponents+'vat_id');
        let taxvat = registry.get(routeComponents+'taxvat');
        let id_verif_digit = registry.get(routeComponents+'id_verif_digit');
        let id_type = registry.get(routeComponents+'id_type');
        let dnitype = registry.get(routeComponents+'dnitype');
        let address2 = registry.get(routeComponents+'street.1');

        let arrTypeDni = [];
        arrTypeDni['Cédula de ciudadanía'] = "CC"
        arrTypeDni['Cédula de extranjería'] = "CE"
        arrTypeDni['NIT'] = "NIT"
        arrTypeDni['Pasaporte'] = "PP"
        arrTypeDni['Tarjeta de extranjería'] = "CE"
        arrTypeDni['Documento de identificación extranjero'] = "CE"
        arrTypeDni['Sin identificación del exterior o para uso definido por la DIAN'] = "CC"

        console.log('Valor Documento: '+id_type.value())

        let ValueTypeDni = arrTypeDni[id_type.value()];
        dnitype.hide();

        let verif_digit = '';
        if(id_type.value()=='NIT')
        {
            id_verif_digit.show()
            verif_digit = "-"+id_verif_digit.value()
            address2.value(id_type.value()+": "+id_number.value()+verif_digit);
            taxvat.value(id_number.value()+verif_digit);
            vat_id.value(id_number.value()+verif_digit);
        }
        else{
            id_verif_digit.hide()
            address2.value(id_type.value()+": "+id_number.value());
            taxvat.value(id_number.value());
            vat_id.value(id_number.value());
        }

        dnitype.value(ValueTypeDni);
    };
})
