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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    use TableInitTrate;

    /**
     * Install
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $bestsellersTable = $setup->getTable('mavenbird_sorting_bestsellers');
        $mostViewedTable = $setup->getTable('mavenbird_sorting_most_viewed');
        $wishedTable = $setup->getTable('mavenbird_sorting_wished');

        /**
         * Create table 'mavenbird_sorting_bestsellers'
         */
        $this->createBestsellers($setup, $bestsellersTable);

        /**
         * Create table 'mavenbird_sorting_most_viewed'
         */
        $this->createMostViewed($setup, $mostViewedTable);

        /**
         * Create table 'mavenbird_sorting_wished'
         */
        $this->createWished($setup, $wishedTable);

        $setup->endSetup();
    }
}
