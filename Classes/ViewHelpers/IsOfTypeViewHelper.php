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
 * Wrapper for PHPs ucfirst function.
 * @see http://www.php.net/manual/en/ucfirst
 *
 * = Examples =
 *
 * <code title="Example">
 * <k:uppercaseFirst>{textWithMixedCase}</k:uppercaseFirst>
 * </code>
 *
 * Output:
 * TextWithMixedCase
 *
 */
class IsOfTypeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Checks if $object is of type $type and returns TRUE or FALSE respectively
	 * @param mixed $object
	 * @param string $type
	 * @return boolean TRUE or FALSE
	 */
	public function render($object, $type) {
		return is_a($object, 'EBT\\ExtensionBuilder\\Domain\\Model\\' . $type);
	}

}
