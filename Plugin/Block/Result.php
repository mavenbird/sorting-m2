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

namespace Mavenbird\Sorting\Plugin\Block;

use Mavenbird\Sorting\Helper\Data;
use Magento\CatalogSearch\Block\Result as Subject;
use Magento\Framework\Registry;

class Result
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * Construct
     *
     * @param Data $helper
     * @param Registry $registry
     */
    public function __construct(Data $helper, Registry $registry)
    {
        $this->helper = $helper;
        $this->registry = $registry;
    }

    /**
     * AfterSetListOrders
     *
     * @param Subject $result
     * @return void
     */
    public function afterSetListOrders(Subject $result)
    {
        $searchSortings = $this->helper->getSearchSorting();
        // getting first default sorting
        $sortBy = array_shift($searchSortings);
        $result->getListBlock()->setDefaultSortBy(
            $sortBy
        );
        $this->registry->unregister(Data::SEARCH_SORTING);
        $this->registry->register(Data::SEARCH_SORTING, true);

        return $this;
    }
}
