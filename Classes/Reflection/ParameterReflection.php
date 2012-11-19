<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nico de Haen <mail@ndh-websolutions.de>
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
 * Extended version of the Tx_Extbase_Reflection_ParameterReflection
 * implements typeHint
 */
class Tx_ExtensionBuilder_Reflection_ParameterReflection extends TYPO3\CMS\Extbase\Reflection\ParameterReflection {

	/**
	 * typeHint is missing in PHP Reflection (at least in 5.3)
	 * In the constructor of Tx_ExtensionBuilder_Reflection_MethodReflection
	 * is a workaround implemented and each parameter gets a typeHint injected
	 *
	 *
	 * @var string
	 */
	var $typeHint;

	/**
	 * The constructor, initializes the reflection parameter
	 *
	 * @param  string $functionName: Name of the function
	 * @param  string $propertyName: Name of the property to reflect
	 * @param  string $typeHint: The typeHint of this parameter
	 * @return void
	 */
	public function __construct($function, $parameterName, $typeHint = '') {
		parent::__construct($function, $parameterName);
		$this->typeHint = $typeHint;
	}

	/**
	 *
	 * @return string $typeHint
	 */
	public function getTypeHint() {
		return $this->typeHint;
	}

	/**
	 *
	 * @param string $typeHint
	 * @return void
	 */
	public function setTypeHint($typeHint) {
		$this->typeHint = $typeHint;
	}


}

?>