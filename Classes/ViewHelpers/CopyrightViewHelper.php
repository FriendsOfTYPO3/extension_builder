<?php
namespace EBT\ExtensionBuilder\ViewHelpers;
/*                                                                        *
 * This script belongs to the TYPO3 package "Extension Builder".          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Format the Copyright notice
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @scope prototype
 */
class CopyrightViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Format the copyright holder's name(s)
	 *
	 * @param string $date
	 * @param \EBT\ExtensionBuilder\Domain\Model\Person[] $persons
	 * @return string The copyright ownership
	 * @author Andreas Lappe <nd@kaeufli.ch>
	 */
	public function render($date, $persons) {
		$copyright= ' *  (c) ' . $date . ' ';
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
