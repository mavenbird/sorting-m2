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

namespace Mavenbird\Sorting\Model\Elasticsearch\Adapter;

class AdditionalFieldMapper
{
    public const ES_DATA_TYPE_STRING = 'string';
    public const ES_DATA_TYPE_TEXT = 'text';
    public const ES_DATA_TYPE_FLOAT = 'float';
    public const ES_DATA_TYPE_INT = 'integer';
    public const ES_DATA_TYPE_DATE = 'date';

    /** @deprecated */
    public const ES_DATA_TYPE_ARRAY = 'array';

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Construct
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $fields
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $fields = []
    ) {
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->fields = $fields;
    }

    /**
     * AfterGetAllAttributesTypes
     *
     * @param [type] $subject
     * @param array $result
     * @return void
     */
    public function afterGetAllAttributesTypes($subject, array $result)
    {
        foreach ($this->fields as $fieldName => $fieldType) {
            if (is_object($fieldType) && ($fieldType instanceof AdditionalFieldMapperInterface)) {
                $attributeTypes = $fieldType->getAdditionalAttributeTypes();
                $result = array_merge($result, $attributeTypes);
                continue;
            }

            if (empty($fieldName)) {
                continue;
            }
            if ($this->isValidFieldType($fieldType)) {
                $result[$fieldName] = ['type' => $fieldType];
            }
        }

        return $result;
    }

    /**
     * AfterBuildEntityFields
     *
     * @param [type] $subject
     * @param array $result
     * @return void
     */
    public function afterBuildEntityFields($subject, array $result)
    {
        return $this->afterGetAllAttributesTypes($subject, $result);
    }

    /**
     * AroundGetFieldName
     *
     * @param [type] $subject
     * @param callable $proceed
     * @param [type] $attributeCode
     * @param array $context
     * @return void
     */
    public function aroundGetFieldName($subject, callable $proceed, $attributeCode, $context = [])
    {
        if (isset($this->fields[$attributeCode]) && is_object($this->fields[$attributeCode])) {
            $filedMapper = $this->fields[$attributeCode];
            if ($filedMapper instanceof AdditionalFieldMapperInterface) {
                return $filedMapper->getFiledName($context);
            }
        }
        return $proceed($attributeCode, $context);
    }

    /**
     * AroundMapFieldName
     *
     * @param [type] $subject
     * @param callable $proceed
     * @param [type] $fieldName
     * @return void
     */
    public function aroundMapFieldName($subject, callable $proceed, $fieldName)
    {
        if (isset($this->fields[$fieldName]) && is_object($this->fields[$fieldName])) {
            $filedMapper = $this->fields[$fieldName];
            if ($filedMapper instanceof AdditionalFieldMapperInterface) {
                $context = [
                    'customerGroupId' => $this->customerSession->getCustomerGroupId(),
                    'websiteId'       => $this->storeManager->getWebsite()->getId()
                ];
                return $filedMapper->getFiledName($context);
            }
        }
        return $proceed($fieldName);
    }

    /**
     * IsValidFieldType
     *
     * @param [type] $fieldType
     * @return boolean
     */
    private function isValidFieldType($fieldType)
    {
        switch ($fieldType) {
            case self::ES_DATA_TYPE_STRING:
            case self::ES_DATA_TYPE_DATE:
            case self::ES_DATA_TYPE_INT:
            case self::ES_DATA_TYPE_FLOAT:
                break;
            default:
                $fieldType = false;
                break;
        }
        return $fieldType;
    }
}
