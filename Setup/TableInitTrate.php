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

namespace Mavenbird\Sorting\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

/*
 * This Trait is called from UpgradeSchema is module has installed and moduleVersion < 1.2
 * Or from InstallSchema
 */
trait TableInitTrate
{
    /**
     * CreateBestsellers
     *
     * @param SchemaSetupInterface $installer
     * @param [type] $bestsellersTable
     * @return void
     */
    private function createBestsellers(SchemaSetupInterface $installer, $bestsellersTable)
    {
        $table = $installer->getConnection()
            ->newTable($bestsellersTable)
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )->addColumn(
                'qty_ordered',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0.0000'],
                'Bestsellers'
            )->addIndex(
                $installer->getIdxName(
                    'mavenbird_sorting_bestsellers',
                    ['product_id', 'store_id']
                ),
                ['product_id', 'store_id']
            )->setComment('Mavenbird Sorting Bestsellers');

        $installer->getConnection()->createTable($table);
    }

    /**
     * CreateMostViewed
     *
     * @param SchemaSetupInterface $installer
     * @param [type] $mostViewedTable
     * @return void
     */
    private function createMostViewed(SchemaSetupInterface $installer, $mostViewedTable)
    {
        $table = $installer->getConnection()
            ->newTable($mostViewedTable)
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )->addColumn(
                'views_num',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Most Viewed'
            )->addIndex(
                $installer->getIdxName(
                    'mavenbird_sorting_most_viewed',
                    ['product_id', 'store_id']
                ),
                ['product_id', 'store_id']
            )->setComment('Mavenbird Sorting Most Viewed');
        $installer->getConnection()->createTable($table);
    }

    /**
     * CreateWished
     *
     * @param SchemaSetupInterface $installer
     * @param [type] $wishedTable
     * @return void
     */
    private function createWished(SchemaSetupInterface $installer, $wishedTable)
    {
        $table = $installer->getConnection()
            ->newTable($wishedTable)
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )->addColumn(
                'wished',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Wished'
            )->addIndex(
                $installer->getIdxName(
                    'mavenbird_sorting_wished',
                    ['product_id', 'store_id']
                ),
                ['product_id', 'store_id']
            )->setComment('Mavenbird Sorting Wished');

        $installer->getConnection()->createTable($table);
    }
}
