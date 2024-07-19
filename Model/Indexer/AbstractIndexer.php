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

use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;
use Magento\Framework\Indexer\CacheContext;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Registry;

class AbstractIndexer implements IndexerActionInterface, MviewActionInterface
{
    /**
     * @var \Mavenbird\Sorting\Api\IndexedMethodInterface
     */
    private $indexBuilder;

    /**
     * @var \Mavenbird\Sorting\Helper\Data
     */
    private $helper;

    /**
     * @var CacheTypeListInterface
     */
    private $cache;

    /**
     * @var CacheContext
     */
    private $cacheContext;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * Construct
     *
     * @param \Mavenbird\Sorting\Api\IndexedMethodInterface $indexBuilder
     * @param \Mavenbird\Sorting\Helper\Data $helper
     * @param CacheTypeListInterface $cache
     * @param CacheContext $cacheContext
     * @param ManagerInterface $eventManager
     * @param Registry $registry
     */
    public function __construct(
        \Mavenbird\Sorting\Api\IndexedMethodInterface $indexBuilder,
        \Mavenbird\Sorting\Helper\Data $helper,
        CacheTypeListInterface $cache,
        CacheContext $cacheContext,
        ManagerInterface $eventManager,
        Registry $registry
    ) {
        $this->indexBuilder = $indexBuilder;
        $this->helper = $helper;
        $this->cache = $cache;
        $this->cacheContext = $cacheContext;
        $this->eventManager = $eventManager;
        $this->registry = $registry;
    }

    /**
     * Execute
     *
     * @param [type] $ids
     * @return void
     */
    public function execute($ids)
    {
        $this->executeList($ids);
    }

    /**
     * ExecuteFull
     *
     * @return void
     */
    public function executeFull()
    {
        // do full reindex if method is not disabled
        if (!$this->helper->isMethodDisabled($this->indexBuilder->getMethodCode())
            && !$this->registry->registry('reindex_' . $this->indexBuilder->getMethodCode())
        ) {
            $this->indexBuilder->reindex();
            $this->cacheContext->registerTags(
                ['sorted_by_' . $this->indexBuilder->getMethodCode()]
            );
            $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
            $this->registry->register('reindex_' . $this->indexBuilder->getMethodCode(), true);
        }
    }

    /**
     * ExecuteList
     *
     * @param array $ids
     * @return void
     */
    public function executeList(array $ids)
    {
        if (!$ids) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Could not rebuild index for empty products array')
            );
        }
        $this->doExecuteList($ids);
    }

    /**
     * DoExecuteList
     *
     * @param [type] $ids
     * @return void
     */
    protected function doExecuteList($ids)
    {
        $this->executeFull();
    }

    /**
     * DoExecuteRow
     *
     * @param [type] $id
     * @return void
     */
    private function doExecuteRow($id)
    {
        $this->executeFull();
    }

    /**
     * ExecuteRow
     *
     * @param [type] $id
     * @return void
     */
    public function executeRow($id)
    {
        if (!$id) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We can\'t rebuild the index for an undefined product.')
            );
        }
        $this->doExecuteRow($id);
    }
}
