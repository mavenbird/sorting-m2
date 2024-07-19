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

namespace Mavenbird\Sorting\Model\Source;

/**
 * Class Stock
 */
abstract class AbstractShowLast implements \Magento\Framework\Data\OptionSourceInterface
{
    public const DISABLED = 0;

    public const SHOW_LAST = 1;

    public const SHOW_LAST_FOR_CATALOG = 2;

    /**
     * ToOptionArray
     *
     * @return void
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::DISABLED,
                'label' => __('No')
            ],
            [
                'value' => self::SHOW_LAST,
                'label' => __('Yes')
            ],
            [
                'value' => self::SHOW_LAST_FOR_CATALOG,
                'label' => __('Yes for Catalog, No for Search')
            ]
        ];

        return $options;
    }
}
