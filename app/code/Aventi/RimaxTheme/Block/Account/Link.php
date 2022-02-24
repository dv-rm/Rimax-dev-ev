<?php

namespace Aventi\RimaxTheme\Block\Account;

use Magento\Customer\Model\Context;

class Link extends \Magento\Customer\Block\Account\Link
{

    /**
     * Customer session
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $customerUrl, $data);
        $this->httpContext = $httpContext;
    }

    /**
     * Is logged in
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
}
