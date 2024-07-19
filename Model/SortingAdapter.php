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

namespace Mavenbird\Sorting\Model;

/**
 * Class SortingAdapter
 * adapter of @see \Magento\Eav\Model\Entity\Attribute
 * used for add sorting method
 */
class SortingAdapter extends \Magento\Framework\DataObject
{
    public const CACHE_TAG = 'SORTING_METHOD';

    /**
     * @var \Mavenbird\Sorting\Helper\Data
     */
    private $helper;

    /**
     * @var \Mavenbird\Sorting\Api\MethodInterface
     */
    private $methodModel;

    /**
     * Construct
     *
     * @param \Mavenbird\Sorting\Helper\Data $helper
     * @param \Mavenbird\Sorting\Api\MethodInterface|null $methodModel
     * @param array $data
     */
    public function __construct(
        \Mavenbird\Sorting\Helper\Data $helper,
        \Mavenbird\Sorting\Api\MethodInterface $methodModel = null,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->methodModel = $methodModel;
        parent::__construct($data);
        $this->prepareData();
    }

    /**
     * PrepareData
     *
     * @return void
     */
    private function prepareData()
    {
        if (!$this->hasData('attribute_code')) {
            $this->setData('attribute_code', $this->methodModel->getMethodCode());
        }
        if (!$this->hasData('frontend_label')) {
            $this->setData('frontend_label', $this->methodModel->getMethodName());
        }
    }

    /**
     * GetAttributeCode
     *
     * @return void
     */
    public function getAttributeCode()
    {
        if ($this->hasData('attribute_code')) {
            return $this->_getData('attribute_code');
        }
        return $this->methodModel->getMethodCode();
    }

    /**
     * GetFrontendLabel
     *
     * @return void
     */
    public function getFrontendLabel()
    {
        if ($this->hasData('frontend_label')) {
            return $this->getData('frontend_label');
        }

        return $this->methodModel->getMethodName();
    }

    /**
     * GetDefaultFrontendLabel
     *
     * @return void
     */
    public function getDefaultFrontendLabel()
    {
        return $this->getFrontendLabel();
    }

    /**
     * GetStoreLabel
     *
     * @param [type] $storeId
     * @return void
     */
    public function getStoreLabel($storeId = null)
    {
        if ($this->hasData('store_label')) {
            return $this->getData('store_label');
        }

        return $this->methodModel->getMethodLabel($storeId);
    }

    /**
     * GetIdentities
     *
     * @return void
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getAttributeCode()];
    }

    /**
     * SetMethodModel
     *
     * @param [type] $methodModel
     * @return void
     */
    public function setMethodModel($methodModel)
    {
        $this->methodModel = $methodModel;
        return $this;
    }

    /**
     * GetMethodModel
     *
     * @return void
     */
    public function getMethodModel()
    {
        return $this->methodModel;
    }

    /**
     * GetFrontendInput
     *
     * @return void
     */
    public function getFrontendInput()
    {
        return 'hidden';
    }

    /**
     * GetName
     *
     * @return void
     */
    public function getName()
    {
        return $this->getAttributeCode();
    }
    
    /**
     * UsesSource
     *
     * @return void
     */
    public function usesSource()
    {
        return false;
    }
}
