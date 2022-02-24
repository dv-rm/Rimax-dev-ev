<?php


namespace Aventi\CityDropDown\Plugin\Magento\Checkout\Block\Cart;

class LayoutProcessor
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function afterProcess(
        \Magento\Checkout\Block\Cart\LayoutProcessor $subject,
        $result,
        $jsLayout
    ) {


        foreach ($result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                 ['payment']['children']['payments-list']['children'] as &$child)
        {

        }

        return $result;
    }
}
