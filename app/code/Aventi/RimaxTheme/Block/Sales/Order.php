<?php

namespace Aventi\RimaxTheme\Block\Sales;

use Magento\Framework\Math\Random;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class Order extends \Magento\Framework\View\Element\Html\Link
{

    /**
     * Customer session
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    public function __construct(
        Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null,
        ?Random $random = null
    ) {
        parent::__construct($context, $data, $secureRenderer, $random);
        $this->httpContext = $httpContext;
    }

    /**
     * Is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }
}
