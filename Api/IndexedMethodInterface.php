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
interface IndexedMethodInterface extends MethodInterface
{
    /**
     * Returns the name of the index table used for sorting.
     *
     * @return string The name of the index table.
     */
    public function getIndexTableName();

    /**
     * Reindex
     *
     * @return void
     */
    public function reindex();

    /**
     * DoReindex
     *
     * @return void
     */
    public function doReindex();

    /**
     * GetSortingColumnName
     *
     * @return void
     */
    public function getSortingColumnName();
}
