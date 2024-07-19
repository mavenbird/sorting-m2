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

namespace Mavenbird\Sorting\Model\Indexer;

use Mavenbird\Sorting\Helper\Data;
use Mavenbird\Sorting\Model\Indexer\Bestsellers\BestsellersProcessor;
use Mavenbird\Sorting\Model\Indexer\MostViewed\MostViewedProcessor;
use Mavenbird\Sorting\Model\Indexer\Wished\WishedProcessor;
use Magento\Indexer\Model\Indexer;
use Magento\Indexer\Model\IndexerFactory;

class Summary
{
    /**
     * @var array
     */
    private $indexerIds = [
        BestsellersProcessor::INDEXER_ID,
        MostViewedProcessor::INDEXER_ID,
        WishedProcessor::INDEXER_ID
    ];

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var \Mavenbird\Sorting\Model\MethodProvider
     */
    private $methodProvider;

    /**
     * @var IndexerFactory
     */
    private $indexerFactory;

    /**
     * Construct
     *
     * @param \Mavenbird\Sorting\Helper\Data $helper
     * @param \Mavenbird\Sorting\Model\MethodProvider $methodProvider
     * @param IndexerFactory $indexerFactory
     */
    public function __construct(
        \Mavenbird\Sorting\Helper\Data $helper,
        \Mavenbird\Sorting\Model\MethodProvider $methodProvider,
        IndexerFactory $indexerFactory
    ) {
        $this->helper = $helper;
        $this->methodProvider = $methodProvider;
        $this->indexerFactory = $indexerFactory;
    }

    /**
     * ReindexAll
     *
     * @return void
     */
    public function reindexAll()
    {
        foreach ($this->indexerIds as $indexerId) {
            // do full reindex if method not disabled
            $this->loadIndexer($indexerId)->reindexAll();
        }
    }

    /**
     * LoadIndexer
     *
     * @param [type] $indexerId
     * @return void
     */
    private function loadIndexer($indexerId)
    {
        return $this->indexerFactory->create()
            ->load($indexerId);
    }
}
