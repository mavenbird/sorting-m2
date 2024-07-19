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

namespace Mavenbird\Sorting\Plugin\Catalog\Product\ProductList;

use Mavenbird\Sorting\Helper\Data;
use Magento\Framework\Registry;

class Toolbar
{
    public const ALWAYS_DESC = [
        'price_desc'
    ];

    public const ALWAYS_ASC = [
        'price_asc'
    ];

    public const RELEVANCE_DIRECTION = 'relevance';

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var \Mavenbird\Sorting\Model\MethodProvider
     */
    private $methodProvider;

    /**
     * @var \Magento\Catalog\Model\Product\ProductList\Toolbar
     */
    private $toolbarModel;

    /**
     * @var \Mavenbird\Sorting\Model\ResourceModel\Method\Image
     */
    private $imageMethod;

    /**
     * @var \Mavenbird\Sorting\Model\ResourceModel\Method\Instock
     */
    private $stockMethod;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * Construct
     *
     * @param Data $helper
     * @param \Mavenbird\Sorting\Model\MethodProvider $methodProvider
     * @param \Magento\Catalog\Model\Product\ProductList\Toolbar $toolbarModel
     * @param \Mavenbird\Sorting\Model\ResourceModel\Method\Image $imageMethod
     * @param \Mavenbird\Sorting\Model\ResourceModel\Method\Instock $stockMethod
     * @param Registry $registry
     */
    public function __construct(
        Data $helper,
        \Mavenbird\Sorting\Model\MethodProvider $methodProvider,
        \Magento\Catalog\Model\Product\ProductList\Toolbar $toolbarModel,
        \Mavenbird\Sorting\Model\ResourceModel\Method\Image $imageMethod,
        \Mavenbird\Sorting\Model\ResourceModel\Method\Instock $stockMethod,
        Registry $registry
    ) {
        $this->helper = $helper;
        $this->methodProvider = $methodProvider;
        $this->toolbarModel = $toolbarModel;
        $this->imageMethod = $imageMethod;
        $this->stockMethod = $stockMethod;
        $this->registry = $registry;
    }

    /**
     * AfterGetCurrentDirection
     *
     * @param [type] $subject
     * @param [type] $dir
     * @return void
     */
    public function afterGetCurrentDirection($subject, $dir)
    {
        $defaultDir = $this->isDescDir($subject->getCurrentOrder()) ? 'desc' : 'asc';
        $subject->setDefaultDirection($defaultDir);

        if (!$this->toolbarModel->getDirection()
            || $this->shouldSetDirection($subject->getCurrentOrder())
        ) {
            $dir = $defaultDir;
        }

        return $dir;
    }

    /**
     * IsDescDir
     *
     * @param [type] $order
     * @return boolean
     */
    private function isDescDir($order)
    {
        $attributeCodes = $this->helper->getScopeValue('general/desc_attributes');
        $shouldBeDesc = array_merge(self::ALWAYS_DESC, [self::RELEVANCE_DIRECTION]);

        if ($attributeCodes) {
            $shouldBeDesc = array_merge($shouldBeDesc, explode(',', $attributeCodes));
        }

        return in_array($order, $shouldBeDesc);
    }

    /**
     * ShouldSetDirection
     *
     * @param [type] $order
     * @return boolean
     */
    private function shouldSetDirection($order)
    {
        return in_array($order, self::ALWAYS_DESC) || in_array($order, self::ALWAYS_ASC);
    }

    /**
     * BeforeSetCollection
     *
     * @param [type] $subject
     * @param [type] $collection
     * @return void
     */
    public function beforeSetCollection($subject, $collection)
    {
        if ($collection instanceof \Magento\Catalog\Model\ResourceModel\Product\Collection) {
            // no image sorting will be the first or the second (after stock). LIFO queue
            $this->imageMethod->apply($collection);
            // in stock sorting will be first, as the method always moves it's paremater first. LIFO queue
            $this->stockMethod->apply($collection);
        }

        return [$collection];
    }

    /**
     * AfterSetCollection
     *
     * @param [type] $subject
     * @param [type] $result
     * @return void
     */
    public function afterSetCollection($subject, $result)
    {
        $this->applyOrdersFromConfig($subject->getCollection());

        return $result;
    }

    /**
     * ApplyOrdersFromConfig
     *
     * @param [type] $collection
     * @return void
     */
    private function applyOrdersFromConfig($collection)
    {
        if ($this->registry->registry(Data::SEARCH_SORTING)) {
            $defaultSortings = $this->helper->getSearchSorting();
        } else {
            $defaultSortings = $this->helper->getCategorySorting();
        }
        // first sorting must be setting by magento as default sorting
        array_shift($defaultSortings);

        foreach ($defaultSortings as $defaultSorting) {
            $dir = $this->isDescDir($defaultSorting) ? 'desc' : 'asc';
            $collection->setOrder($defaultSorting, $dir);
        }
    }
}
