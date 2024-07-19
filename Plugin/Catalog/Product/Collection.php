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

namespace Mavenbird\Sorting\Plugin\Catalog\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;

/**
 * Plugin Collection
 * plugin name: Mavenbird_Sorting::SortingMethodsProcessor
 * type: \Magento\Catalog\Model\ResourceModel\Product\Collection
 */
class Collection
{
    public const PROCESS_FLAG = 'amsort_process';

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
     * @var \Mavenbird\Sorting\Model\ResourceModel\Method\Image
     */
    private $imageMethod;

    /**
     * @var \Mavenbird\Sorting\Model\ResourceModel\Method\Instock
     */
    private $stockMethod;

    /**
     * @var array
     */
    private $skipAttributes = [];

    /**
     * Construct
     *
     * @param \Mavenbird\Sorting\Helper\Data $helper
     * @param \Mavenbird\Sorting\Model\MethodProvider $methodProvider
     * @param \Mavenbird\Sorting\Model\ResourceModel\Method\Image $imageMethod
     * @param \Mavenbird\Sorting\Model\ResourceModel\Method\Instock $stockMethod
     * @param \Mavenbird\Sorting\Model\SortingAdapterFactory $adapterFactory
     */
    public function __construct(
        \Mavenbird\Sorting\Helper\Data $helper,
        \Mavenbird\Sorting\Model\MethodProvider $methodProvider,
        \Mavenbird\Sorting\Model\ResourceModel\Method\Image $imageMethod,
        \Mavenbird\Sorting\Model\ResourceModel\Method\Instock $stockMethod,
        \Mavenbird\Sorting\Model\SortingAdapterFactory $adapterFactory
    ) {
        $this->helper         = $helper;
        $this->methodProvider = $methodProvider;
        $this->adapterFactory = $adapterFactory;
        $this->imageMethod    = $imageMethod;
        $this->stockMethod    = $stockMethod;
    }

    /**
     * BeforeSetOrder
     *
     * @param [type] $subject
     * @param [type] $attribute
     * @param [type] $dir
     * @return void
     */
    public function beforeSetOrder($subject, $attribute, $dir = Select::SQL_DESC)
    {
        $subject->setFlag(self::PROCESS_FLAG, true);
        $this->applyHighPriorityOrders($subject, $dir);
        $flagName = $this->getFlagName($attribute);
        if ($subject->getFlag($flagName)) {
            if ($this->helper->isElasticSort()) {
                $this->skipAttributes[] = $flagName;
            } else {
                // attribute already used in sorting; disable double sorting by renaming attribute into not existing
                $attribute .= '_ignore';
            }
        } else {
            $method = $this->methodProvider->getMethodByCode($attribute);
            if ($method) {
                $method->apply($subject, $dir);
                $attribute = $method->getAlias();
            }
            if (!$this->helper->isElasticSort()) {
                if ($attribute == 'relevance' && !$subject->getFlag($this->getFlagName('relevance'))) {
                    $this->addRelevanceSorting($subject, $dir);
                    $attribute = 'relevance';
                }
                if ($attribute == 'price') {
                    $subject->addOrder($attribute, $dir);
                    $attribute .= '_ignore';
                }
            }
            $subject->setFlag($flagName, true);
        }

        $subject->setFlag(self::PROCESS_FLAG, false);

        return [$attribute, $dir];
    }

    /**
     * AroundSetOrder
     *
     * @param [type] $subject
     * @param callable $proceed
     * @param [type] $attribute
     * @param [type] $dir
     * @return void
     */
    public function aroundSetOrder($subject, callable $proceed, $attribute, $dir = Select::SQL_DESC)
    {
        $flagName = $this->getFlagName($attribute);
        if (!in_array($flagName, $this->skipAttributes)) {
            $proceed($attribute, $dir);
        }

        return $subject;
    }

    /**
     * GetFlagName
     *
     * @param [type] $attribute
     * @return void
     */
    private function getFlagName($attribute)
    {
        if ($attribute == 'price_asc' || $attribute == 'price_desc') {
            $attribute = 'price';
        }
        if (is_string($attribute)) {
            return 'sorted_by_' . $attribute;
        }

        return 'mavenbird_sorting';
    }

    /**
     * ApplyHighPriorityOrders
     *
     * @param [type] $collection
     * @param [type] $dir
     * @return void
     */
    private function applyHighPriorityOrders($collection, $dir)
    {
        if (!$collection->getFlag($this->getFlagName('high'))) {
            $this->stockMethod->apply($collection, $dir);
            $this->imageMethod->apply($collection, $dir);
            $collection->setFlag($this->getFlagName('high'), true);
        }
    }

    /**
     * AddRelevanceSorting
     *
     * @param [type] $collection
     * @return void
     */
    private function addRelevanceSorting($collection)
    {
        $collection->getSelect()->columns(['relevance' => new \Zend_Db_Expr(
            'search_result.'. TemporaryStorage::FIELD_SCORE
        )]);
        $collection->addExpressionAttributeToSelect('relevance', 'relevance', []);

        // remove last item from columns because e.relevance from addExpressionAttributeToSelect not exist
        $columns = $collection->getSelect()->getPart(\Zend_Db_Select::COLUMNS);
        array_pop($columns);
        $collection->getSelect()->setPart(\Zend_Db_Select::COLUMNS, $columns);
        $collection->setFlag($this->getFlagName('relevance'), true);
    }

    /**
     * BeforeAddOrder
     *
     * @param [type] $subject
     * @param [type] $attribute
     * @param [type] $dir
     * @return void
     */
    public function beforeAddOrder($subject, $attribute, $dir = Select::SQL_DESC)
    {
        if (!$subject->getFlag(self::PROCESS_FLAG)) {
            $result =  $this->beforeSetOrder($subject, $attribute, $dir);
        }

        return $result ?? [$attribute, $dir];
    }

    /**
     * AroundAddOrder
     *
     * @param [type] $subject
     * @param callable $proceed
     * @param [type] $attribute
     * @param [type] $dir
     * @return void
     */
    public function aroundAddOrder($subject, callable $proceed, $attribute, $dir = Select::SQL_DESC)
    {
        return $this->aroundSetOrder($subject, $proceed, $attribute, $dir);
    }
}
