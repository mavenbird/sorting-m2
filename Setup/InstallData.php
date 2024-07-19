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

namespace Mavenbird\Sorting\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Indexer\Model\IndexerFactory;
use Magento\Indexer\Model\Indexer;
use Mavenbird\Sorting\Model\Indexer\Bestsellers\BestsellersProcessor;
use Mavenbird\Sorting\Model\Indexer\MostViewed\MostViewedProcessor;
use Mavenbird\Sorting\Model\Indexer\Wished\WishedProcessor;
use Magento\Framework\App\State;

class InstallData implements InstallDataInterface
{
    /**
     * @var IndexerFactory
     */
    private $indexer;

    /**
     * @var array
     */
    private $indexerIds = [
        BestsellersProcessor::INDEXER_ID,
        MostViewedProcessor::INDEXER_ID,
        WishedProcessor::INDEXER_ID
    ];

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;
    /**
     * @var Operation\UpdateDefaultSearch
     */
    private $defaultSearch;

    /**
     * Construct
     *
     * @param IndexerFactory $indexer
     * @param State $state
     * @param Operation\UpdateDefaultSearch $defaultSearch
     */
    public function __construct(
        IndexerFactory $indexer,
        State $state,
        Operation\UpdateDefaultSearch $defaultSearch
    ) {
        $this->state = $state;
        $this->indexer = $indexer;
        $this->defaultSearch = $defaultSearch;
    }

    /**
     * Install
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->state->emulateAreaCode(
            'adminhtml',
            [$this, 'reindexAll']
        );

        $this->defaultSearch->execute($setup);
    }

    /**
     * ReindexAll
     *
     * @return void
     */
    public function reindexAll()
    {
        foreach ($this->indexerIds as $indexerId) {
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
        return $this->indexer->create()
            ->load($indexerId);
    }
}
