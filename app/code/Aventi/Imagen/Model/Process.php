<?php

namespace Aventi\Imagen\Model;

use Aventi\Imagen\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Gallery\ReadHandler;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Gallery;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Product\Gallery\Processor;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\FunctionalTestingFramework\Config\FileResolver\Root;
use Magento\Setup\Exception;
use Psr\Log\LoggerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use const Yandex\Allure\Adapter\ROOT_DIRECTORY;

class Process
{

    /**
     * @var Data
     */
    private $data;
    /**
     * @var ImagenRepository
     */
    private $imagenRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var ReadHandler
     */
    private $readHandler;
    /**
     * @var Gallery
     */
    private $gallery;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var OutputInterface
     */
    private $output = null;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var Imagen
     */
    private $imagen;
    /**
     * @var DirectoryList
     */
    private $directoryList;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    private $mediaDirectory;
    /**
     * @var File
     */
    private $_file;

    /**
     * Process constructor.
     * @param DirectoryList $directoryList
     * @param Filesystem $filesystem
     * @param Data $data
     * @param ImagenRepository $imagenRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param ProductRepositoryInterface $productRepository
     * @param ReadHandler $readHandler
     * @param Gallery $gallery
     * @param LoggerInterface $logger
     * @param Imagen $imagen
     * @param Processor $imageProcessor
     * @param CollectionFactory $productCollectionFactory
     * @param File $file
     * @throws FileSystemException
     */
    public function __construct(
        DirectoryList $directoryList,
        Filesystem $filesystem,
        Data $data,
        ImagenRepository $imagenRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        ProductRepositoryInterface $productRepository,
        ReadHandler $readHandler,
        Gallery $gallery,
        LoggerInterface $logger,
        Imagen $imagen,
        Processor $imageProcessor,
        CollectionFactory $productCollectionFactory,
        File $file
    ) {
        $this->data = $data;
        $this->imagenRepository = $imagenRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->readHandler = $readHandler;
        $this->gallery = $gallery;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->imagen = $imagen;
        $this->directoryList = $directoryList;
        $this->filesystem = $filesystem;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->imageProcessor = $imageProcessor;
        $this->_file = $file;
    }

    /**
     * @return null
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Process the imagen products
     *
     * @throws FileSystemException
     * @throws LocalizedException
     * @throws \Exception
     * @author Carlos Hernan Aguilar Hurado <caguilar@aventi.co>
     * @date 28/04/20
     */
    public function update()
    {
        $sizeMb = 2;
        $sizeMb = ($sizeMb*1024000);
        $pathImages = $this->data->getPathImage();

        if (empty($pathImages)) {
            return true;
        }

        $this->writeIn(__('Path origin the data ') . $pathImages);
        $resume = [
            'total' => 0,
            'completed' => 0 ,
            'noFound' => 0 ,
            'NoProcessing' => 0
        ];

        /**
         * Assumed images are named [sku].[ext]
         */
        $dirs = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathImages));
        $files_array = [];
        foreach ($dirs as $dir) {
            if ($dir->isDir()) {
                continue;
            }
            if (in_array(trim(pathinfo($dir)['extension']), ['jpg', 'png', 'gif'])) {
                $sizeImg = $this->getFileSize(pathinfo($dir)['dirname'] . "/" . pathinfo($dir)['basename']);
                $resume['total'] += 1;
                if ($sizeImg <= $sizeMb) {
                    $imageFileName = trim(pathinfo($dir)['filename']);
                    $imageFileName = explode('_', $imageFileName);
                    $imageBase = trim(pathinfo($dir)['basename']);
                    $index = substr($imageBase, -6);
                    $customSku = substr($imageFileName[0], 0, 8);
                    $collection = $this->productCollectionFactory->create();
                    $collection = $collection->addAttributeToSelect(['id', 'sku'])
                        ->addAttributeToSort('created_at', 'desc')
                        ->addAttributeToFilter('sku', ['like' => $customSku]);
                    foreach ($collection as $item) {
                        $files_array[$item->getSku() . $index] = $this->directoryList->getPath(DirectoryList::ROOT)."/".$dir->getPathName();
                    }
                } else {
                    $resume['NoProcessing'] += 1;
                }
            }
        }
        ksort($files_array);
        $cantPictures = count($files_array);
        $i=0;
        foreach ($files_array as $key => $value) {
            $i++;
            $this->writeIn("Imagen ".$key." No: ".$i." de ".$cantPictures);
            $imageFileName = explode('_', $key);
            $sku = $imageFileName[0];
            try {
                $product = $this->productRepository->get($sku);
                if (substr($imageFileName[1], 0, 1) == "1") {
                    try {
                        $this->deleteProductPictures($sku, "_" . $imageFileName[1]);
                        $product->addImageToMediaGallery($value, [
                            'image',
                            'small_image',
                            'thumbnail'
                        ], false, false);

                        if ($this->_file->isExists($value)) {
                            $this->_file->deleteFile($value);
                        }
                    } catch (NoSuchEntityException | \Exception $e) {
                        $this->writeIn("Error: " . $e->getMessage());
                        $resume['NoProcessing'] += 1;
                    }
                } else {
                    $this->deleteProductPicture($sku, "_" . $imageFileName[1]);
                    $product->addImageToMediaGallery($value, null, false, false);
                    if ($this->_file->isExists($value)) {
                        $this->_file->deleteFile($value);
                    }
                }

                $this->productRepository->save($product);
                $resume['completed'] += 1;
            }
            catch (NoSuchEntityException | \Exception $e) {
                $this->writeIn("Error: " . $e->getMessage());
                $resume['NoProcessing'] += 1;
            }
        }
        $this->resumen(array_values($resume));
        return $resume;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function deleteProductPictures($sku, $separator)
    {
        $separator = (explode('.', $separator))[0];
        $product = $this->productRepository->get($sku);
        $this->readHandler->execute($product);
        $imageName = $sku.$separator;
        // Unset existing images
        $images = $product->getMediaGalleryImages();
        $product->setMediaGalleryEntries([]);
        $this->productRepository->save($product);
        foreach ($images as $image) {
            if (str_contains($image->getData("file"), $imageName)) {
                $this->gallery->deleteGallery($image->getValueId());
                if ($this->_file->isExists($image->getData("path"))) {
                    $this->_file->deleteFile($image->getData("path"));
                }
            }
        }
    }

    public function deleteProductPicture($sku, $separator)
    {
        $separator = (explode('.', $separator))[0];
        $product = $this->productRepository->get($sku);
        $this->readHandler->execute($product);
        $imageName = $sku.$separator;
        // Unset existing images
        $images = $product->getMediaGalleryEntries();

        foreach($images as $image){
            if (str_contains($image->getData("file"), $imageName)) {
                $this->imageProcessor->removeImage($product,$image->getFile());
            }
        }
    }

    public function getFileSize($file)
    {
        $fileSize = $this->mediaDirectory->stat($file)['size'];
        //$readableSize = $this->convertToReadableSize($fileSize);
        return $fileSize;
    }

    public function convertToReadableSize($size)
    {
        $base = log($size) / log(1024);
        $suffix = ["", " KB", " MB", " GB", " TB"];
        $f_base = floor($base);
        return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
    }
    /**
     * Uploads images Configurable Products.
     * @return bool|int[]
     * @throws LocalizedException
     */
    public function updateImgConfigurable()
    {
        $sizeMb = 2;
        $sizeMb = ($sizeMb*1024000);
        $pathImages = $this->data->getPathImage();
        if (empty($pathImages)) {
            return true;
        }

        $this->writeIn(__('Path origin the data ') . $pathImages);
        $resume = [
            'total' => 0,
            'completed' => 0 ,
            'noFound' => 0 ,
            'NoProcessing' => 0
        ];

        $files_array = [];

        $dirs = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathImages));
        foreach ($dirs as $dir) {
            if ($dir->isDir()) {
                continue;
            }

            if (in_array(trim(pathinfo($dir)['extension']), ['jpg', 'png', 'gif'])) {
                $sizeImg = $this->getFileSize(pathinfo($dir)['dirname']."/".pathinfo($dir)['basename']);
                $resume['total'] += 1;
                if ($sizeImg<= $sizeMb) {
                    $imageFileName = explode('_', trim(pathinfo($dir)['filename']));
                    $imageExtension = trim(pathinfo($dir)['extension']);
                    $imageBase = substr($imageFileName[0], 0, 8) . "." . $imageExtension;
                    if ($imageFileName[1] == "1" && !array_key_exists($imageBase, $files_array)) {
                        $files_array[$imageBase] = $dir->getPathName();
                    }
                } else {
                    $resume['NoProcessing'] += 1;
                }
            }
        }
        ksort($files_array);

        foreach ($files_array as $key => $value) {
            if (!$this->imageRegister($key)) {

                $this->writeIn("PASO REGISTER y va con key: $key y value: $value");
                $response = $this->updateConfigurable($key, $value);

                $this->writeIn("PASO response: ".$response);
                if ($response) {
                    $resume['completed'] += 1;
                }
                $resume['noFound'] += 1;
            } else {
                $resume['NoProcessing'] += 1;
            }
        }
        $this->resumen(array_values($resume));
        return $resume;
    }

    /**
     * print data
     *
     * @author Carlos Hernan Aguilar Hurado <caguilar@aventi.co>
     * @date 28/04/20
     * @param $message
     */
    public function writeIn($message)
    {
        $output = $this->getOutput();
        if ($output) {
            $output->writeln($message);
        }
    }
    /**
     * Find the imagen
     *
     * @author Carlos Hernan Aguilar Hurado <caguilar@aventi.co>
     * @date 28/04/20
     * @param $name
     * @return bool
     * @throws LocalizedException
     */
    private function imageRegister($name)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            'image',
            $name,
            'eq'
        )->create();
        $items = $this->imagenRepository->getList($searchCriteria);
        return ($items->getTotalCount() > 0) ? true : false;
    }
    /**
     * Print the resume
     *
     * @author Carlos Hernan Aguilar Hurado <caguilar@aventi.co>
     * @date 28/04/20
     * @param array $data
     */
    private function resumen($data = [])
    {
        $output = $this->getOutput();
        if ($output) {
            $table = new table($output);
            $table
                ->setHeaders(['Total', 'Complete', 'Product no found', 'No processing'])
                ->setRows([$data]);
            $table->render();
        }
    }

    /**
     * @param string $imageFileName
     * @param $dir
     * @return bool
     */
    private function updateConfigurable(string $imageFileName, $dir): bool
    {
        $baseImageFileName = explode('_', $imageFileName);
        $skuConfigurable = (string) substr($baseImageFileName[0], 0, 8);
        try {
            $configurable = $this->productRepository->get($skuConfigurable, true, null, false);
            //$this->writeIn("Paso configurable: ".json_encode($configurable));
            $baseImage  = $configurable->getData('image');
            if ($baseImage) {
                return false;
            } else {
                $configurable->addImageToMediaGallery($dir, [
                    'image',
                    'small_image',
                    'thumbnail'
                ], false, false);
                $this->productRepository->save($configurable);
                $this->imagen->setData(['image' => $imageFileName]);
                $this->imagen->save();
                $this->imagen->setId(null);
                return true;
            }
        } catch (NoSuchEntityException | \Exception $e) {
            $this->writeIn("EERRROORR: ".$e->getMessage());
            $this->logger->debug($e->getMessage());
            return false;
        }
    }
}
