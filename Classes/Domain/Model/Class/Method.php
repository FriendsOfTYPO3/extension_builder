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
 * method representing a "method" in the context of software development
 *
 * @version $ID:$
 */
class Tx_ExtensionBuilder_Domain_Model_Class_Method extends Tx_ExtensionBuilder_Domain_Model_Class_AbstractObject {

	/**
	 * body
	 * @var string
	 */
	protected $body;

	public $defaultIndent = "\t\t";

	/**
	 *
	 * @var array
	 */
	protected $parameters;


	public function __construct($methodName, $methodReflection = NULL) {
		$this->setName($methodName);
		if ($methodReflection instanceof Tx_ExtensionBuilder_Reflection_MethodReflection) {
			$methodReflection->getTagsValues(); // just to initialize the docCommentParser
			foreach ($this as $key => $value) {
				$setterMethodName = 'set' . \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($key);
				$getterMethodName = 'get' . \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($key);
				// map properties of reflection class to this class
				if (method_exists($methodReflection, $getterMethodName) && method_exists($this, $setterMethodName)) {
					$this->$setterMethodName($methodReflection->$getterMethodName());
					//\TYPO3\CMS\Core\Utility\GeneralUtility::print_array($getterMethodName);
				}

			}
			if (empty($this->tags)) {
				// strange behaviour in php ReflectionProperty->getDescription(). A backslash is added to the description
				$this->description = str_replace("\n/", '', $this->description);
				$this->description = trim($this->description);
				//$this->setTag('return','void');
			}
		}

	}

	/**
	 * Setter for body
	 *
	 * @param string $body body
	 * @return void
	 */
	public function setBody($body) {
		// keep or set the indent
		if (strpos($body, $this->defaultIndent) !== 0) {
			$lines = explode("\n", $body);
			$newLines = array();
			foreach ($lines as $line) {
				$newLines[] = $this->defaultIndent . $line;
			}
			$body = implode("\n", $newLines);
		}
		$this->body = rtrim($body);
	}

	/**
	 * Getter for body
	 *
	 * @return string body
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * getter for parameters
	 * @return array parameters
	 */
	public function getParameters() {
		return $this->parameters;
	}

	/**
	 * getter for parameter names
	 * @return array parameter names
	 */
	public function getParameterNames() {
		$parameterNames = array();
		if(is_array($this->parameters)) {
			foreach ($this->parameters as $parameter) {
				$parameterNames[] = $parameter->getName();
			}
		}
		return $parameterNames;
	}

	/**
	 * adder for parameters
	 * @param array $parameters of type Tx_ExtensionBuilder_Reflection_ParameterReflection
	 * @return void
	 */
	public function setParameters($parameters) {
		foreach ($parameters as $parameter) {
			$methodParameter = new Tx_ExtensionBuilder_Domain_Model_Class_MethodParameter($parameter->getName(), $parameter);
			$this->parameters[$methodParameter->getPosition()] = $methodParameter;
		}

	}

	/**
	 * setter for a single parameter
	 * @param array $parameter
	 * @return void
	 */
	public function setParameter($parameter) {
		if (!in_array($parameter->getName(), $this->getParameterNames())) {
			$this->parameters[$parameter->getPosition()] = $parameter;
		}

	}

	/**
	 * replace a single parameter, depending on position
	 * @param array $parameter
	 * @return void
	 */
	public function replaceParameter($parameter) {
		$this->parameters[$parameter->getPosition()] = $parameter;
	}

	/**
	 * removes a parameter
	 * @param $parameterName
	 * @param $parameterSortingIndex
	 * @return boolean TRUE (if successfull removed)
	 */
	public function removeParameter($parameterName, $parameterPosition) {
		//TODO: Not yet tested
		if (isset($this->parameter[$parameterPosition]) && $this->parameter[$parameterPosition]->getName() == $parameterName) {
			unset($this->parameter[$parameterPosition]);
			return TRUE;
		}
		else return FALSE;
	}

	/**
	 *
	 * @param $parameterName
	 * @param $parameterSortingIndex
	 * @return boolean TRUE (if successfull removed)
	 */
	public function renameParameter($oldName, $newName, $parameterPosition) {
		//TODO: Not yet tested
		if (isset($this->parameter[$parameterPosition])) {
			$parameter = $this->parameter[$parameterPosition];
			if ($parameter->getName() == $oldName) {
				$parameter->setName($newName);
				$this->parameter[$parameterPosition] = $parameter;
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 *
	 * TODO: THe sorting of tags/annotations should be controlled
	 *
	 */
	public function getAnnotations() {
		$annotations = parent::getAnnotations();
		if (is_array($this->parameters) && count($this->parameters) > 0 && !$this->isTaggedWith('param')) {
			$paramTags = array();
			foreach ($this->parameters as $parameter) {
				$varType = $parameter->getVarType();
				if(in_array(strtolower($varType), array('string','boolean','integer','doubler','float'))) {
					$varType = strtolower(strtolower);
				}
				$paramTags[] = 'param ' . $varType . ' $' . $parameter->getName();
			}
			$annotations = array_merge($paramTags, $annotations);
		}
		if (!$this->isTaggedWith('return')) {
			$annotations[] = 'return';
		}
		return $annotations;
	}

}

?>