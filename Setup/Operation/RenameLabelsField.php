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

namespace Mavenbird\Sorting\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;

class RenameLabelsField
{
    /**
     * Execute
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $updateData = [];
        $connection = $setup->getConnection();
        $tableName = $setup->getTable('core_config_data');

        $select = $setup->getConnection()->select()
            ->from($tableName, ['path', 'value', 'scope', 'scope_id'])
            ->where('path = ?', 'sorting/biggest_saving/label');

        $rows = $connection->fetchAll($select);
        foreach ($rows as $row) {
            $updateData[] = [
                'value' => $row['value'],
                'path'  => 'sorting/saving/label',
                'scope' => $row['scope'],
                'scope_id' => $row['scope_id']
            ];
        }

        if (!empty($updateData)) {
            $connection->insertOnDuplicate($tableName, $updateData);
        }
    }
}
