<?php

namespace Aventi\CustomCheckout\Plugin\Magento\Checkout\Block\Checkout;

use \Psr\Log\LoggerInterface;

class LayoutProcessor
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param $result
     * @param $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $result,
        $jsLayout
    ) {
        $typeDocId = 'id_type';
        $docNumber = 'id_number';
        $verifDigitId = 'id_verif_digit';
        $tagAdressId = 'tag_address';
        $indicationsId = 'indications';
        $dnitypeId = 'dnitype';
        $taxVatId = 'taxvat';

        $arrTypeIds = [
            "Cédula de ciudadanía" => "Cédula de ciudadanía",
            "Cédula de extranjería" => "Cédula de extranjería",
            "NIT" => "NIT",
            "Tarjeta de extranjería" => "Tarjeta de extranjería",
            "Pasaporte" => "Pasaporte",
            "Documento de identificación extranjero" => "Documento de identificación extranjero",
            "Sin identificación del exterior o para uso definido por la DIAN" => "Sin identificación del exterior o para uso definido por la DIAN"
        ];
        $typeIds = [];
        foreach ($arrTypeIds as $key => $v) {
            $optVal = [];
            $optVal['value']= $v;
            $optVal['label'] = $key;
            $typeIds[] = $optVal;
        }
        $typeDoc = $this->createFormElement(
            $typeDocId,
            'select',
            10,
            'Tipo de Documento',
            '',
            true,
            true,
            $typeIds
        );
        $dnitype = $this->createFormElement(
            $dnitypeId,
            'select',
            10,
            'Tipo de Documento',
            '',
            true,
            true,
            $typeIds
        );
        $id_number = $this->createFormElement(
            $docNumber,
            'input',
            15,
            'Numero de Documento',
            '',
            true,
            true
        );
        $verifDigit         = $this->createFormElement(
            $verifDigitId,
            'input',
            16,
            'Digito de Verificacion',
            '',
            true,
            false
        );
        $taxVat         = $this->createFormElement(
            $taxVatId,
            'input',
            140,
            'Eloom TaxVat',
            '',
            true,
            false
        );

        $tagAdress = $this->createFormElement(
            $tagAdressId,
            'input',
            141,
            'Etiqueta de direccion',
            '',
            true,
            false
        );

        $indications   = $this->createFormElement(
            $indicationsId,
            'textarea',
            140,
            'Indicaciones',
            '',
            true,
            false
        );

        $shippingAddressFields = &$result['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];


        ## Personal Information
        $shippingAddressFields['firstname']['sortOrder'] = 1;
        $shippingAddressFields['lastname']['sortOrder'] = 2;
        $shippingAddressFields[$typeDocId] = $typeDoc;
        $shippingAddressFields[$dnitypeId] = $dnitype;

        $shippingAddressFields[$taxVatId] = $taxVat;
        $shippingAddressFields[$typeDocId]['sortOrder'] = 10;

        $shippingAddressFields[$docNumber] = $id_number;
        $shippingAddressFields[$verifDigitId] = $verifDigit;
        $shippingAddressFields['telephone']['sortOrder'] = 20;


        ## Shipping Info
        $fieldGroup = &$shippingAddressFields['custom-field-group']['children']['field-group']['children'];
        $fieldGroup['region'] = $shippingAddressFields['region'];
        $fieldGroup['city'] = $shippingAddressFields['city'];
        $fieldGroup['street'] = $shippingAddressFields['street'];
        $shippingAddressFields['region']['visible'] = false;
        $shippingAddressFields['city']['visible'] = false;
        $shippingAddressFields['street']['visible'] = false;
        $fieldGroup['region']['sortOrder'] = 30;
        $fieldGroup['city']['sortOrder'] = 40;
        $fieldGroup['street']['sortOrder'] = 65;

        $shippingAddressFields[$taxVatId] = $taxVat;
        $shippingAddressFields[$tagAdressId] = $tagAdress;
        $shippingAddressFields[$indicationsId] = $indications;

        /*Example: https://newbedev.com/checkout-form-how-to-wrap-multiple-elements-in-a-class-magento-2*/

        ## Hide elements
        $shippingAddressFields['country_id']['visible'] = false;
        $shippingAddressFields['company']['visible'] = false;
        $shippingAddressFields['postcode']['visible'] = false;
        $shippingAddressFields[$verifDigitId]['visible'] = false;
        $shippingAddressFields['dnitype']['visible'] = false;
        $shippingAddressFields['taxvat']['visible'] = false;
        $shippingAddressFields['vat_id']['visible'] = false;


        return $result;
    }

    /**
     * @param $id
     * @param $typeInput
     * @param $order
     * @param $label
     * @param string $placeHolder
     * @param bool $custom_attribute
     * @param bool $required
     * @param string $value
     * @param bool $visible
     * @return array
     */
    public function createFormElement($id, $typeInput, $order, $label, string $placeHolder = '', bool $custom_attribute, bool $required, $value = '', bool $visible = true): array
    {
        $readonly = false;
        $options = ($typeInput=='select') ? $value : [] ;
        $value = ($typeInput=='select') ? '' : $value ;
        $scope = ($custom_attribute) ? 'custom_attributes' : 'custom' ;
        if ($id == 'formattedAddr') {
            $readonly = true;
        }
        return [
            'component' => 'Aventi_CustomCheckout/js/form/element/custom-'.$typeInput,
            'config' => [
                // customScope is used to group elements within a single form (e.g. they can be validated separately)
                'customScope' => 'shippingAddress.'.$scope,
                'template' => 'ui/form/field',
                'elementTmpl' => 'Aventi_CustomCheckout/shipping/'.$typeInput,
                'id' => $id,
                'options' => $options,
            ],
            'id' => $id,
            'type' => $typeInput,
            'dataScope' => 'shippingAddress.'. $scope . '.' . $id,
            'label' => __($label),
            'provider' => 'checkoutProvider',
            'sortOrder' => $order,
            'validation' => [
                'required-entry' => $required
            ],
            'options' => $options,
            'placeholder' => __($placeHolder),
            'filterBy' => null,
            'customEntry' => null,
            'visible' => $visible,
            'value' => $value,
            'disabled' => $readonly// value field is used to set a default value of the attribute
        ];
    }
}
