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

namespace Mavenbird\Sorting\Model\Elasticsearch\Adapter\DataMapper;

use Mavenbird\Sorting\Helper\Data;
use Mavenbird\Sorting\Model\Elasticsearch\Adapter\IndexedDataMapper;
use Mavenbird\Sorting\Model\ResourceModel\Method\Saving as SavingResource;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Indexer\IndexerRegistry;

class Saving extends IndexedDataMapper
{
    public const FIELD_NAME = 'saving';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Construct
     *
     * @param IndexerRegistry $indexerRegistry
     * @param CollectionFactory $collectionFactory
     * @param SavingResource $resourceMethod
     * @param Data $helper
     */
    public function __construct(
        IndexerRegistry $indexerRegistry,
        CollectionFactory $collectionFactory,
        SavingResource $resourceMethod,
        Data $helper
    ) {
        parent::__construct($indexerRegistry, $resourceMethod, $helper);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * GetIndexerCode
     *
     * @return void
     */
    public function getIndexerCode()
    {
        return false;
    }

    /**
     * ForceLoad
     *
     * @param [type] $storeId
     * @return void
     */
    protected function forceLoad($storeId)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addPriceData();
        $this->resourceMethod->setLimitColumns(true);
        $this->resourceMethod->apply($collection, '');
        return $this->resourceMethod->getConnection()->fetchPairs($collection->getSelect());
    }
}
