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

use Magento\Catalog\Model\Config;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Registry;

class ListDisabled implements OptionSourceInterface
{
    /**
     * @var Config
     */
    private $catalogConfig;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * Construct
     *
     * @param Config $catalogConfig
     * @param Registry $registry
     */
    public function __construct(Config $catalogConfig, Registry $registry)
    {
        $this->catalogConfig = $catalogConfig;
        $this->registry = $registry;
    }

    /**
     * ToOptionArray
     *
     * @return void
     */
    public function toOptionArray()
    {
        $options = [];
        $this->registry->unregister('sorting_all_attributes');
        $this->registry->register('sorting_all_attributes', true);
        $allAttributes = $this->catalogConfig->getAttributeUsedForSortByArray();
        $this->registry->unregister('sorting_all_attributes');
        foreach ($allAttributes as $code => $label) {
            $options[] = [
                'label' => $label,
                'value' => $code
            ];
        }

        return $options;
    }
}
