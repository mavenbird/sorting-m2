<?php
/**
 * Mavenbird Technologies Private Limited
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mavenbird.com/Mavenbird-Module-License.txt
 *
 * =================================================================
 *
 * @category   Mavenbird
 * @package    Mavenbird_Sorting
 * @author     Mavenbird Team
 * @copyright  Copyright (c) 2018-2024 Mavenbird Technologies Private Limited ( http://mavenbird.com )
 * @license    http://mavenbird.com/Mavenbird-Module-License.txt
 */

namespace Mavenbird\Sorting\Observer\System;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\Config as CatalogConfig;

class ConfigChanged implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private $configWriter;

    /**
     * @var \Magento\Framework\App\Config\ReinitableConfigInterface
     */
    private $reinitableConfig;

    /**
     * @var \Mavenbird\Sorting\Helper\Data
     */
    private $helper;

    /**
     * @var CatalogConfig
     */
    private $catalogConfig;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Config\ReinitableConfigInterface $reinitableConfig
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Mavenbird\Sorting\Helper\Data $helper
     * @param CatalogConfig $catalogConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ReinitableConfigInterface $reinitableConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Mavenbird\Sorting\Helper\Data $helper,
        CatalogConfig $catalogConfig
    ) {
        $this->reinitableConfig = $reinitableConfig;
        $this->configWriter = $configWriter;
        $this->helper = $helper;
        $this->catalogConfig = $catalogConfig;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $name = $observer->getEvent()->getName();
        $store = (int)$observer->getEvent()->getStore() ?: null;
        $defaultSortings = $this->helper->getCategorySorting($store);
        $moduleValue = array_shift($defaultSortings);
        $catalogValue = $this->catalogConfig->getProductListDefaultSortBy($store);
        if ($catalogValue != $moduleValue) {
            switch ($name) {
                case 'admin_system_config_changed_section_catalog':
                    $this->saveMavenbirdValue($catalogValue, $store);
                    break;
                case 'admin_system_config_changed_section_sorting':
                    $this->saveCatalogValue($moduleValue, $store);
                    break;
            }
        }
    }

    /**
     * SaveMavenbirdValue
     *
     * @param [type] $value
     * @param [type] $store
     * @return void
     */
    private function saveMavenbirdValue($value, $store)
    {
        $this->saveConfig('sorting/default_sorting/category_1', $value, $store);
    }

    /**
     * SaveCatalogValue
     *
     * @param [type] $value
     * @param [type] $store
     * @return void
     */
    private function saveCatalogValue($value, $store)
    {
        $this->saveConfig(CatalogConfig::XML_PATH_LIST_DEFAULT_SORT_BY, $value, $store);
    }

    /**
     * SaveConfig
     *
     * @param [type] $path
     * @param [type] $value
     * @param [type] $store
     * @return void
     */
    private function saveConfig($path, $value, $store)
    {
        if ($store) {
            $this->configWriter->save($path, $value, \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $store);
        } else {
            $this->configWriter->save($path, $value);
        }
        $this->reinitableConfig->reinit();

        return $this;
    }
}
