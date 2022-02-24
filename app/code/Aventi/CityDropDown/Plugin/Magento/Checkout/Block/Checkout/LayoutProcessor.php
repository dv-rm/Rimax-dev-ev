<?php

namespace Aventi\CityDropDown\Plugin\Magento\Checkout\Block\Checkout;

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
     * @param LayoutProcessor $subject
     * @param array $result
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
                                                         $result,
                                                         $jsLayout
    ) {
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city'] = $this->getConfigShipping();

        return $result;
    }

    /**
     * @return $field
     */
    private function getConfigShipping()
    {
        $field = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'city'
            ],
            'cacheable' => true,
            'label' => 'City',
            'value' => '',
            'dataScope' => 'shippingAddress.city',
            'provider' => 'checkoutProvider',
            'sortOrder' => 60,
            'customEntry' => null,
            'visible' => true,
            'options' => [],
            'validation' => [
                'required-entry' => true
            ],
            'id' => 'city',
        ];


        return $field;
    }

    /**
     * @return $field
     */
    private function getConfigBilling()
    {
        $field = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'billingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'city'
            ],
            'cacheable' => true,
            'label' => 'City',
            'value' => '',
            'dataScope' => 'billingAddress.city',
            'provider' => 'checkoutProvider',
            'sortOrder' => 60,
            'customEntry' => null,
            'visible' => true,
            'options' => [],
            'validation' => [
                'required-entry' => true
            ],
            'id' => 'city',
        ];


        return $field;
    }
}
