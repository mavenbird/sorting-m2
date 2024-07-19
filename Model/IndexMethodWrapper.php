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

namespace Mavenbird\Sorting\Model;

use Mavenbird\Sorting\Api\IndexMethodWrapperInterface;
use Mavenbird\Sorting\Api\IndexedMethodInterface;
use Mavenbird\Sorting\Model\Indexer\AbstractIndexer;

/**
 * This Class used for DI VirtualType
 */
class IndexMethodWrapper implements IndexMethodWrapperInterface
{
    /**
     * @var IndexedMethodInterface
     */
    private $source;

    /**
     * @var AbstractIndexer
     */
    private $indexer;

    /**
     * Construct
     *
     * @param IndexedMethodInterface $source
     * @param AbstractIndexer $indexer
     */
    public function __construct(
        IndexedMethodInterface $source,
        AbstractIndexer $indexer
    ) {
        $this->source = $source;
        $this->indexer = $indexer;
    }

    /**
     * GetSource
     *
     * @return void
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * GetIndexer
     *
     * @return void
     */
    public function getIndexer()
    {
        return $this->indexer;
    }
}
