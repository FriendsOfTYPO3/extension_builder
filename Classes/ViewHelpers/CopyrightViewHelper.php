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
 * Format the Copyright notice
 */
class CopyrightViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    protected $escapeChildren = false;

    /**
     * Arguments Initialization
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('date', 'string', 'Date', true);
        $this->registerArgument('persons', 'array', 'Array with persons', true);
    }

    /**
     * Format the copyright holder's name(s)
     *
     * @return string The copyright ownership
     */
    public function render(): string
    {
        $copyright = ' * (c) ' . $this->arguments['date'] . ' ';
        $offset = strlen($copyright) - 2;

        foreach ($this->arguments['persons'] as $index => $person) {
            $entry = '';

            if ($index !== 0) {
                $entry .= chr(10) . ' *' . str_repeat(' ', $offset);
            }

            $entry .= $person->getName();

            if ($person->getEmail() !== '') {
                $entry .= ' <' . $person->getEmail() . '>';
            }

            if ($person->getCompany() !== '') {
                $entry .= ', ' . $person->getCompany();
            }

            $copyright .= $entry;
        }

        return $copyright;
    }
}
