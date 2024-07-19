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

namespace Mavenbird\Sorting\Plugin\Catalog\Model\ResourceModel\Product\Attribute;

use Mavenbird\Sorting\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;
use Magento\Framework\DB\Select;

class Collection
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * Construct
     *
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * AfterAddToIndexFilter
     *
     * @param [type] $subject
     * @param [type] $result
     * @return void
     */
    public function afterAddToIndexFilter($subject, $result)
    {
        if ($this->helper->isElasticSort()) {
            $parts = $result->getSelect()->getPart(Select::WHERE);
            $conditions = array_pop($parts);
            $newCondition = $result->getConnection()->quoteInto(
                'main_table.attribute_code IN (?)',
                $this->helper->getMavenbirdAttributesCodes()
            );
            $conditions = str_replace(
                'additional_table.is_searchable',
                $newCondition . ' OR additional_table.is_searchable',
                $conditions
            );
            $parts[] = $conditions;
            $result->getSelect()->setPart(Select::WHERE, $parts);
        }

        return $result;
    }
}
