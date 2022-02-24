<?php

namespace Aventi\ShowOutOfStockSwatch\Plugin;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;
use Magento\Framework\App\ObjectManager;

class ShowOutOfStockProductsPlugin
{

    /**
     * Get Allowed Products
     *
     * @return Product[]
     */
    public function beforeGetAllowProducts(Configurable $subject)
    {
        if (!$subject->hasAllowProducts()) {
            $allProducts = $subject->getProduct()->getTypeInstance()->getUsedProducts($subject->getProduct(), null);
            $products = [];
            foreach ($allProducts as $product) {
                if ($product->getStatus() != Status::STATUS_DISABLED) {
                    $products[] = $product;
                }
            }
            $subject->setAllowProducts($products);
        } else {
            $_children = $subject->getProduct()->getExtensionAttributes()->getConfigurableProductLinks();
            if ($_children!=null) {
                foreach ($_children as $child) {
                    $objectManager = ObjectManager::getInstance();
                    $product = $objectManager->create(Product::class)->load($child);
                    $products[] = $product;
                }
                $subject->setAllowProducts($products);
            }

        }

        return [];
    }
}
