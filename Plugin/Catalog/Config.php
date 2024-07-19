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

namespace Mavenbird\Sorting\Plugin\Catalog;

/**
 * Plugin Config
 * plugin name: AddSortingMethods
 * type: \Magento\Catalog\Model\Config
 */
class Config
{
    /**
     * @var \Mavenbird\Sorting\Helper\Data
     */
    private $helper;

    /**
     * @var \Mavenbird\Sorting\Model\MethodProvider
     */
    private $methodProvider;

    /**
     * @var \Mavenbird\Sorting\Model\SortingAdapterFactory
     */
    private $adapterFactory;

    /**
     * @var array
     */
    private $correctSortOrder;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * Construct
     *
     * @param \Mavenbird\Sorting\Helper\Data $helper
     * @param \Mavenbird\Sorting\Model\MethodProvider $methodProvider
     * @param \Mavenbird\Sorting\Model\SortingAdapterFactory $adapterFactory
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Mavenbird\Sorting\Helper\Data $helper,
        \Mavenbird\Sorting\Model\MethodProvider $methodProvider,
        \Mavenbird\Sorting\Model\SortingAdapterFactory $adapterFactory,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->helper = $helper;
        $this->methodProvider = $methodProvider;
        $this->adapterFactory = $adapterFactory;
        $this->correctSortOrder = array_keys($this->helper->getSortOrder());
        $this->layout = $layout;
    }

    /**
     * AfterGetAttributesUsedForSortBy
     *
     * @param [type] $subject
     * @param [type] $options
     * @return void
     */
    public function afterGetAttributesUsedForSortBy($subject, $options)
    {
        foreach ($options as $key => $option) {
            if ($this->helper->isMethodDisabled($key)) {
                unset($options[$key]);
            }
        }

        return $this->addNewOptions($options);
    }

    /**
     * AddNewOptions
     *
     * @param [type] $options
     * @return void
     */
    public function addNewOptions($options)
    {
        $methods = $this->methodProvider->getMethods();

        foreach ($methods as $methodObject) {
            $code = $methodObject->getMethodCode();
            if (!$this->helper->isMethodDisabled($code) && !isset($options[$code])) {
                $options[$code] = $this->adapterFactory->create(['methodModel' => $methodObject]);
            }
        }

        return $options;
    }

    /**
     * AfterGetAttributeUsedForSortByArray
     *
     * @param [type] $subject
     * @param [type] $options
     * @return void
     */
    public function afterGetAttributeUsedForSortByArray($subject, $options)
    {
        if ($this->helper->isMethodDisabled('position')) {
            unset($options['position']);
        }

        $options = $this->sortOptions($options);

        if (count($options) == 0 && !$this->layout->getBlock('search.result')) {
            $options[] = '';
        }

        return $options;
    }

    /**
     * SortOptions
     *
     * @param array $options
     * @return void
     */
    private function sortOptions($options = [])
    {
        uksort($options, [$this, "sortingRule"]);

        return $options;
    }

    /**
     * SortingRule
     *
     * @param [type] $first
     * @param [type] $second
     * @return void
     */
    private function sortingRule($first, $second)
    {
        $firstValue = array_search($first, $this->correctSortOrder);
        $secondValue = array_search($second, $this->correctSortOrder);
        if ($firstValue < $secondValue) {
            return -1;
        } elseif ($firstValue == $secondValue) {
            return 0;
        } else {
            return 1;
        }
    }
}
