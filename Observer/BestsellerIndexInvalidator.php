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

use Mavenbird\Sorting\Model\Indexer\Bestsellers\BestsellersProcessor;
use Magento\Framework\Event\ObserverInterface;

/**
 * observer name: bestsellers_index_invalidate
 * event names:
 *     sales_order_place_after
 *     order_cancel_after
 *     sales_order_state_change_before
 */
class BestsellerIndexInvalidator implements ObserverInterface
{
    /**
     * @var BestsellersProcessor
     */
    private $indexProcessor;

    /**
     * Construct
     *
     * @param BestsellersProcessor $indexProcessor
     */
    public function __construct(BestsellersProcessor $indexProcessor)
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
