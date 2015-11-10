<?php
namespace EBT\ExtensionBuilder\ViewHelpers;

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

/**
 * Format the Copyright notice
 *
 */
class CopyrightViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Format the copyright holder's name(s)
     *
     * @param string $date
     * @param \EBT\ExtensionBuilder\Domain\Model\Person[] $persons
     * @return string The copyright ownership
     * @author Andreas Lappe <nd@kaeufli.ch>
     */
    public function render($date, $persons)
    {
        $copyright = ' *  (c) ' . $date . ' ';
        $offset = strlen($copyright) - 2;

        foreach ($persons as $index => $person) {
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
