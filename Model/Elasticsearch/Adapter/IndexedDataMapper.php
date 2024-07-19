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

namespace Mavenbird\Sorting\Model\Elasticsearch\Adapter;

use Magento\Framework\Indexer\IndexerRegistry;
use Mavenbird\Sorting\Model\ResourceModel\Method\AbstractMethod;
use Mavenbird\Sorting\Helper\Data;

/**
 * Class IndexedDataMapper
 */
abstract class IndexedDataMapper implements DataMapperInterface
{
    public const DEFAULT_VALUE = 0;

    /**
     * @var AbstractMethod
     */
    protected $resourceMethod;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * Construct
     *
     * @param IndexerRegistry $indexerRegistry
     * @param AbstractMethod $resourceMethod
     * @param Data $helper
     */
    public function __construct(
        IndexerRegistry $indexerRegistry,
        AbstractMethod $resourceMethod,
        Data $helper
    ) {
        $this->resourceMethod = $resourceMethod;
        $this->helper = $helper;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * GetIndexerCode
     *
     * @return void
     */
    abstract public function getIndexerCode();

    /**
     * LoadValuesArray
     *
     * @param [type] $storeId
     * @return void
     */
    protected function loadValuesArray($storeId)
    {
        if (!isset($this->values[$storeId])) {
            $this->values[$storeId] = $this->forceLoad($storeId);
        }
    }

    /**
     * ForceLoad
     *
     * @param [type] $storeId
     * @return void
     */
    protected function forceLoad($storeId)
    {
        try {
            $indexer = $this->indexerRegistry->get($this->getIndexerCode());
            $indexer->reindexAll();
        } catch (\InvalidArgumentException $e) {
            ;//No action required
        }

        return $this->resourceMethod->getIndexedValues($storeId);
    }

    /**
     * IsAllowed
     *
     * @return boolean
     */
    public function isAllowed()
    {
        return !$this->helper->isMethodDisabled($this->resourceMethod->getMethodCode());
    }

    /**
     * Map
     *
     * @param [type] $entityId
     * @param array $entityIndexData
     * @param [type] $storeId
     * @param array $context
     * @return void
     */
    public function map($entityId, array $entityIndexData, $storeId, $context = [])
    {
        $this->loadValuesArray($storeId);
        $value = isset($this->values[$storeId][$entityId]) ? $this->values[$storeId][$entityId] : self::DEFAULT_VALUE;

        return [static::FIELD_NAME => $value];
    }
}
