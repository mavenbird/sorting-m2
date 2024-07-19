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

use Mavenbird\Sorting\Model\Logger as MavenbirdLogger;
use Mavenbird\Sorting\Model\MethodProvider;
use Magento\Catalog\Block\Product\ListProduct as NativeList;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;

class ListProduct
{
    /**
     * @var MethodProvider
     */
    private $methodProvider;

    /**
     * @var MavenbirdLogger
     */
    private $logger;

    /**
     * Construct
     *
     * @param MethodProvider $methodProvider
     * @param MavenbirdLogger $logger
     */
    public function __construct(MethodProvider $methodProvider, MavenbirdLogger $logger)
    {
        $this->methodProvider = $methodProvider;
        $this->logger = $logger;
    }

    /**
     * AfterGetIdentities
     *
     * @param NativeList $subject
     * @param [type] $identities
     * @return void
     */
    public function afterGetIdentities(NativeList $subject, $identities)
    {
        /** @var Toolbar $toolbarBlock */
        $toolbarBlock = $subject->getLayout()->getBlock('product_list_toolbar');
        if ($toolbarBlock
            && in_array(
                $toolbarBlock->getCurrentOrder(),
                array_keys($this->methodProvider->getIndexedMethods())
            )
        ) {
            $identities[] = 'sorted_by_' . $toolbarBlock->getCurrentOrder();
        }

        return $identities;
    }

    /**
     * AfterGetLoadedProductCollection
     *
     * @param NativeList $subject
     * @param [type] $result
     * @return void
     */
    public function afterGetLoadedProductCollection(NativeList $subject, $result)
    {
        $this->logger->logCollectionQuery($result);

        return $result;
    }
}
