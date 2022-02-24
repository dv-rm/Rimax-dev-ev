<?php

namespace Aventi\CityDropDown\Setup;

use Exception;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Psr\Log\LoggerInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Csv
     */
    private $_csv;
    /**
     * @var DirectoryList
     */
    private $_directoryList;
    /**
     * @var LoggerInterface
     */
    private $_logger;
    /**
     * @var InstallData
     */
    private $_installData;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * UpgradeData constructor.
     * @param Csv $csv
     * @param DirectoryList $directoryList
     * @param LoggerInterface $logger
     * @param InstallData $installData
     */
    public function __construct(
        Csv $csv,
        DirectoryList $directoryList,
        LoggerInterface $logger,
        InstallData $installData,
        RegionFactory $regionFactory
    ) {
        $this->_csv = $csv;
        $this->_directoryList = $directoryList;
        $this->_logger = $logger;
        $this->_installData = $installData;
        $this->regionFactory = $regionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), "1.0.0", "<")) {
            try {
                $folder = $this->_directoryList->getPath('Aventi') . '/' . 'CityDropDown' . '/' . 'Setup' . '/' . 'data.csv';
                $this->_installData->import($folder);
            } catch (Exception $e) {
                $this->_logger->error($e->getMessage());
            }
        }

        if (version_compare($context->getVersion(), "1.0.0", ">")) {
            try {
                $regionModel = $this->regionFactory->create()->loadByCode("CO-DC", "CO");
                if (!$regionModel->getId()) {
                    $region = $this->regionFactory->create();
                    $region->setCountryId("CO");
                    $region->setCode("CO-DC");
                    $region->setDefaultName("Bogotá D.C.");
                    $region->save();
                }
            } catch (Exception $e) {
                $this->_logger->error($e->getMessage());
            }
        }
    }
}
