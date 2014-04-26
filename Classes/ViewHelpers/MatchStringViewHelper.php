<?php
namespace EBT\ExtensionBuilder\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3 package "Extension Builder".         *
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
 * View helper to check if one string contains another string
 *
 * = Examples =
 * <k:matchString match="this" in="this and that" />
 * {k:matchString(match:'this', in:'this and that')}
 *
 * @author Andreas Lappe
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class MatchStringViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {
	/**
	 * @param string $match
	 * @param string $in
	 * @param boolean $caseSensitive
	 *
	 * @return boolean
	 */
	public function render($match, $in, $caseSensitive = FALSE) {
		$matchAsRegularExpression = '/' . $match . '/';
		if (!$caseSensitive) $matchAsRegularExpression .= 'i';
		return (preg_match($matchAsRegularExpression, $in) === 0) ? FALSE : TRUE;
	}
}
