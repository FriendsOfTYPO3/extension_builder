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
 * Singularize a word
 *
 * = Examples =
 *
 * <code title="Example">
 * <k:inflect.singularize>foos</k:inflect.singularize>
 * </code>
 *
 * Output:
 * foo
 */
class SingularizeViewHelper extends AbstractViewHelper
{
    /**
     * Singularize a word
     *
     * @return string The singularized string
     */
    public function render(): string
    {
        $content = $this->renderChildren();
        return Inflector::singularize($content);
    }
}
