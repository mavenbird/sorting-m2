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

use Mavenbird\Sorting\Api\MethodInterface;
use Mavenbird\Sorting\Api\IndexMethodWrapperInterface;
use Mavenbird\Sorting\Helper\Data as Helper;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Method
 */
class MethodProvider
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Sorting Methods which can use index table
     *
     * @var IndexMethodWrapperInterface[]
     */
    private $indexedMethods = [];

    /**
     * Sorting methods
     *
     * @var MethodInterface[]
     */
    private $methods = [];

    /**
     * @var Helper
     */
    private $helper;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Helper $helper
     * @param array $indexedMethods
     * @param array $methods
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Helper $helper,
        $indexedMethods = [],
        $methods = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
        $this->initMethods($indexedMethods, $methods);
    }

    /**
     * InitMethods
     *
     * @param array $indexedMethods
     * @param array $methods
     * @return void
     */
    private function initMethods($indexedMethods = [], $methods = [])
    {
        foreach ($indexedMethods as $methodWrapper) {
            $this->indexedMethods[$methodWrapper->getSource()->getMethodCode()] = $methodWrapper;
        }
        foreach ($methods as $methodObject) {
            if (!$methodObject instanceof MethodInterface) {
                if (is_object($methodObject)) {
                    throw new LocalizedException(
                        __('Method object ' . get_class($methodObject) .
                            ' must be implemented by Mavenbird\Sorting\Api\MethodInterface')
                    );
                } else {
                    throw new LocalizedException(__('$methodObject is not object'));
                }
            }
            $this->methods[$methodObject->getMethodCode()] = $methodObject;
        }
    }

    /**
     * GetMethodByCode
     *
     * @param [type] $code
     * @return void
     */
    public function getMethodByCode($code)
    {
        if (isset($this->methods[$code]) && !$this->helper->isMethodDisabled($code)) {
            return $this->methods[$code];
        }

        return null;
    }

    /**
     * GetIndexedMethods
     *
     * @return void
     */
    public function getIndexedMethods()
    {
        return $this->indexedMethods;
    }

    /**
     * GetMethods
     *
     * @return void
     */
    public function getMethods()
    {
        return $this->methods;
    }
}
