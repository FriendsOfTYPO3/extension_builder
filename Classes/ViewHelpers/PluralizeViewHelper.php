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

use EBT\ExtensionBuilder\Utility\Inflector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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
class PluralizeViewHelper extends AbstractViewHelper
{
    /**
     * @var \EBT\ExtensionBuilder\Utility\Inflector
     */
    protected $inflector = null;

    public function __construct()
    {
        $this->inflector = GeneralUtility::makeInstance(Inflector::class);
    }

    /**
     * Pluralize a word
     *
     * @return string The pluralized string
     * @author Christopher Hlubek <hlubek@networkteam.com>
     */
    public function render()
    {
        $content = $this->renderChildren();
        $pluralizedContent = $this->inflector->pluralize($content);
        if ($pluralizedContent == $content) {
            $pluralizedContent .= 's';
        }
        return $pluralizedContent;
    }
}
