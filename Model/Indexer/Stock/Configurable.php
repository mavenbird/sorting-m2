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

namespace Mavenbird\Sorting\Model\Indexer\Stock;

use Magento\ConfigurableProduct\Model\ResourceModel\Indexer\Stock\Configurable as NativeIndexer;
use Zend_Db_Expr;
use Magento\Framework\DB\Select;

class Configurable extends NativeIndexer
{

    /**
     * GetStockStatusSelect
     *
     * @param [type] $entityIds
     * @param boolean $usePrimaryTable
     * @return void
     */
    protected function _getStockStatusSelect($entityIds = null, $usePrimaryTable = false)
    {
        $select = parent::_getStockStatusSelect($entityIds, $usePrimaryTable);
        $this->calculateDependOnSimples($select);

        return $select;
    }

    /**
     * CalculateDependOnSimples
     *
     * @param [type] $select
     * @return void
     */
    private function calculateDependOnSimples($select)
    {
        $columns = $select->getPart(Select::COLUMNS);
        foreach ($columns as &$column) {
            if (isset($column[2]) && $column[2] == 'qty') {
                $column[1] = new Zend_Db_Expr('SUM(IF(i.stock_status > 0, i.qty, 0))');
            }
            // determine stock status based on simples
//            if (isset($column[2]) && $column[2] == 'status') {
//                $column[1] = new Zend_Db_Expr('MAX(i.stock_status = 1)');
//            }
        }
        $select->setPart(Select::COLUMNS, $columns);
    }
}
