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

class Newest extends AbstractMethod
{
    public function getSortingColumnName()
    {
        $attributeCode = $this->helper->getScopeValue('new/new_attr');
        if ($attributeCode) {
            return $attributeCode;
        }

        return 'created_at';
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->getSortingColumnName();
    }

    /**
     * {@inheritdoc}
     */
    public function apply($collection, $direction)
    {
        return $this;
    }

    public function getIndexedValues($storeId)
    {
        return [];
    }
}
