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

namespace Mavenbird\Sorting\Observer;

use Mavenbird\Sorting\Model\Indexer\Wished\WishedProcessor;
use Magento\Framework\Event\ObserverInterface;

/**
 * observer name: wished_index_invalidate
 * event names:
 *     wishlist_add_product
 */
class WishedIndexInvalidator implements ObserverInterface
{
    /**
     * @var WishedProcessor
     */
    private $indexProcessor;

    /**
     * Construct
     *
     * @param WishedProcessor $indexProcessor
     */
    public function __construct(WishedProcessor $indexProcessor)
    {
        $this->indexProcessor = $indexProcessor;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->indexProcessor->markIndexerAsInvalid();
    }
}
