<?php
namespace EBT\ExtensionBuilder\Utility;
/*                                                                        *
 * This script belongs to the TYPO3 package "Extension Builder".          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

require(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Resources/Private/PHP/Sho_Inflect.php');

/**
 * Inflector utilities for the Extension Builder. This is a basic conversion from PHP
 * class and field names to a human readable form.
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Inflector {
	/**
	 * @param string $word The word to pluralize
	 * @return string The pluralized word
	 * @author Christopher Hlubek
	 */
	// TODO: These methods are static now, this breaks other places.
	public static function pluralize($word) {
		return \Sho_Inflect::pluralize($word);
	}

	/**
	 * @param string $word The word to singularize
	 * @return string The singularized word
	 * @author Sebastian Kurfürst <sbastian@typo3.org>
	 */
	public static function singularize($word) {
		return \Sho_Inflect::singularize($word);
	}

	/**
	 * Convert a model class name like "BlogAuthor" or a field name like
	 * "blog_author" to a humanized version "Blog Author" for better readability.
	 *
	 * @param string $string The camel cased or lower underscore value
	 * @return string The humanized value
	 */
	public static function humanize($string) {
		$string = strtolower(preg_replace('/(?<=\w)([A-Z])/', '_\\1', $string));
		$delimiter = '\\';
		if (strpos($delimiter, $string) === FALSE) {
			$delimiter = '_';
		}
		$string = str_replace($delimiter, ' ', $string);
		$string = ucwords($string);
		return $string;
	}

}
