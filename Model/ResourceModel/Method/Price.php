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

namespace Mavenbird\Sorting\Model\ResourceModel\Method;

class Price extends AbstractMethod
{
    /**
     * Apply
     *
     * @param [type] $collection
     * @param [type] $direction
     * @return void
     */
    public function apply($collection, $direction)
    {
        return $this;
    }

    /**
     * GetAlias
     *
     * @return void
     */
    public function getAlias()
    {
        return 'price';
    }

    /**
     * GetIndexedValues
     *
     * @param [type] $storeId
     * @return void
     */
    public function getIndexedValues($storeId)
    {
        return [];
    }
}
