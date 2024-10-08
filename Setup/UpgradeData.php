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

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Upgrade Data script
 * @codingStandardsIgnoreFile
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    private $resourceConfig;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface  $resourceConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->resourceConfig = $resourceConfig;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($context->getVersion() && version_compare($context->getVersion(), '2.0.0', '<')) {

            $path = 'sorting/general/desc_attributes';
            $connection = $this->resourceConfig->getConnection();

            $select = $connection->select()->from(
                $this->resourceConfig->getMainTable()
            )->where(
                'path = ?',
                $path
            );

            $rowSet = $connection->fetchAll($select);
            $defaultDesc = 'bestsellers,rating_summary,reviews_count,most_viewed,wished,created_at,saving';

            if (count($rowSet)) {
                $idName = $this->resourceConfig->getIdFieldName();
                foreach ($rowSet as $row) {
                    $value = '';
                    if ($row['value']) {
                        // prepare old values
                        $value = ',' .  str_replace(' ', '', $row['value']);
                    }
                    $row['value'] = $defaultDesc . $value;
                    $whereCondition = [$idName . '=?' => $row[$idName]];
                    $connection->update($this->resourceConfig->getMainTable(), $row, $whereCondition);
                }
            }
        }

        $setup->endSetup();
    }
}
