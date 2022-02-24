<?php

namespace Aventi\RimaxTheme\Block\Account;

use Magento\Customer\Model\Url;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Data\Helper\PostHelper;

class AuthorizationLink extends \Magento\Customer\Block\Account\AuthorizationLink
{

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Context $httpContext,
        Url $customerUrl,
        PostHelper $postDataHelper,
        array $data = []
    ) {
        parent::__construct($context, $httpContext, $customerUrl, $postDataHelper, $data);
    }
}
