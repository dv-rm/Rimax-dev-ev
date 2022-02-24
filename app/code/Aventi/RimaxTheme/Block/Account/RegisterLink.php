<?php

namespace Aventi\RimaxTheme\Block\Account;

class RegisterLink extends \Magento\Customer\Block\Account\RegisterLink
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Registration $registration,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = [])
    {
        parent::__construct($context, $httpContext, $registration, $customerUrl, $data);
    }
}
