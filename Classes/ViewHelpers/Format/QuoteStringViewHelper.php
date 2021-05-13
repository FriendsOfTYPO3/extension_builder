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
 * <f:quoteString>{anyString}</f:quoteString>
 */
class QuoteStringViewHelper extends AbstractViewHelper
{
    /**
     * Arguments Initialization
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'string', 'The string to addslashes', false);
    }

    public function render(): string
    {
        $value = null;
        if ($this->hasArgument('value')) {
            $value = $this->arguments['value'];
        }
        if ($value === null) {
            $value = $this->renderChildren();
        }

        return addslashes($value);
    }
}
