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
 * View helper for preg_replace
 *
 * = Examples =
 * <k:pregReplace match="/this/" replace="that" subject="this" />
 * {k:pregReplace(match:'/this/', replace:'that', subject:'this')}
 */
class PregReplaceViewHelper extends AbstractViewHelper
{
    /**
     * Arguments Initialization
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('match', 'string', 'pattern', true);
        $this->registerArgument('replace', 'string', 'replacement', true);
        $this->registerArgument('subject', 'string', 'subject', true);
    }

    /**
     * Execute the preg_replace
     *
     * @return mixed
     */
    public function render()
    {
        return preg_replace($this->arguments['match'], $this->arguments['replace'], $this->arguments['subject']);
    }
}
