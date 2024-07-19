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

use Mavenbird\Sorting\Model\Source\Image as ImageSource;
use Magento\Sitemap\Model\ResourceModel\Catalog\Product as ProductResource;

/**
 * Class Image
 * Method Using like additional sorting and not visible in the list of methods
 */
class Image extends AbstractMethod
{
    /**
     * {@inheritdoc}
     */
    public function getSortingColumnName()
    {
        return 'small_image';
    }

    /**
     * {@inheritdoc}
     */
    public function apply($collection, $direction = '')
    {
        if (!$this->isMethodActive($collection)) {
            return $this;
        }

        $attribute = $this->getSortingColumnName();

        $collection->addAttributeToSelect($attribute, 'left');
        $collection->getSelect()->order($this->getSortExpression($attribute));

        $orders = $collection->getSelect()->getPart(\Zend_Db_Select::ORDER);
        // move from the last to the the first position
        array_unshift($orders, array_pop($orders));
        $collection->getSelect()->setPart(\Zend_Db_Select::ORDER, $orders);

        return $this;
    }

    /**
     * Is can apply method sorting
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return bool
     */
    private function isMethodActive($collection)
    {
        $show = $this->helper->getScopeValue('general/no_image_last');

        if (!$show || ($show == ImageSource::SHOW_LAST_FOR_CATALOG && $this->isSearchModule())) {
            return false;
        }

        return true;
    }

    /**
     * Skip search results
     *
     * @return bool
     */
    private function isSearchModule()
    {
        return in_array(
            $this->request->getModuleName(),
            ['sqli_singlesearchresult', 'catalogsearch']
        );
    }

    /**
     * If image value is no_selection then drop value to down of the list
     * return IF(IFNULL(e.small_image, 'no_selection')='no_selection', 1, 0)
     *
     * @return \Zend_Db_Expr
     */
    private function getSortExpression($imageColumn)
    {
        $connection = $this->getConnection();
        $noSelection = $connection->quote(ProductResource::NOT_SELECTED_IMAGE);
        /** IFNULL(e.small_image, 'no_selection') */
        $ifNull = $connection->getIfNullSql($imageColumn, $noSelection);
        /** IFNULL(e.small_image, 'no_selection')='no_selection' */
        $ifNull .= '=' . $noSelection;

        /** IF(IFNULL(e.small_image, 'no_selection')='no_selection', 1, 0) */
        return $connection->getCheckSql($ifNull, 1, 0);
    }

    /**
     * @inheritdoc
     */
    public function getIndexedValues($storeId)
    {
        return [];
    }
}
