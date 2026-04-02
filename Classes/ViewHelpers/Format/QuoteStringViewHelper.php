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
 * View helper which returns a quoted string
 *
 * = Examples =
 *
 * <k:quoteString>{anyString}</k:quoteString>
 * <k:quoteString value="{anyString}"/>
 * {anyString -> k:quoteString()}
 */
class QuoteStringViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'string', 'The string to add slashes', false);
    }

    public function render(): string
    {
        $value = $this->arguments['value'] ?? $this->renderChildren();
        return addslashes($value);
    }
}
