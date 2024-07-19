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

namespace Mavenbird\Sorting\Plugin\CatalogInventory\Model\Source;

class Stock
{

    /**
     * AroundAddValueSortToCollection
     *
     * @param [type] $subject
     * @param [type] $proceed
     * @param [type] $collection
     * @param [type] $dir
     * @return void
     */
    public function aroundAddValueSortToCollection(
        $subject,
        $proceed,
        $collection,
        $dir
    ) {
        // fix magento bug. getting full table name
        $collection->getSelect()->joinLeft(
            ['stock_item_table' => $collection->getResource()->getTable('cataloginventory_stock_item')],
            "e.entity_id=stock_item_table.product_id",
            []
        );
        $collection->getSelect()->order("stock_item_table.qty $dir");

        return $this;
    }
}
