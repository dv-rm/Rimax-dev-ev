<?php

namespace Aventi\CityDropDown\Setup;

use Exception;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Psr\Log\LoggerInterface;

class InstallData implements InstallDataInterface
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
     * InstallData constructor.
     * @param Csv $csv
     * @param DirectoryList $directoryList
     * @param LoggerInterface $logger
     */
    public function __construct(
        Csv $csv,
        DirectoryList $directoryList,
        LoggerInterface $logger
    ) {
        $this->_csv = $csv;
        $this->_directoryList = $directoryList;
        $this->_logger = $logger;
    }

    /**
     * @param $file
     * @return array
     * @throws Exception
     * @throws Exception
     * By Alejandro / Adrián
     */
    public function import($file)
    {
        try {
            if (file_exists($file)) {
                $csvData = $this->_csv->getData($file);
                $cities = [];
                foreach ($csvData as $data) {
                    $cities[$data[1]][] = [
                        $data[0], $data[2], $data[3]
                    ];
                }
                return $cities;
            }
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        try {
            $folder = $this->_directoryList->getPath('app') . '/' . 'code' . '/' . '/' . 'Aventi' . '/' . '/' . 'CityDropDown' . '/' . 'Setup' . '/' . 'data.csv';
            $departments = $this->import($folder);
        } catch (Exception $e) {
            $this->_logger->error($e->getMessage());
        }

        $cityDropdown=$setup->getTable('aventi_citydropdown_city');
        $magentoRegions = $setup->getTable('directory_country_region');
        foreach ($departments as $keyDepart =>  $department) {
            $sql = 'SELECT region_id FROM ' . $magentoRegions . ' WHERE country_id="CO" and code like "' . $keyDepart . '" ';
            $regionId =$setup->getConnection()->fetchOne($sql);
            $cities = $department;
            foreach ($cities as $city) {
                $createCities = ['name' => $city[0], 'region_id' => $regionId, 'postalCode' => $city[1], 'region_code' => $city[2]];
                $setup->getConnection()->insert($cityDropdown, $createCities);
            }
        }
    }
}
