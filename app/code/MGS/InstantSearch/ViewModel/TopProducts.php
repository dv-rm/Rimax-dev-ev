<?php
namespace MGS\InstantSearch\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Catalog\Model\ProductRepository;
use \Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Check is available add to compare.
 */
class TopProducts implements ArgumentInterface
{
    /**
     * @var UrlHelper
     */
    private $urlHelper;
    /**
     * @var ProductRepository
     */
    private $_productRepository;
    /**
     * @var Configurable
     */
    private $configurable;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param UrlHelper $urlHelper
     * @param ProductRepository $ProductRepository
     * @param Configurable $configurable
     */
    public function __construct(
                UrlHelper $urlHelper,
                ProductRepository $ProductRepository,
                Configurable $configurable,
                CollectionFactory $collectionFactory
    )
    {
        $this->urlHelper = $urlHelper;
        $this->_productRepository = $ProductRepository;
        $this->configurable = $configurable;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Wrapper for the PostHelper::getPostData()
     *
     * @param string $url
     * @param array $data
     * @return array
     */
    public function getPostData()
    {
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /*$productCollection = $objectManager->create('Magento\Reports\Model\ResourceModel\Report\Collection\Factory');
        $collection = $productCollection->create('Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection');

        $collection->setPeriod('month');
        $collection->setPageSize(6);
        //$collection->setPeriod('year');
        //$collection->setPeriod('day');*/

        //$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');

        //echo 'Antes';
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter('type_id', 'configurable');
        $collection->setPageSize(4);
        $collection->getSelect()->order('rand()');

        //$collection->getSelect()->orderRand();
        //$collection = $collection->getItems();
        //$collection->load();
        //print_r($collection);
        //echo 'ACAAA';
        return $collection;
    }

    /**
     * @param $prodId
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct($prodId): \Magento\Catalog\Api\Data\ProductInterface
    {
        return $this->_productRepository->getById($prodId);


    }
}
