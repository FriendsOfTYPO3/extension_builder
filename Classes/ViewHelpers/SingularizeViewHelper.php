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
 * Pluralize a word
 *
 * = Examples =
 *
 * <code title="Example">
 * <k:inflect.pluralize>foo</k:inflect.pluralize>
 * </code>
 *
 * Output:
 * foos
 *
 */
class SingularizeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @var \EBT\ExtensionBuilder\Utility\Inflector
     */
    protected $inflector = null;

    public function __construct()
    {
        $this->inflector = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('EBT\\ExtensionBuilder\\Utility\\Inflector');
    }

    /**
     * Singularize a word
     *
     * @return string The pluralized string
     * @author Sebastian Kurfürst <sbastian@typo3.org>
     */
    public function render()
    {
        $content = $this->renderChildren();
        return $this->inflector->singularize($content);
    }
}
