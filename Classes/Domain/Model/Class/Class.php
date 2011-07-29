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
 * class schema representing a "class" in the context of software development
 *
 * @package ExtensionBuilder
 * @version $ID:$
 */


class Tx_ExtensionBuilder_Domain_Model_Class_Class extends Tx_ExtensionBuilder_Domain_Model_Class_AbstractObject{

	/**
	 * constants
	 * @var array
	 */
	protected $constants;

	/**
	 * properties
	 * @var array
	 */
	protected $properties = array();

	/**
	 * propertyNames - deprecated -> use this->getPropertyNames() instead
	 * @var array
	 */
	protected $propertyNames = array();


	/**
	 * methods
	 * @var array
	 */
	protected $methods = array();


	/**
	 * interfaceNames
	 * @var string
	 */
	protected $interfaceNames;

	/**
	 * all lines that were found below the class declaration
	 * @var string
	 */
	protected $appendedBlock;

	/**
	 * all includes (filenames) that were found in a file
	 * currently not used
	 * includes should be preserved by writing the blocks (preceding, appended) into the new file
	 * @var array
	 */
	protected $includes;

	/**
	 * parentClass
	 * @var string
	 */
	//protected $parent_class;


	/**
	 * isFileBased
	 * @var boolean
	 */
	protected $isFileBased = false;


	/**
	 * the path to the file this class was defined in
	 * @var string
	 */
	protected $fileName;

	/**
	 * is instantiated only if the class is imported from a file
	 * @var Tx_ExtensionBuilder_Reflection_ClassReflection
	 */
	protected $classReflection = NULL;

	/**
	 * @var object parentClass
	 */
	protected $parentClass = NULL;

	/**
	 * constructor of this class
	 * @param string $className
	 * @return unknown_type
	 */
	public function __construct($className){
		$this->name = $className;
	}

	/**
	 * Setter for a single constant
	 *
	 * @param string $constant constant
	 * @return void
	 */
	public function setConstant($constantName,$constantValue) {
		$this->constants[$constantName] = array('name' => $constantName,'value' => $constantValue);
	}

	/**
	 * Setter for constants
	 *
	 * @param string $constants constants
	 * @return void
	 */
	public function setConstants($constants) {
		$this->constants = $constants;
	}

	/**
	 * Getter for constants
	 *
	 * @return string constants
	 */
	public function getConstants() {
		return $this->constants;
	}

	/**
	 * Getter for a single constant
	 *
	 * @return mixed constant value
	 */
	public function getConstant($constantName) {
		if(isset($this->constants[$constantName])){
			return $this->constants[$constantName]['value'];
		}
		else return NULL;
	}

	/**
	 * removes a constant
	 * @param string $constantName
	 * @return boolean true (if successfull removed)
	 */
	public function removeConstant($constantName){
		if(isset($this->constants[$constantName])){
			unset($this->constants[$constantName]);
			return true;
		}
		return false;
	}

	/**
	 *
	 * @return boolean
	 */
	public function methodExists($methodName){
		if(!is_array($this->methods)){
			return false;
		}
		$methodNames = array_keys($this->methods);
		if(is_array($methodNames) && in_array($methodName,$methodNames)){
			return true;
		}
		else return false;
	}

	/**
	 * Setter for methods
	 *
	 * @param array $methods methods
	 * @return void
	 */
	public function setMethods($methods) {
		$this->methods = $methods;
	}

	/**
	 * Setter for a single method (allows to override an existing method)
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_Class_Method $method
	 * @return void
	 */
	public function setMethod(Tx_ExtensionBuilder_Domain_Model_Class_Method $classMethod) {
		$this->methods[$classMethod->getName()] = $classMethod;
	}

	/**
	 * Getter for methods
	 *
	 * @return array methods
	 */
	public function getMethods() {
		return $this->methods;
	}

	/**
	 * Getter for method
	 *
	 * @return Tx_ExtensionBuilder_Domain_Model_Class_Method
	 */
	public function getMethod($methodName) {
		if($this->methodExists($methodName)){
			return $this->methods[$methodName];
		}
		else return NULL;
	}

	/**
	 * Add a method
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_Class_Method $classMethod
	 * @return void
	 */
	public function addMethod($classMethod) {
		if(!$this->methodExists($classMethod->getName())){
			$this->methods[$classMethod->getName()] = $classMethod;
		}

	}

	/**
	 * removes a method
	 * @param string $methodName
	 * @return boolean true (if successfull removed)
	 */
	public function removeMethod($methodName){
		if($this->methodExists($methodName)){
			unset($this->methods[$methodName]);
			return true;
		}
		return false;
	}

	/**
	 * rename a method
	 * @param string $oldName
	 * @param string $newName
	 * @return boolean success
	 */
	public function renameMethod($oldName, $newName){
		if($this->methodExists($oldName)){
			$method = $this->methods[$oldName];
			$method->setName($newName);
			$this->methods[$newName] = $method;
			$this->removeMethod($oldName);
			return true;
		}
		else return false;
	}


	/**
	 * returnes all methods starting with "get"
	 * @return array an array of method objects
	 */
	public function getGetters(){
		$getterMethods = array();
		foreach($this->getMethods() as $method){
			$methodName = $method->getName();
			if(strpos($methodName,'get')===0){
				$propertyName = strtolower(substr($methodName,3));
				if($this->propertyExists($propertyName)){
					$getterMethods[$propertyName] = $method;
				}
			}
		}

		return $getterMethods;
	}

	/**
	 * returnes all methods starting with "set"
	 * @return array an array of method objects
	 */
	public function getSetters(){
		$setterMethods = array();
		foreach($this->getMethods() as $method){
			$methodName = $method->getName();
			if(strpos($methodName,'set')===0){
				$propertyName = strtolower(substr($methodName,3));
				if($this->propertyExists($propertyName)){
					$setterMethods[$propertyName] = $method;
				}
			}
		}
		return $setterMethods;
	}



	/**
	 * Getter for property
	 * @param $propertyName the name of the property
	 * @return Tx_ExtensionBuilder_Reflection_PropertyReflection
	 */
	public function getProperty($propertyName) {
		if($this->propertyExists($propertyName)){
			return $this->properties[$propertyName];
		}
		else return NULL;
	}

	/**
	 * Setter for properties
	 *
	 * @param select $properties properties
	 * @return void
	 */
	public function setProperties($properties) {
		$this->properties = $properties;
	}

	/**
	 * Getter for properties
	 *
	 * @return select properties
	 */
	public function getProperties() {
		return $this->properties;
	}

	/**
	 * removes a property
	 * @param string $propertyName
	 * @return boolean true (if successfull removed)
	 */
	public function removeProperty($propertyName){
		if($this->propertyExists($propertyName)){
			unset($this->properties[$propertyName]);
			return true;
		}
		return false;
	}

	/**
	 * rename a property
	 * @param string $oldName
	 * @param string $newName
	 * @return boolean success
	 */
	public function renameProperty($oldName, $newName){
		if($this->propertyExists($oldName)){
			$property = $this->properties[$oldName];
			$property->setName($newName);
			$this->properties[$newName] = $property;
			$this->removeProperty($oldName);
			return true;
		}
		else return false;
	}

	/**
	 *
	 * @param string $propertyName
	 * @param array $tag
	 */
	public function setPropertyTag($propertyName,$tag){
		if($this->propertyExists($propertyName)){
			$this->properties[$propertyName]->setTag($tag['name'],$tag['value']);
		}
	}
	/**
	 * Setter for staticProperties
	 *
	 * @param string $staticProperties staticProperties
	 * @return void
	 */
	public function setStaticProperties($staticProperties) {
		$this->staticProperties = $staticProperties;
	}

	/**
	 * Getter for staticProperties
	 *
	 * @return string staticProperties
	 */
	public function getStaticProperties() {
		return $this->staticProperties;
	}



	/**
	 *
	 * @return boolean
	 */
	public function propertyExists($propertyName){
		$propertyNames = $this->getPropertyNames();
		if(!is_array($this->methods)){
			return false;
		}
		if(in_array($propertyName,$this->getPropertyNames())){
			return true;
		}
		else return false;
	}

	/**
	 * add a property (returns true if successfull added)
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_Class_Property
	 * @return boolean success
	 */
	public function addProperty(Tx_ExtensionBuilder_Domain_Model_Class_Property $classProperty) {
		if(!$this->propertyExists($classProperty->getName())){
			$this->propertyNames[] = $classProperty->getName();
			$this->properties[$classProperty->getName()] = $classProperty;
		}
		else return false;
	}

	/**
	 * returns all property names
	 * @return array
	 */
	public function getPropertyNames(){
		return array_keys($this->properties);
	}
	/**
	 * Setter for property
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_Class_Property
	 * @return boolean success
	 */
	public function setProperty($classProperty) {
		$this->properties[$classProperty->getName()] = $classProperty;
	}


	/**
	 * Setter for interfaceNames
	 *
	 * @param string $interfaceNames interfaceNames
	 * @return void
	 */
	public function setInterfaceNames($interfaceNames) {
		$this->interfaceNames = $interfaceNames;
	}

	/**
	 * Getter for interfaceNames
	 *
	 * @return string interfaceNames
	 */
	public function getInterfaceNames() {
		return $this->interfaceNames;
	}


	/**
	 * Setter for parentClass
	 *
	 * @param string $parentClass parentClass
	 * @return void
	 */
	public function setParentClass($parentClass) {
		$this->parentClass = $parentClass;
	}

	/**
	 * Getter for parentClass
	 *
	 * @return string parentClass
	 */
	public function getParentClass() {
		return $this->parentClass;
	}


	/**
	 * Setter for includes
	 *
	 * @param array $includes
	 * @return void
	 */
	public function setIncludes($includes) {
		$this->includes = $includes;
	}

	/**
	 * Getter for includes
	 *
	 * @return array includes
	 */
	public function getIncludes() {
		return $this->includes;
	}

	/**
	 *
	 * @param $fileName
	 * @return void
	 */
	public function addInclude($fileName){
		//TODO make some checks... allowed file?
		$this->includes[] = $fileName;
	}

	/**
	 * Getter for fileName
	 *
	 * @return string fileName
	 */
	public function getFileName() {
		return $this->fileName;
	}

	/**
	 * Setter for fileName
	 * @param string $fileName
	 * @return void
	 */
	public function setFileName($fileName) {
		$this->fileName = $fileName;
	}

	/**
	 * getter for appendedBlock
	 * @return string $appendedBlock
	 */
	public function getAppendedBlock(){
		return $this->appendedBlock;
	}

	/**
	 * setter for appendedBlock
	 * @param string $appendedBlock
	 * @return void
	 */
	public function setAppendedBlock($appendedBlock){
		$this->appendedBlock = $appendedBlock;
	}

	public function getInfo(){
		$infoArray = array();
		$infoArray['className'] = $this->getName();
		$infoArray['fileName'] = $this->getFileName();

		$methodArray  = array();
		foreach( $this->getMethods() as $method){
			$methodArray[$method->getName()] = array('parameter'=>$method->getParameters());
			//'body'=>$method->getBody()
		}
		$infoArray['Methods'] = $methodArray;
		//$infoArray['Inherited Methods'] = count($this->getInheritedMethods());
		//$infoArray['Not inherited Methods'] = count($this->getNotInheritedMethods());
		$infoArray['Properties'] = $this->getProperties();
		//$infoArray['Inherited Properties'] = count($this->getInheritedProperties());
		//$infoArray['Not inherited Properties'] = count($this->getNotInheritedProperties());
		$infoArray['Constants'] = $this->getConstants();
		$infoArray['Includes'] = $this->getIncludes();
		$infoArray['Modifiers'] = $this->getModifierNames();
		$infoArray['Tags'] = $this->getTags();
		//$infoArray['Methods'] = count($this->getMethods());
		return $infoArray;
	}

}

?>