<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nico de Haen
 *  All rights reserved
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
 * class view helper
 *
 * @version $ID:$
 */

class Tx_ExtensionBuilder_ViewHelpers_ClassViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 *
	 * @param object $classObject
	 * @param string $renderElement
	 * @return
	 */
	public function render($classObject, $renderElement) {
		$content = '';

		switch ($renderElement) {
			case 'parentClass'		:
				$content = $this->renderExtendClassDeclaration($classObject);
				break;

			case 'interfaces'		:
				$content = $this->renderInterfaceDeclaration($classObject);
				break;
		}
		return $content;
	}

	/**
	 *
	 * @param object $classObject
	 * @return
	 */
	private function renderExtendClassDeclaration($classObject) {
		$parentClass = $classObject->getParentClass();
		if (is_object($parentClass)) {
			$parentClass = $parentClass->getName();
		}
		if (!empty($parentClass)) {
			return ' extends ' . $parentClass;
		}
		else return '';
	}

	/**
	 *
	 * @param object $classObject
	 * @return
	 */
	private function renderInterfaceDeclaration($classObject) {
		$interfaceNames = $classObject->getInterfaceNames();
		if (count($interfaceNames) > 0) {
			return ' implements ' . implode(',', $interfaceNames);
		}
		else return '';
	}


}

?>
