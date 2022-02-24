<?php
/**
*
* PayU Mapped Attributes para Magento 2
*
* @category     elOOm
* @package      Modulo PayUMappedAttributes
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.0
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayUMappedAttributes\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\Order;

class MappedOrderAttributeDefinition extends \Magento\Framework\App\Helper\AbstractHelper {

	public function __construct(Context $context) {
		parent::__construct($context);
	}

	public function getTaxvat(Order $order): string {
		//if ($order->getCustomerIsGuest()) {
			//return $order->getBillingAddress()->getIdNumber();
		//}
        return $order->getCustomerTaxvat()?? $order->getCustomerIdNumber();
		//return $order->getCustomerTaxvat();
	}

	public function getDniType(Order $order): ?string {
		/*if (null != $order->getBillingAddress()->getDnitype()) {
            if (null != $order->getBillingAddress()->getIdType()) {
                return "CC";
            }
            else{
                return $order->getBillingAddress()->getIdType();
            }
			//return $order->getBillingAddress()->getIdType();
        }*/
        //return $order->getCustomAttribute("DNI_TYPE")->getValue();
        $typeDocs[19] = 'CC';
        $typeDocs[20] = 'CE';
        $typeDocs[21] = 'NIT';
        $typeDocs[23] = 'CE';
        $typeDocs[24] = 'PP';
        $typeDocs[25] = 'CE';
        $typeDocs[26] = 'DE';

        return $typeDocs[$order->getCustomerIdType()]; //REVISAR ESTA SOLUCION
	}
}
