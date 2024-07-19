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

namespace Mavenbird\Sorting\Block\Widget;

use Mavenbird\Sorting\Model\Source\SortOrder;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Framework\DB\Select;

class Featured extends ProductsList
{
    public const DEFAULT_COLLECTION_SORT_BY = 'name';
    public const DEFAULT_COLLECTION_ORDER = SortOrder::SORT_ASC;

    /**
     * CreateCollection
     *
     * @return void
     */
    public function createCollection()
    {
        $collection = parent::createCollection();
        $collection->getSelect()->reset(Select::ORDER);
        $collection->setOrder($this->getSortBy(), $this->getSortOrder());
        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $collection]
        );

        return $collection;
    }

    /**
     * GetSortBy
     *
     * @return void
     */
    public function getSortBy()
    {
        if (!$this->hasData('sort_by')) {
            $this->setData('sort_by', self::DEFAULT_COLLECTION_SORT_BY);
        }
        return $this->getData('sort_by');
    }

    /**
     * GetSortOrder
     *
     * @return void
     */
    public function getSortOrder()
    {
        if (!$this->hasData('sorting_sort_order')) {
            $this->setData('sorting_sort_order', self::DEFAULT_COLLECTION_ORDER);
        }
        return $this->getData('sorting_sort_order');
    }
}
