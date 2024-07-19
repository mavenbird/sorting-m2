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

class Logger
{
    public const DEBUG_CONFIG_PATH = 'sorting/general/debug';
    public const DEBUG_REQUEST_VAR = 'debug';
    
    /**
     * @var \Mavenbird\Core\Debug\VarDump
     */
    private $logger;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * Construct
     *
     * @param \Mavenbird\Core\Debug\VarDump $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Mavenbird\Core\Debug\VarDump $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    /**
     * LogCollectionQuery
     *
     * @param [type] $collection
     * @return void
     */
    public function logCollectionQuery($collection)
    {
        if ($this->scopeConfig->isSetFlag(self::DEBUG_CONFIG_PATH)
            && $this->request->getParam(self::DEBUG_REQUEST_VAR, false)
        ) {
            $this->logger->mavenbirdEcho($collection->getSelect()->__toString());
        }
        return $this;
    }
}
