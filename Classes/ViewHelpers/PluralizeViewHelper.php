<?php

declare(strict_types=1);

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

namespace EBT\ExtensionBuilder\ViewHelpers;

use EBT\ExtensionBuilder\Utility\Inflector;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

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
 */
class PluralizeViewHelper extends AbstractViewHelper
{
    /**
     * Pluralize a word
     *
     * @return string The pluralized string
     */
    public function render()
    {
        $content = $this->renderChildren();
        $pluralizedContent = Inflector::pluralize($content);
        if ($pluralizedContent == $content) {
            $pluralizedContent .= 's';
        }
        return $pluralizedContent;
    }
}
