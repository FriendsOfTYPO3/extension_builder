<?php
namespace EBT\ExtensionBuilder\Utility;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Jochen Rau <jochen.rau@typoplanet.de>
 *  All rights reserved
 *
 *  This class is a backport of the corresponding class of FLOW3.
 *  All credits go to the v5 team.
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Autoloader of ExtensionBuilder
 *
 * Needed to avoid errors when loading classes that have references or parent classes
 * to other classes in a not installed extension
 */
class ClassLoader {

	/**
	 * Loads php files containing classes or interfaces found in the classes directory of
	 * an extension.
	 *
	 * @param string $className: Name of the class/interface to load
	 * @uses \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath()
	 * @return void
	 */
	public static function loadClass($className) {
		$delimiter = '\\';
		$index = 1;
		if (strpos($delimiter, $className) === FALSE) {
			$delimiter = '_';
			$index = 2;
		}
		$classNameParts = explode($delimiter, $className, 4);
		$extensionKey = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToLowerCamelCase($classNameParts[$index]);
		$classFilePathAndName = PATH_typo3conf . 'ext/' . $extensionKey . '/Classes/' . strtr($classNameParts[$index + 1], $delimiter, '/') . '.php';
		if (file_exists($classFilePathAndName)) {
			require_once($classFilePathAndName);
		}
	}

}
