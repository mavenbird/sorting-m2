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

namespace Mavenbird\Sorting\Model\Source;

class Methods implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Mavenbird\Sorting\Model\MethodProvider
     */
    private $methodProvider;

    /**
     * Construct
     *
     * @param \Mavenbird\Sorting\Model\MethodProvider $methodProvider
     */
    public function __construct(
        \Mavenbird\Sorting\Model\MethodProvider $methodProvider
    ) {
        $this->methodProvider = $methodProvider;
    }

    /**
     * ToOptionArray
     *
     * @return void
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->methodProvider->getMethods() as $methodObject) {
            $options[] = [
                'value' => $methodObject->getMethodCode(),
                'label' => $methodObject->getMethodName()
            ];
        }

        return $options;
    }
}
