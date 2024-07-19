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

use Mavenbird\Sorting\Api\MethodInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class AbstractMethod
 *
 * @package Mavenbird\Sorting\Model\Method
 */
abstract class AbstractMethod extends AbstractDb implements MethodInterface
{
    /**
     * @var bool
     */
    public const ENABLED = true;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var string
     */
    protected $methodCode;

    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var \Mavenbird\Sorting\Helper\Data
     */
    protected $helper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var AdapterInterface|null
     */
    protected $indexConnection = null;

    /**
     * @var array
     */
    private $data;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * Construct
     *
     * @param Context $context
     * @param \Magento\Framework\Escaper $escaper
     * @param [type] $connectionName
     * @param string $methodCode
     * @param string $methodName
     * @param AbstractDb|null $indexResource
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Escaper $escaper,
        $connectionName = null,
        $methodCode = '',
        $methodName = '',
        AbstractDb $indexResource = null,
        $data = []
    ) {
        $this->scopeConfig      = $context->getScopeConfig();
        $this->request          = $context->getRequest();
        $this->storeManager     = $context->getStoreManager();
        $this->helper           = $context->getHelper();
        $this->logger           = $context->getLogger();
        $this->date             = $context->getDate();
        $this->methodCode       = $methodCode;
        $this->methodName       = $methodName;
        if ($indexResource) {
            $this->indexConnection = $indexResource->getConnection();
        }
        $this->data = $data;
        parent::__construct($context, $connectionName);
        $this->escaper = $escaper;
    }

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        // dummy
    }

    /**
     * Apply
     *
     * @param [type] $collection
     * @param [type] $direction
     * @return void
     */
    abstract public function apply($collection, $direction);

    /**
     * GetIndexedValues
     *
     * @param [type] $storeId
     * @return void
     */
    abstract public function getIndexedValues($storeId);

    /**
     * IsActive
     *
     * @return boolean
     */
    public function isActive()
    {
        return !$this->helper->isMethodDisabled($this->getMethodCode());
    }

    /**
     * GetMethodCode
     *
     * @return void
     */
    public function getMethodCode()
    {
        if (empty($this->methodCode)) {
            $this->logger->warning('Undefined mavenbird sorting method code, add method code to di.xml');
        }
        return $this->methodCode;
    }

    /**
     * GetMethodName
     *
     * @return void
     */
    public function getMethodName()
    {
        if (empty($this->methodCode)) {
            $this->logger->warning('Undefined mavenbird sorting method code, add method code to di.xml');
        }
        return $this->methodName;
    }

    /**
     * GetMethodLabel
     *
     * @param [type] $store
     * @return void
     */
    public function getMethodLabel($store = null)
    {
        $label = $this->helper->getScopeValue($this->getMethodCode() . '/label', $store);
        if (!$label) {
            $label = __($this->getMethodName());
        }

        return $this->escaper->escapeHtml($label);
    }

    /**
     * GetAdditionalData
     *
     * @param [type] $key
     * @return void
     */
    protected function getAdditionalData($key)
    {
        $result = null;
        if (isset($this->data[$key])) {
            $result = $this->data[$key];
        }

        return $result;
    }
}
