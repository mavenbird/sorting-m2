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

namespace Mavenbird\Sorting\Plugin\Elasticsearch\Model\Adapter\FieldMapper\Product;

use Mavenbird\Sorting\Helper\Data;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\AttributeAdapter as NativeAttributeAdapter;

class AttributeAdapter
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
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * AfterIsSortable
     *
     * @param [type] $subject
     * @param [type] $result
     * @return void
     */
    public function afterIsSortable($subject, $result)
    {
        if ($this->helper->isElasticSort()
            && in_array(
                $subject->getAttributeCode(),
                $this->helper->getMavenbirdAttributesCodes()
            )
        ) {
            $result = true;
        }

        return $result;
    }
}
