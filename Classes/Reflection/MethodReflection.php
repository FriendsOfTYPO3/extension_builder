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
 * Extended version of the ReflectionMethod
 *
 * @package ExtensionBuilder
 * @subpackage Reflectionn $
 */
class Tx_ExtensionBuilder_Reflection_MethodReflection extends Tx_Extbase_Reflection_MethodReflection {


	protected $tags;

	/**
	 * @var string description as found in docComment
	 */
	protected $description;

	/**
	 * The constructor, initializes the reflection class
	 *
	 * @param  string $className Name of the method's class
	 * @param  string $methodName Name of the method to reflect
	 * @return void
	 */
	public function __construct($className, $methodName) {
		parent::__construct($className, $methodName);
	}

	/**
	 * Replacement for the original getParameters() method which makes sure
	 * that Tx_Extbase_Reflection_ParameterReflection objects are returned instead of the
	 * orginal ReflectionParameter instances.
	 *
	 * @return array of Tx_ExtensionBuilder_Reflection_ParameterReflection Parameter reflection objects of the parameters of this method
	 */
	public function getParameters() {

		$extendedParameters = array();
		foreach (parent::getParameters() as $parameter) {
			$typeHint = $this->getTypeHintFromReflectionParameter($parameter);
			$extendedParameters[] = new Tx_ExtensionBuilder_Reflection_ParameterReflection(array($this->getDeclaringClass()->getName(), $this->getName()), $parameter->getName(), $typeHint);

		}
		return $extendedParameters;
	}


	/**
	 * Workaround for missing support of typeHints in parameters
	 * the typeHint is parsed from a the casted string representation of the
	 * reflectionParameter
	 * The string has the format 'Parameter #index [ <required/optional> typeHint $parameterName ]'
	 * where index is the sort number and typeHint is optional
	 * The parts in the brackets are splitted and counted
	 *
	 * @param $reflectionParameter
	 * @return string typeHint
	 */
	protected function getTypeHintFromReflectionParameter($reflectionParameter) {
		$paramAsString = (string)$reflectionParameter;
		$paramRegex = '/^Parameter\s\#[0-9]\s\[\s<(required|optional)>\s*.*\$.*]$/';
		//t3lib_div::devLog('ReflectionParameter in method '.$this->getName().' : '.$paramAsString,'extension_builder',2);

		if (!preg_match($paramRegex, $paramAsString)) {
			// since the approach to cast the reflection parameter as a string is not part of the official PHP API
			// this might not work anymore in future versions
			t3lib_div::devLog('ReflectionParameter in method ' . $this->getName() . ' casted as string has not the expected format: ' . $paramAsString, 'extension_builder', 2);
			return '';
		}
		$typeHintRegex = '/>\s*([a-zA-Z0-9_&\s]*)\s*\$/';
		$matches = array();
		if (preg_match($typeHintRegex, $paramAsString, $matches)) {
			if (!empty($matches[1])) {
				$typeHint = $matches[1];
				if ($reflectionParameter->isPassedByReference()) {
					// remove the & from typeHint
					$typeHint = str_replace('&', '', $typeHint);
				}
				$typeHint = trim($typeHint);
				return $typeHint;
			}
		}
		return '';
	}


	public function getDescription() {
		if (empty($this->description)) {
			$this->description = $this->getDocCommentParser()->getDescription();
		}
		return $this->description;
	}

	/**
	 * Returns the declaring class
	 *
	 * @return Tx_ExtensionBuilder_Reflection_ClassReflection The declaring class
	 */
	public function getDeclaringClass() {
		return new Tx_ExtensionBuilder_Reflection_ClassReflection(parent::getDeclaringClass()->getName());
	}

	public function getTags() {
		return $this->getDocCommentParser()->getTagsValues();
	}

}

?>
