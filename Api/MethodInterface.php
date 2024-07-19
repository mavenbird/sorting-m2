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

namespace Mavenbird\Sorting\Api;

/**
 * Interface IndexedMethodInterface
 * @api
 */
interface MethodInterface
{
    /**
     * Apply sorting method to collection
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param string $direction
     *
     * @return $this
     */
    public function apply($collection, $direction);

    /**
     * Returns Sorting method Code for using in code
     *
     * @return string
     */
    public function getMethodCode();

    /**
     * Returns Sorting method Name for using like Method label
     *
     * @return string
     */
    public function getMethodName();

    /**
     * Get method label for store
     *
     * @param null|int|\Magento\Store\Model\Store $store
     *
     * @return string
     */
    public function getMethodLabel($store = null);
}
