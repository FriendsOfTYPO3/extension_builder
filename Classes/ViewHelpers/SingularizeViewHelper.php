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
 * Pluralize a word
 *
 * = Examples =
 *
 * <code title="Example">
 * <k:inflect.pluralize>foo</k:inflect.pluralize>
 * </code>
 *
 * Output:
 * foos
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @scope prototype
 */
class SingularizeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {
	/**
	 * @var \EBT\ExtensionBuilder\Utility\Inflector
	 */
	protected $inflector = NULL;

	public function __construct() {
		$this->inflector = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('EBT\\ExtensionBuilder\\Utility\\Inflector');
	}

	/**
	 * Singularize a word
	 *
	 * @return string The pluralized string
	 * @author Sebastian Kurfürst <sbastian@typo3.org>
	 */
	public function render() {
		$content = $this->renderChildren();
		return $this->inflector->singularize($content);
	}
}
