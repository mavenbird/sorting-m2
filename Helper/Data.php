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

namespace Mavenbird\Sorting\Helper;

use Mavenbird\Core\Model\Serializer;
use Magento\CatalogInventory\Model\Configuration;
use Magento\CatalogSearch\Model\ResourceModel\EngineInterface;
use Magento\Framework\Registry;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const CONFIG_SORT_ORDER = 'general/sort_order';

    public const SEARCH_SORTING = 'sorting_search';

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var \Mavenbird\Core\Model\MagentoVersion
     */
    private $magentoVersion;
    
    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * Gonstruct
     *
     * @param \Mavenbird\Core\Model\Serializer $serializer
     * @param Registry $registry
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Mavenbird\Core\Model\MagentoVersion $magentoVersion
     */
    public function __construct(
        \Mavenbird\Core\Model\Serializer $serializer,
        Registry $registry,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Mavenbird\Core\Model\MagentoVersion $magentoVersion
    ) {
        parent::__construct($context);
        $this->serializer = $serializer;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->magentoVersion = $magentoVersion;
    }

    /**
     * GetScopeValue
     *
     * @param [type] $path
     * @param [type] $store
     * @return void
     */
    public function getScopeValue($path, $store = null)
    {
        return $this->scopeConfig->getValue(
            'sorting/' . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * IsMethodDisabled
     *
     * @param [type] $methodCode
     * @return boolean
     */
    public function isMethodDisabled($methodCode)
    {
        $result = false;
        if (!$this->registry->registry('sorting_all_attributes')) {
            $disabledMethods = $this->getScopeValue('general/disable_methods');
            if ($disabledMethods && !empty($disabledMethods)) {
                $disabledMethods = explode(',', $disabledMethods);
                foreach ($disabledMethods as $disabledCode) {
                    if (trim($disabledCode) == $methodCode) {
                        $result = true;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * GetSearchSorting
     *
     * @return void
     */
    public function getSearchSorting()
    {
        $defaultSorting = [];
        foreach (['search_1', 'search_2', 'search_3'] as $path) {
            if ($sort = $this->getScopeValue('default_sorting/' . $path)) {
                $defaultSorting[] = $sort;
            }
        }

        return $defaultSorting;
    }

    /**
     * IsYotpoEnabled
     *
     * @return boolean
     */
    public function isYotpoEnabled()
    {
        return $this->getScopeValue('rating_summary/yotpo')
            && $this->_moduleManager->isEnabled('Mavenbird_Yotpo')
            && $this->_moduleManager->isEnabled('Yotpo_Yotpo');
    }

    /**
     * GetQtyOutStock
     *
     * @return void
     */
    public function getQtyOutStock()
    {
        return (int)$this->scopeConfig->getValue(Configuration::XML_PATH_MIN_QTY);
    }

    /**
     * GetSortOrder
     *
     * @return void
     */
    public function getSortOrder()
    {
        $value = $this->getScopeValue(self::CONFIG_SORT_ORDER);
        if ($value) {
            $value = $this->serializer->unserialize($value);
        }
        if (!$value) {
            $value = [];
        }

        return $value;
    }

    /**
     * GetCategorySorting
     *
     * @param [type] $store
     * @return void
     */
    public function getCategorySorting($store = null)
    {
        $defaultSorting = [];
        foreach (['category_1', 'category_2', 'category_3'] as $path) {
            if ($sort = $this->getScopeValue('default_sorting/' . $path, $store)) {
                $defaultSorting[] = $sort;
            }
        }

        return $defaultSorting;
    }

    /**
     * IsElasticSort
     *
     * @return boolean
     */
    public function isElasticSort()
    {
        return version_compare($this->magentoVersion->get(), '2.3.2', '>=')
            && strpos($this->scopeConfig->getValue(EngineInterface::CONFIG_ENGINE_PATH), 'elast') !== false
            && $this->storeManager->getStore()->getId();
    }

    /**
     * GetMavenbirdAttributesCodes
     *
     * @return void
     */
    public function getMavenbirdAttributesCodes()
    {
        $result = [
            'created_at',
            $this->getScopeValue('bestsellers/best_attr'),
            $this->getScopeValue('most_viewed/viewed_attr'),
            $this->getScopeValue('new/new_attr')
        ];

        return array_filter($result);
    }
}
