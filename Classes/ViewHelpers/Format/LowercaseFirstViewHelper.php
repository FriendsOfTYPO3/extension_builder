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

namespace EBT\ExtensionBuilder\ViewHelpers\Format;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Wrapper for PHPs lcfirst function.
 * @see http://www.php.net/manual/en/lcfirst
 *
 * = Examples =
 *
 * <code title="Example">
 * <k:lowercaseFirst>{TextWithMixedCase}</k:lowercaseFirst>
 * </code>
 *
 * Output:
 * textWithMixedCase
 */
class LowercaseFirstViewHelper extends AbstractViewHelper
{
    /**
     * Lowercase first character
     *
     * @return string The altered string.
     */
    public function render(): string
    {
        return lcfirst($this->renderChildren());
    }
}
