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

namespace Mavenbird\Sorting\Plugin\Elasticsearch\Model\Adapter\BatchDataMapper;

use Mavenbird\Sorting\Model\Elasticsearch\Adapter\DataMapperInterface;
use Magento\Elasticsearch\Model\Adapter\BatchDataMapper\ProductDataMapper;

class AdditionalProductDataMapper
{
    /**
     * @var DataMapperInterface[]
     */
    private $dataMappers = [];

    /**
     * Construct
     *
     * @param array $dataMappers
     */
    public function __construct(array $dataMappers = [])
    {
        $this->dataMappers = $dataMappers;
    }

    /**
     * AroundMap
     *
     * @param [type] $subject
     * @param callable $proceed
     * @param array $documentData
     * @param [type] $storeId
     * @param array $context
     * @return void
     */
    public function aroundMap(
        $subject,
        callable $proceed,
        array $documentData,
        $storeId,
        $context = []
    ) {
        $documentData = $proceed($documentData, $storeId, $context);
        foreach ($documentData as $productId => $document) {
            $context['document'] = $document;
            foreach ($this->dataMappers as $mapper) {
                if ($mapper instanceof DataMapperInterface && $mapper->isAllowed()) {
                    $document = array_merge($document, $mapper->map($productId, $document, $storeId, $context));
                }
            }
            $documentData[$productId] = $document;
        }

        return $documentData;
    }
}
