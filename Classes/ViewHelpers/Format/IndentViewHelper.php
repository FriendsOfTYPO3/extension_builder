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
 * Indentation ViewHelper
 */
class IndentViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    /**
     * Arguments Initialization
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('indentation', 'integer', 'number of spaces to indent', true);
    }

    public function render(): string
    {
        $outputToIndent = $this->renderChildren();
        $lineArray = explode(chr(10), $outputToIndent);
        $indentString = '';
        for ($i = 0; $i < $this->arguments['indentation']; $i++) {
            $indentString .= '    ';
        }
        return implode(chr(10) . $indentString, $lineArray);
    }
}
