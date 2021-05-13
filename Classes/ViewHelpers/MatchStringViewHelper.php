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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper to check if one string contains another string
 *
 * = Examples =
 * <k:matchString match="this" in="this and that" />
 * {k:matchString(match:'this', in:'this and that')}
 */
class MatchStringViewHelper extends AbstractViewHelper
{
    /**
     * Arguments Initialization
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('match', 'string', 'RegEx', true);
        $this->registerArgument('in', 'string', 'the string to compare', true);
        $this->registerArgument('caseSensitive', 'boolean', 'caseSensitive', false);
    }

    public function render(): bool
    {
        $matchAsRegularExpression = '/' . $this->arguments['match'] . '/';
        if (!$this->arguments['caseSensitive']) {
            $matchAsRegularExpression .= 'i';
        }
        return preg_match($matchAsRegularExpression, $this->arguments['in']) !== 0;
    }
}
