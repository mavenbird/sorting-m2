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

namespace Mavenbird\Sorting\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Mavenbird\Sorting\Helper\Data as SortingHelper;
use Magento\Catalog\Model\Config;

class Position extends Field
{
    /**
     * @var SortingHelper
     */
    private $helper;

    /**
     * @var Config
     */
    private $config;

    /**
     * Construct
     *
     * @param SortingHelper $helper
     * @param Config $config
     * @param Context $context
     */
    public function __construct(
        SortingHelper $helper,
        Config $config,
        Context $context
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->config = $config;
    }

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('Mavenbird_Sorting::/position.phtml');
    }

    /**
     * Render
     *
     * @param AbstractElement $element
     * @return void
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);

        return $this->_toHtml();
    }

    /**
     * GetPositions
     *
     * @return void
     */
    public function getPositions()
    {
        $positions =  (array) $this->helper->getSortOrder();
        if ($positions === []) {
            $positions = $this->getOptionalArray();
        } else {
            $availableOptions = $this->getOptionalArray();
            // delete disabled options
            $positions = array_intersect($availableOptions, $positions);
            $newOptions = array_diff($availableOptions, $positions);
            $positions = array_merge($positions, $newOptions);
        }

        return $positions;
    }

    /**
     * GetNamePrefix
     *
     * @param [type] $index
     * @return void
     */
    public function getNamePrefix($index)
    {
        return $this->getElement()->getName() . '[' . $index . ']';
    }

    /**
     * GetOptionalArray
     *
     * @return void
     */
    private function getOptionalArray()
    {
        $positions = [];
        $methods = $this->config->getAttributeUsedForSortByArray();
        foreach ($methods as $key => $methodObject) {
            if (is_object($methodObject)) {
                $positions[$key] = $methodObject->getText();
            } else {
                $positions[$key] = $methodObject;
            }
        }

        return $positions;
    }
}
