<?php
namespace EBT\ExtensionBuilder\Domain\Model\ClassObject;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Nico de Haen
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
use EBT\ExtensionBuilder\Domain\Model\AbstractObject;

/**
 * Class schema representing a "PHP class" in the context of software development
 */
class ClassObject extends AbstractObject {

	/**
	 * @var array
	 */
	protected $constants = array();

	/**
	 * @var \EBT\ExtensionBuilder\Domain\Model\ClassObject\Property[]
	 */
	protected $properties = array();

	/**
	 * @deprecated Use this->getPropertyNames() instead
	 * @var string[]
	 */
	protected $propertyNames = array();


	/**
	 * @var \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method[]
	 */
	protected $methods = array();


	/**
	 * @var string[]
	 */
	protected $interfaceNames = array();

	/**
	 * All lines that were found below the class declaration.
	 *
	 * @var string
	 */
	protected $appendedBlock = '';

	/**
	 * @var array
	 */
	protected $aliasDeclarations = array();

	/**
	 * @var bool
	 */
	protected $isFileBased = FALSE;

	/**
	 * the path to the file this class was defined in
	 *
	 * @var string
	 */
	protected $fileName = '';

	/**
	 * @var \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
	 */
	protected $parentClass = NULL;

	/**
	 * @var string
	 */
	protected $parentClassName = NULL;

	/**
	 * @var bool
	 */
	protected $isTemplate = FALSE;

	/**
	 * @param string $name
	 */
	public function __construct($name) {
		$this->name = $name;
	}

	/**
	 * @return void
	 */
	public function __clone() {
		$clonedProperties = array();
		$clonedMethods = array();
		foreach ($this->properties as $property) {
			$clonedProperties[] = clone($property);
		}
		$this->properties = $clonedProperties;
		foreach ($this->methods as $method) {
			$clonedMethods[] = clone($method);
		}
		$this->methods = $clonedMethods;
	}

	/**
	 * @param string $constantName
	 * @param string $constantValue
	 * @return void
	 */
	public function setConstant($constantName, $constantValue) {
		$this->constants[$constantName] = $constantValue;
	}

	/**
	 * @param array $constants
	 * @return void
	 */
	public function setConstants($constants) {
		$this->constants = $constants;
	}

	/**
	 * @return array
	 */
	public function getConstants() {
		return $this->constants;
	}

	/**
	 * @param string $constantName
	 * @return mixed
	 */
	public function getConstant($constantName) {
		if (isset($this->constants[$constantName])) {
			$result = $this->constants[$constantName];
		} else {
			$result = NULL;
		}

		return $result;
	}

	/**
	 * @param string $constantName
	 * @return bool TRUE if successfully removed
	 */
	public function removeConstant($constantName) {
		if (isset($this->constants[$constantName])) {
			unset($this->constants[$constantName]);
			$result = TRUE;
		} else {
			$result = FALSE;
		}

		return $result;
	}

	/**
	 * @param string $methodName
	 * @return bool
	 */
	public function methodExists($methodName) {
		if (!is_array($this->methods)) {
			$result = FALSE;
		} else {
			$methodNames = array_keys($this->methods);

			if (is_array($methodNames) && in_array($methodName, $methodNames)) {
				$result = TRUE;
			} else {
				$result = FALSE;
			}
		}

		return $result;
	}

	/**
	 * @param array $methods
	 * @return void
	 */
	public function setMethods(array $methods) {
		$this->methods = $methods;
	}

	/**
	 * Allows to override an existing method.
	 *
	 * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method $classMethod
	 * @return void
	 */
	public function setMethod(Method $classMethod) {
		$this->methods[$classMethod->getName()] = $classMethod;
	}

	/**
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method[]
	 */
	public function getMethods() {
		return $this->methods;
	}

	/**
	 * @param string $methodName
	 * @return NULL|Method
	 */
	public function getMethod($methodName) {
		if ($this->methodExists($methodName)) {
			$result = $this->methods[$methodName];
		} else {
			$result = NULL;
		}

		return $result;
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method $classMethod
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
	 */
	public function addMethod($classMethod) {
		if (!$this->methodExists($classMethod->getName())) {
			$this->methods[$classMethod->getName()] = $classMethod;
		}
		return $this;
	}

	/**
	 * @param string $methodName
	 * @return bool TRUE if successfully removed
	 */
	public function removeMethod($methodName) {
		if ($this->methodExists($methodName)) {
			unset($this->methods[$methodName]);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @param string $oldName
	 * @param string $newName
	 * @return bool TRUE if successfully renamed
	 */
	public function renameMethod($oldName, $newName) {
		if ($this->methodExists($oldName)) {
			$method = $this->methods[$oldName];
			$method->setName($newName);
			$this->methods[$newName] = $method;
			$this->removeMethod($oldName);
			$result = TRUE;
		} else {
			$result = FALSE;
		}

		return $result;
	}


	/**
	 * Returns all methods starting with "get".
	 *
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method[]
	 */
	public function getGetters() {
		$getterMethods = array();
		foreach ($this->getMethods() as $method) {
			$methodName = $method->getName();
			if (strpos($methodName, 'get') === 0) {
				$propertyName = strtolower(substr($methodName, 3));
				if ($this->propertyExists($propertyName)) {
					$getterMethods[$propertyName] = $method;
				}
			}
		}

		return $getterMethods;
	}

	/**
	 * Returns all methods starting with "set".
	 *
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method[]
	 */
	public function getSetters() {
		$setterMethods = array();
		foreach ($this->getMethods() as $method) {
			$methodName = $method->getName();
			if (strpos($methodName, 'set') === 0) {
				$propertyName = strtolower(substr($methodName, 3));
				if ($this->propertyExists($propertyName)) {
					$setterMethods[$propertyName] = $method;
				}
			}
		}
		return $setterMethods;
	}


	/**
	 * @param string $propertyName
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Property
	 */
	public function getProperty($propertyName) {
		if ($this->propertyExists($propertyName)) {
			if ($this->isTemplate) {
				$propertyTemplate = clone($this->properties[$propertyName]);
				$propertyTemplate->setIsTemplate(TRUE);
				$result = $propertyTemplate;
			} else {
				$result = $this->properties[$propertyName];
			}
		} else {
			$result = NULL;
		}

		return $result;
	}

	/**
	 * @param array $properties
	 * @return void
	 */
	public function setProperties($properties) {
		$this->properties = $properties;
	}

	/**
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Property[]
	 */
	public function getProperties() {
		return $this->properties;
	}

	/**
	 * @param string $propertyName
	 * @return bool TRUE if successfully removed
	 */
	public function removeProperty($propertyName) {
		if ($this->propertyExists($propertyName)) {
			unset($this->properties[$propertyName]);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @param string $oldName
	 * @param string $newName
	 * @return bool TRUE if successfully renamed
	 */
	public function renameProperty($oldName, $newName) {
		if ($this->propertyExists($oldName)) {
			$property = $this->properties[$oldName];
			$property->setName($newName);
			$this->properties[$newName] = $property;
			$this->removeProperty($oldName);
			$result = TRUE;
		} else {
			$result = FALSE;
		}

		return $result;
	}

	/**
	 * @param string $propertyName
	 * @param array $tag
	 * @return void
	 */
	public function setPropertyTag($propertyName, $tag) {
		if ($this->propertyExists($propertyName)) {
			$this->properties[$propertyName]->setTag($tag['name'], $tag['value']);
		}
	}


	/**
	 * @param string $propertyName
	 * @return bool
	 */
	public function propertyExists($propertyName) {
		if (!is_array($this->methods)) {
			$result = FALSE;
		} else {
			if (in_array($propertyName, $this->getPropertyNames())) {
				$result = TRUE;
			} else {
				$result = FALSE;
			}
		}

		return $result;
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\Property $classProperty
	 * @return bool TRUE if successfull added
	 */
	public function addProperty(Property $classProperty) {
		if (!$this->propertyExists($classProperty->getName())) {
			$this->propertyNames[] = $classProperty->getName();
			$this->properties[$classProperty->getName()] = $classProperty;
			$result = TRUE;
		} else {
			$result = FALSE;
		}

		return $result;
	}

	/**
	 * @return array
	 */
	public function getPropertyNames() {
		return array_keys($this->properties);
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\Property $classProperty
	 * @return void
	 */
	public function setProperty($classProperty) {
		$this->properties[$classProperty->getName()] = $classProperty;
	}


	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $parentClass
	 * @return void
	 */
	public function setParentClass(ClassObject $parentClass) {
		$this->parentClass = $parentClass;
	}

	/**
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
	 */
	public function getParentClass() {
		return $this->parentClass;
	}

	/**
	 * @return string
	 */
	public function getFileName() {
		return $this->fileName;
	}

	/**
	 * @param string $fileName
	 * @return void
	 */
	public function setFileName($fileName) {
		$this->fileName = $fileName;
	}

	/**
	 * @return string $appendedBlock
	 */
	public function getAppendedBlock() {
		return $this->appendedBlock;
	}

	/**
	 * @param string $appendedBlock
	 * @return void
	 */
	public function setAppendedBlock($appendedBlock) {
		$this->appendedBlock = $appendedBlock;
	}

	/**
	 * @return array
	 */
	public function getInfo() {
		$infoArray = array();
		$infoArray['className'] = $this->getName();
		$infoArray['nameSpace'] = $this->getNamespaceName();
		$infoArray['parentClass'] = $this->getParentClassName();
		$infoArray['fileName'] = $this->getFileName();

		$methodArray = array();
		foreach ($this->getMethods() as $method) {
			$methodArray[$method->getName()] = array(
				'parameter' => $method->getParameters()
			);
		}
		$infoArray['Methods'] = $methodArray;
		$infoArray['Properties'] = $this->getProperties();
		$infoArray['Constants'] = $this->getConstants();
		$infoArray['Modifiers'] = $this->getModifierNames();
		$infoArray['Tags'] = $this->getTags();

		return $infoArray;
	}

	/**
	 * @param $alias
	 * @return void
	 */
	public function addAliasDeclaration($alias) {
		if (!in_array($alias, $this->aliasDeclarations)) {
			$this->aliasDeclarations[] = $alias;
		}
	}

	/**
	 * @return array
	 */
	public function getAliasDeclarations() {
		return $this->aliasDeclarations;
	}

	/**
	 * @param array $interfaceNames
	 * @return void
	 */
	public function setInterfaceNames($interfaceNames) {
		$this->interfaceNames = $interfaceNames;
	}

	/**
	 * @return array
	 */
	public function getInterfaceNames() {
		return $this->interfaceNames;
	}

	/**
	 * @param string $interfaceName
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
	 */
	public function addInterfaceName($interfaceName) {
		if (!in_array($interfaceName, $this->interfaceNames)) {
			$this->interfaceNames[] = $interfaceName;
		}
		return $this;
	}

	/**
	 * @param string $interfaceName
	 * @return bool
	 */
	public function hasInterface($interfaceName) {
		return in_array($interfaceName, $this->interfaceNames);
	}

	/**
	 * @param $interfaceNameToRemove
	 * @return void
	 */
	public function removeInterface($interfaceNameToRemove) {
		$interfaceNames = array();
		foreach ($this->interfaceNames as $interfaceName) {
			if ($interfaceName != $interfaceNameToRemove) {
				$interfaceNames[] = $interfaceName;
			}
		}
		$this->interfaceNames = $interfaceNames;
	}

	/**
	 * @return void
	 */
	public function removeAllInterfaces() {
		$this->interfaceNames = array();
	}

	/**
	 * @param string $parentClassName
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
	 */
	public function setParentClassName($parentClassName) {
		$this->parentClassName = $parentClassName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getParentClassName() {
		return $this->parentClassName;
	}

	/**
	 * @return void
	 */
	public function removeParentClassName() {
		$this->parentClassName = '';
	}

	/**
	 * @return void
	 */
	public function resetAll() {
		$this->constants = array();
		$this->properties = array();
		$this->methods = array();
	}

}
