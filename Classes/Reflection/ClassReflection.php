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
 * Extended version of the Tx_Extbase_Reflection_ClassReflection
 */
class Tx_ExtensionBuilder_Reflection_ClassReflection extends TYPO3\CMS\Extbase\Reflection\ClassReflection {

	/**
	 *
	 * @var array to keep all methods that are inherited from parent classes
	 */
	protected $inheritedMethods = array();

	/**
	 * The constructor - initializes the class Tx_Extbase_Reflection_reflector
	 *
	 * @param  string $className: Name of the class Tx_Extbase_Reflection_to reflect
	 */
	public function __construct($className) {
		parent::__construct($className);
	}

	/**
	 * Replacement for the original getMethods() method which makes sure
	 * that Tx_Extbase_Reflection_MethodReflection objects are returned instead of the
	 * orginal ReflectionMethod instances.
	 *
	 * @param  long $filter: A filter mask
	 * @return Tx_Extbase_Reflection_MethodReflection Method reflection objects of the methods in this class
	 */
	public function getMethods($filter = NULL) {
		$extendedMethods = array();

		$methods = ($filter === NULL ? parent::getMethods() : parent::getMethods($filter));

		foreach ($methods as $method) {
			$extendedMethods[] = new Tx_ExtensionBuilder_Reflection_MethodReflection($this->getName(), $method->getName());
		}
		return $extendedMethods;
	}


	/**
	 * get all methods that are declared in parent classes and not in the actual class
	 *
	 * @return array
	 */

	public function getInheritedMethods() {
		$inheritedMethods = array();
		$parentClass = $this->getParentClass();
		if ($parentClass) {
			$inheritedMethods = $parentClass->getMethods();
		}
		return $inheritedMethods;
	}

	/**
	 * get all methods that are declared in the actual class and not inherited (or overwrite inherited methods)
	 *
	 * @param $includeOverridingMethods default is TRUE
	 *
	 * @return array
	 */
	public function getNotInheritedMethods($includeOverridingMethods = TRUE) {
		$parentClass = $this->getParentClass();
		if (!$parentClass) {
			return $this->getMethods();
		}
		$allMethods = $this->getMethods();
		$inheritedMethods = $this->getInheritedMethods();
		$notInheritedMethods = array();
		foreach ($allMethods as $method) {
			if (!in_array($method, $inheritedMethods)) {
				if ($includeOverridingMethods || (!$includeOverridingMethods && !$parentClass->hasMethod($method->getName()))) {
					$notInheritedMethods[] = $method;
				}
			}
		}
		return $notInheritedMethods;
	}


	/**
	 * Replacement for the original getMethod() method which makes sure
	 * that Tx_Extbase_Reflection_MethodReflection objects are returned instead of the
	 * orginal ReflectionMethod instances.
	 *
	 * @return Tx_ExtensionBuilder_Reflection_MethodReflection Method reflection object of the named method
	 */
	public function getMethod($name) {
		$parentMethod = parent::getMethod($name);
		if (!is_object($parentMethod)) return $parentMethod;
		return new Tx_ExtensionBuilder_Reflection_MethodReflection($this->getName(), $parentMethod->getName());
	}

	/**
	 * Replacement for the original getConstructor() method which makes sure
	 * that Tx_Extbase_Reflection_MethodReflection objects are returned instead of the
	 * orginal ReflectionMethod instances.
	 *
	 * @return Tx_ExtensionBuilder_Reflection_MethodReflection Method reflection object of the constructor method
	 */
	public function getConstructor() {
		$parentConstructor = parent::getConstructor();
		if (!is_object($parentConstructor)) return $parentConstructor;
		return new Tx_ExtensionBuilder_Reflection_MethodReflection($this->getName(), $parentConstructor->getName());
	}

	/**
	 * Replacement for the original getProperties() method which makes sure
	 * that Tx_Extbase_Reflection_PropertyReflection objects are returned instead of the
	 * orginal ReflectionProperty instances.
	 *
	 * @param  long $filter: A filter mask
	 * @return array of Tx_Extbase_Reflection_PropertyReflection Property reflection objects of the properties in this class
	 */
	public function getProperties($filter = NULL) {
		$extendedProperties = array();
		$properties = ($filter === NULL ? parent::getProperties() : parent::getProperties($filter));
		foreach ($properties as $property) {
			$extendedProperties[] = new Tx_ExtensionBuilder_Reflection_PropertyReflection($this->getName(), $property->getName());
		}
		return $extendedProperties;
	}

	/**
	 * Replacement for the original getProperty() method which makes sure
	 * that a Tx_Extbase_Reflection_PropertyReflection object is returned instead of the
	 * orginal ReflectionProperty instance.
	 *
	 * @param  string $name: Name of the property
	 * @return Tx_Extbase_Reflection_PropertyReflection Property reflection object of the specified property in this class
	 */
	public function getProperty($name) {
		return new Tx_ExtensionBuilder_Reflection_PropertyReflection($this->getName(), $name);
	}

	/**
	 * get all methods that are declared in parent classes and not in the actual class
	 *
	 * @return array
	 */

	public function getInheritedProperties() {
		$inheritedProperties = array();
		$parentClass = $this->getParentClass();
		if ($parentClass) {
			$inheritedProperties = $parentClass->getProperties();
		}
		return $inheritedProperties;
	}

	/**
	 * get all Properties that are declared in the actual class and not inherited (or overwrite inherited Properties)
	 *
	 * @param $includeOverridingProperties default is TRUE
	 *
	 * @return array
	 */
	public function getNotInheritedProperties($includeOverridingProperties = TRUE) {
		$parentClass = $this->getParentClass();
		if (!$parentClass) {
			return $this->getProperties();
		}
		$allProperties = $this->getProperties();
		$inheritedProperties = $this->getInheritedProperties();
		$notInheritedProperties = array();
		foreach ($allProperties as $property) {
			if (!in_array($property, $inheritedProperties)) {
				if ($includeOverridingProperties || (!$includeOverridingProperties && !$parentClass->hasProperty($property->getName()))) {
					$notInheritedProperties[] = $property;
				}
			}
		}
		return $notInheritedProperties;
	}

	/**
	 * Replacement for the original getParentClass() method which makes sure
	 * that a Tx_Extbase_Reflection_ClassReflection object is returned instead of the
	 * orginal ReflectionClass instance.
	 *
	 * @return Tx_Extbase_Reflection_ClassReflection Reflection of the parent class - if any
	 */
	public function getParentClass() {
		//$parentClass = parent::getParentClass();
		// workaround for bug #8800 in Tx_Extbase_Reflection_ClassReflection
		$phpReflectionClass = new ReflectionClass($this->getName());
		$parentClass = $phpReflectionClass->getParentClass();
		if ($parentClass) {
			return new Tx_ExtensionBuilder_Reflection_ClassReflection($parentClass->getName());
		}
		else return FALSE;
	}

	/**
	 * Checks if the doc comment of this method is tagged with
	 * the specified tag
	 *
	 * @param  string $tag: Tag name to check for
	 * @return boolean TRUE if such a tag has been defined, otherwise FALSE
	 */
	public function isTaggedWith($tag) {
		$result = $this->getDocCommentParser()->isTaggedWith($tag);
		return $result;
	}

	/**
	 * Returns an array of tags and their values
	 *
	 * @return array Tags and values
	 */
	public function getTagsValues() {
		return $this->getDocCommentParser()->getTagsValues();
	}

	/**
	 * Returns the values of the specified tag
	 * @return array Values of the given tag
	 */
	public function getTagValues($tag) {
		return $this->getDocCommentParser()->getTagValues($tag);
	}

	/**
	 * Returns an array of tags and their values
	 *
	 * @return array Tags and values
	 */
	public function getTags() {
		return $this->getDocCommentParser()->getTagsValues();
	}

	/**
	 * Returns an instance of the doc comment parser and
	 * runs the parse() method.
	 *
	 * @return Tx_Extbase_Reflection_DocCommentParser
	 */
	protected function getDocCommentParser() {
		if (!is_object($this->docCommentParser)) {
			$this->docCommentParser = new \TYPO3\CMS\Extbase\Reflection\DocCommentParser;
			$this->docCommentParser->parseDocComment($this->getDocComment());
		}
		return $this->docCommentParser;
	}

	public function getDocCommentContent() {
		$this->getDocCommentParser();
		return $this->docCommentParser->getDescription();
	}

	public function getRawComments() {
		$this->getDocCommentParser();
		return $this->docCommentParser->getRawComments($this->getFileName());
	}
}

?>
