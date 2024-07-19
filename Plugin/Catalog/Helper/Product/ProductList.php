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

namespace Mavenbird\Sorting\Plugin\Catalog\Helper\Product;

use Mavenbird\Sorting\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Helper\Product\ProductList as Subject;

class ProductList
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var array
     */
    private $searchModules = [
        'catalogsearch'
    ];

    /**
     * Construct
     *
     * @param Data $helper
     * @param RequestInterface $request
     */
    public function __construct(
        Data $helper,
        RequestInterface $request
    ) {
        $this->helper = $helper;
        $this->request = $request;
    }

    /**
     * AfterGetDefaultSortField
     *
     * @param Subject $subject
     * @param [type] $sortBy
     * @return void
     */
    public function afterGetDefaultSortField(Subject $subject, $sortBy)
    {
        if (in_array($this->request->getModuleName(), $this->searchModules)) {
            $searchSortings = $this->helper->getSearchSorting();
            // getting first default sorting
            $sortBy = array_shift($searchSortings);
        }

        return $sortBy;
    }
}
