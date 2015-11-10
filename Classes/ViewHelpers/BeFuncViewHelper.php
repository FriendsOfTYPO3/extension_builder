<?php
namespace EBT\ExtensionBuilder\ViewHelpers;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * View helper which return input as it is
 *
 * = Examples =
 *
 * <f:null>{anyString}</f:null>
 *
 *
 */
class BeFuncViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $objectAccessorPostProcessorEnabled = false;

    /**
     * Render without processing
     *
     *
     * @return string
     */
    public function render()
    {
        return $this->renderChildren();
    }
}
