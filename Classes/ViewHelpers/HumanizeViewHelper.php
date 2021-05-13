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
 * Makes a word in CamelCase or lower_underscore human readable
 *
 * = Examples =
 *
 * <code title="Example">
 * <k:inflect.humanize>foo_bar</k:inflect.humanize>
 * </code>
 *
 * Output:
 * Foo Bar
 */
class HumanizeViewHelper extends AbstractViewHelper
{
    /**
     * Arguments Initialization
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('string', 'string', 'The string to make human readable', true);
    }

    /**
     * Make a word human readable
     *
     * @return string The human readable string
     */
    public function render(): string
    {
        $string = $this->arguments['string'];
        if ($string === null) {
            $string = $this->renderChildren();
        }

        return Inflector::humanize($string);
    }
}
