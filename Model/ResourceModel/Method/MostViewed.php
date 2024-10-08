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

class MostViewed extends AbstractIndexMethod
{
    /**
     * {@inheritdoc}
     */
    public function getIndexTableName()
    {
        return 'mavenbird_sorting_most_viewed';
    }

    /**
     * {@inheritdoc}
     */
    public function getSortingColumnName()
    {
        return 'views_num';
    }

    /**
     * {@inheritdoc}
     */
    public function doReindex()
    {
        $select = $this->indexConnection->select();

        $select->group(['source_table.store_id', 'source_table.object_id']);

        $viewsNumExpr = new \Zend_Db_Expr('COUNT(source_table.event_id)');

        $columns = [
            'product_id' => 'source_table.object_id',
            'store_id' => 'source_table.store_id',
            'views_num' => $viewsNumExpr,
        ];

        $select->from(
            ['source_table' => $this->getTable('report_event')],
            $columns
        )->where(
            'source_table.event_type_id = ?',
            \Magento\Reports\Model\Event::EVENT_PRODUCT_VIEW
        );

        $this->addFromDate($select);

        $havingPart = $this->indexConnection->prepareSqlCondition($viewsNumExpr, ['gt' => 0]);
        $select->having($havingPart);

        $select->useStraightJoin();

        $viewedInfo = $this->indexConnection->fetchAll($select);

        if ($viewedInfo) {
            $this->getConnection()->insertArray($this->getMainTable(), array_keys($columns), $viewedInfo);
        }
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     *
     * @return bool
     */
    private function addFromDate($select)
    {
        $period = (int)$this->helper->getScopeValue('most_viewed/viewed_period');
        if ($period) {
            $from = $this->date->date(
                \Magento\Framework\DB\Adapter\Pdo\Mysql::TIMESTAMP_FORMAT,
                $this->date->timestamp() - $period * 24 * 3600
            );
            $select->where('source_table.logged_at >= ?', $from);
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($collection, $direction)
    {
        $attributeCode = $this->helper->getScopeValue('most_viewed/viewed_attr');
        if ($attributeCode) {
            if ($this->helper->isElasticSort()) {
                $collection->addAttributeToSort($attributeCode, $direction);
            } else {
                $collection->addAttributeToSelect($attributeCode);
                $collection->addOrder($attributeCode, $direction);
            }
        }
        return parent::apply($collection, $direction);
    }

    /**
     * @inheritdoc
     */
    public function getIndexedValues($storeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getMainTable(),
            ['product_id', 'value' => 'views_num']
        )->where('store_id = ?', $storeId);

        return $this->getConnection()->fetchPairs($select);
    }
}
