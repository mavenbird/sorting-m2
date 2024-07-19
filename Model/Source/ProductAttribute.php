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

class ProductAttribute implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Option
     *
     * @var [type]
     */
    private $options;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * Construct
     *
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    /**
     * ToOptionArray
     *
     * @return void
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [['value' => '', 'label' => ' ']];
            $attributes = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)
                ->getAttributeCollection()
                ->addFieldToFilter('frontend_input', ['nin' => ['multiselect', 'gallery', 'textarea']])
                ->addFieldToFilter('used_in_product_listing', 1)
                ->getItems();

            foreach ($attributes as $item) {
                $this->options[] = [
                    'value' => $item->getAttributeCode(),
                    'label' => __($item->getFrontendLabel()),
                ];
            }
        }

        return $this->options;
    }
}
