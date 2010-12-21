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
 * Builds the required class objects for extbase extensions
 *
 * @package ExtbaseKickstarter
 */

class Tx_ExtbaseKickstarter_Service_ClassBuilder implements t3lib_Singleton {
	
	/**
	 * The current class object 
	 * @var Tx_ExtbaseKickstarter_Domain_Model_Class
	 */
	protected $classObject = NULL;
	
	/**
	 * @var Tx_ExtbaseKickstarter_Utility_ClassParser
	 */
	protected $classParser;	
	
	/**
	 * @var Tx_ExtbaseKickstarter_Service_RoundTrip
	 */
	protected $roundTripService;		
	
	
	/**
	 * This line is added to the constructor if there are storage objects to initialize
	 * @var string
	 */
	protected $initStorageObjectCall = "//Do not remove the next line: It would break the functionality\n\$this->initStorageObjects();";
	
	
	
	/**
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_Extension $extension
	 * @return void
	 */
	public function initialize(Tx_ExtbaseKickstarter_Domain_Model_Extension $extension){
		$this->extension = $extension;
		$this->extensionDirectory = $this->extension->getExtensionDir();
		$this->extClassPrefix = 'Tx_' . Tx_Extbase_Utility_Extension::convertLowerUnderscoreToUpperCamelCase($this->extension->getExtensionKey());
		if (!$this->roundTripService instanceof Tx_ExtbaseKickstarter_Service_RoundTrip) {
			$this->injectRoundtripService(t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Service_RoundTrip'));
			$this->injectClassParser(t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Utility_ClassParser'));
			$frameworkConfiguration = Tx_Extbase_Dispatcher::getExtbaseFrameworkConfiguration();
			$this->settings = $frameworkConfiguration['settings'];
		}
		$this->settings = array_merge($this->settings,Tx_ExtbaseKickstarter_Service_RoundTrip::getExtConfiguration());
		$this->roundTripService->initialize($this->extension);
	}
	
	/**
	 * @param Tx_ExtbaseKickstarter_Service_RoundTrip $roundTripService
	 * @return void
	 */
	public function injectRoundtripService(Tx_ExtbaseKickstarter_Service_RoundTrip $roundTripService) {
		$this->roundTripService = $roundTripService;
	}
	
	/**
	 * @param Tx_ExtbaseKickstarter_Utility_ClassParser $classParser
	 * @return void
	 */
	public function injectClassParser(Tx_ExtbaseKickstarter_Utility_ClassParser $classParser) {
		$this->classParser = $classParser;
	}	

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManager $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
		$this->settings = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
	}	
	
	/**
	 * This method generates the class schema object, which is passed to the template
	 * it keeps all methods and properties including user modified method bodies and comments 
	 * needed to create a domain object class file
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject
	 * @return 
	 */
	public function generateModelClassObject($domainObject){
		t3lib_div::devlog('------------------------------------- generateModelClassObject('.$domainObject->getName().') ---------------------------------','extbase_kickstarter',1);
		$this->classObject = NULL;
		
		$className = $domainObject->getClassName();
		// is there already a class file? 
		$classPath = 'Classes/Domain/Model/' . $domainObject->getName() . '.php';
		
		if( Tx_ExtbaseKickstarter_Service_RoundTrip::getOverWriteSettingForPath($classPath,$this->extension) != 0){
			try {
				$this->classObject = $this->roundTripService->getDomainModelClass($domainObject);
			}
			catch(Exception $e){
				t3lib_div::devLog('Class '.$className.' could not be imported: '.$e->getMessage(), 'extbase_kickstarter',2);		
			}
		}
		else t3lib_div::devlog('Class:'.$classPath.' : '.Tx_ExtbaseKickstarter_Service_RoundTrip::getOverWriteSettingForPath($classPath,$this->extension),'extbase_kickstarter',2);
		
		if($this->classObject == NULL) {
			return NULL;
		}
		
		if(!$this->classObject->hasDescription()){
			$this->classObject->setDescription($domainObject->getDescription());
		}
		
		
		
		$anyToManyRelationProperties = $domainObject->getAnyToManyRelationProperties();
		
		if(!$this->classObject->methodExists('__construct')){
			$constructorMethod = new Tx_ExtbaseKickstarter_Domain_Model_Class_Method('__construct');
			$constructorMethod->setDescription('The constructor of this '.$domainObject->getName());
			if(count($anyToManyRelationProperties) > 0){
				$constructorMethod->setBody($this->initStorageObjectCall);
			}
			$constructorMethod->addModifier('public');
			$constructorMethod->setTag('return','void');
			$this->classObject->addMethod($constructorMethod);
		}
		else if(count($anyToManyRelationProperties) > 0){
			$constructorMethod = $this->classObject->getMethod('__construct');
			if(preg_match('/\$this->initStorageObjects()/',$constructorMethod->getBody()) < 1){
				t3lib_div::devLog('Constructor method in Class '. $this->classObject->getName().' was overwritten since the initStorageObjectCall was missing', 'extbase_kickstarter',2,array('Original method'=>$constructorMethod->getBody()));		
				$constructorMethod->setBody($this->initStorageObjectCall);
				$this->classObject->setMethod($constructorMethod);
				
			}
			//initStorageObjects
		}
		else {
		}
		
		if(count($anyToManyRelationProperties) > 0){
			$initStorageObjectsMethod = new Tx_ExtbaseKickstarter_Domain_Model_Class_Method('initStorageObjects');
			$initStorageObjectsMethod->setDescription('Initializes all Tx_Extbase_Persistence_ObjectStorage properties.');
			$methodBody = "/**\n* Do not modify this method!\n* It will be rewritten on each save in the kickstarter\n* You may modify the constructor of this class instead\n*/\n";
			foreach($anyToManyRelationProperties as $relationProperty){
				$methodBody .= "\$this->".$relationProperty->getName()." = new Tx_Extbase_Persistence_ObjectStorage();\n";
			}
			$initStorageObjectsMethod->setBody($methodBody);
			$initStorageObjectsMethod->addModifier('protected');
			$initStorageObjectsMethod->setTag('return','void');
			$this->classObject->setMethod($initStorageObjectsMethod);
		}
		else if($this->classObject->methodExists('initStorageObjects')){
			$this->classObject->getMethod('initStorageObjects')->setBody('// empty');
		}
		//TODO the following part still needs some enhancement: 
		//what should be obligatory in existing properties and methods
		foreach ($domainObject->getProperties() as $domainProperty) {
			$propertyName = $domainProperty->getName();
			// add the property to class Object (or update an existing class Object property)
			if($this->classObject->propertyExists($propertyName)){
				$classProperty = $this->classObject->getProperty($propertyName);
				$classPropertyTags = $classProperty->getTags();
			}
			else {
				$classProperty = new Tx_ExtbaseKickstarter_Domain_Model_Class_Property($propertyName);
				$classProperty->setTag('var',$domainProperty->getTypeForComment());
				$classProperty->addModifier('protected');
				t3lib_div::devLog('New property: '.$propertyName.':'.$domainProperty->getTypeForComment(), 'extbase_kickstarter',1);
			}
			
			$classProperty->setAssociatedDomainObjectProperty($domainProperty);
			
			$this->classObject->setProperty($classProperty);
			
			$this->setPropertyRelatedMethods($domainProperty);
		}
		// t3lib_div::devlog('Methods before sorting','extbase_kickstarter',0,array_keys($this->classObject->getMethods()));
		//$this->sortMethods($domainObject);
		return $this->classObject;
	}
	
	/**
	 * add all setter/getter/adder etc. methods
	 * @param Tx_ExtbaseKickstarter_Domain_Model_AbstractDomainObjectProperty $domainProperty
	 */
	protected function setPropertyRelatedMethods($domainProperty){
		t3lib_div::devlog('setPropertyRelatedMethods:'.$domainProperty->getName(),'extbase_kickstarter',1);
		if (is_subclass_of($domainProperty, 'Tx_ExtbaseKickstarter_Domain_Model_Property_Relation_AnyToManyRelation')) {
			t3lib_div::devlog('setPropertyAddMethods:'.$domainProperty->getName(),'extbase_kickstarter',1);
			$addMethod = $this->buildAddMethod($domainProperty);
			$removeMethod = $this->buildRemoveMethod($domainProperty);
			$this->classObject->setMethod($addMethod);
			$this->classObject->setMethod($removeMethod);
		}
		$getMethod = $this->buildGetterMethod($domainProperty);
		$setMethod = $this->buildSetterMethod($domainProperty);
		$this->classObject->setMethod($getMethod);
		$this->classObject->setMethod($setMethod);
		if ($domainProperty->getTypeForComment() == 'boolean'){
			$isMethod = $this->buildIsMethod($domainProperty);
			$this->classObject->setMethod($isMethod);
		}
	}
	
	
	/**
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_AbstractDomainObjectProperty $domainProperty
	 */
	protected function buildGetterMethod($domainProperty){
		
		// add (or update) a getter method
		$getterMethodName = $this->getMethodName($domainProperty,'get');
		if($this->classObject->methodExists($getterMethodName)){
			$getterMethod = $this->classObject->getMethod($getterMethodName);
			$getterMethodTags = $getterMethod->getTags();
		}
		else {
			t3lib_div::devlog('new getMethod:'.$getterMethodName,'extbase_kickstarter',1);
			$getterMethod = new Tx_ExtbaseKickstarter_Domain_Model_Class_Method($getterMethodName);
			// default method body
			$getterMethod->setBody($this->getDefaultMethodBody($domainProperty,'get'));
			$getterMethod->setTag('return',$domainProperty->getTypeForComment().' $'.$domainProperty->getName());
			$getterMethod->addModifier('public');
		}
		if(!$getterMethod->hasDescription()){
			$getterMethod->setDescription('Returns the '.$domainProperty->getName());
		}
		return $getterMethod;
	}
	
	/**
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_AbstractDomainObjectProperty $domainProperty
	 */
	protected function buildSetterMethod($domainProperty){
		
		$propertyName = self::getParameterName($domainProperty, 'set');
		// add (or update) a setter method
		$setterMethodName = $this->getMethodName($domainProperty,'set');
		if($this->classObject->methodExists($setterMethodName)){
			$setterMethod = $this->classObject->getMethod($setterMethodName);
			$setterMethodTags = $setterMethod->getTags();
		}
		else {
			t3lib_div::devlog('new setMethod:'.$setterMethodName,'extbase_kickstarter',1);
			$setterMethod = new Tx_ExtbaseKickstarter_Domain_Model_Class_Method($setterMethodName);
			// default method body
			$setterMethod->setBody($this->getDefaultMethodBody($domainProperty,'set'));
			$setterMethod->setTag('param',$domainProperty->getTypeForComment().' $'.$propertyName);
			$setterMethod->setTag('return','void');
			$setterMethod->addModifier('public');
		}
		if(!$setterMethod->hasDescription()){
			$setterMethod->setDescription('Sets the '.$propertyName);
		}
		$setterParameters = $setterMethod->getParameterNames();
		if(!in_array($propertyName,$setterParameters)){
			$setterParameter = new Tx_ExtbaseKickstarter_Domain_Model_Class_MethodParameter($propertyName);
			$setterParameter->setVarType($domainProperty->getTypeForComment());
			$setterMethod->setParameter($setterParameter);
		}
		return $setterMethod;
	}
	
	/**
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_AbstractDomainObjectProperty $domainProperty
	 */
	protected function buildAddMethod($domainProperty){

		$propertyName = $domainProperty->getName();
		
		$addMethodName = $this->getMethodName($domainProperty,'add');
		
		if($this->classObject->methodExists($addMethodName)){
			$addMethod = $this->classObject->getMethod($addMethodName);
		}
		else {
			t3lib_div::devlog('new addMethod:'.$addMethodName,'extbase_kickstarter',1);
			$addMethod = new Tx_ExtbaseKickstarter_Domain_Model_Class_Method($addMethodName);
			// default method body
			$addMethod->setBody($this->getDefaultMethodBody($domainProperty,'add'));
			$addMethod->setTag('param',$domainProperty->getForeignClass()->getClassName().' $'.self::getParameterName($domainProperty,'add'));
			$addMethod->setTag('return','void');
			$addMethod->addModifier('public');
		}
		$addParameters = $addMethod->getParameterNames();
	
		if(!in_array(Tx_ExtbaseKickstarter_Utility_Inflector::singularize($propertyName),$addParameters)){
			$addParameter = new Tx_ExtbaseKickstarter_Domain_Model_Class_MethodParameter(self::getParameterName($domainProperty,'add'));
			$addParameter->setVarType($domainProperty->getForeignClass()->getClassName());
			$addParameter->setTypeHint($domainProperty->getForeignClass()->getClassName());
			$addMethod->setParameter($addParameter);
		}
		if(!$addMethod->hasDescription()){
			$addMethod->setDescription('Adds a '.ucfirst($domainProperty->getForeignClass()->getName()));
		}
		return $addMethod;
	}
	
	/**
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_AbstractDomainObjectProperty $domainProperty
	 */
	protected function buildRemoveMethod($domainProperty){
		
		$propertyName = $domainProperty->getName();
		
		$removeMethodName = $this->getMethodName($domainProperty,'remove');
		
		if($this->classObject->methodExists($removeMethodName)){
			$removeMethod = $this->classObject->getMethod($removeMethodName);
		}
		else {
			t3lib_div::devlog('new removeMethod:'.$removeMethodName,'extbase_kickstarter',1);
			$removeMethod = new Tx_ExtbaseKickstarter_Domain_Model_Class_Method($removeMethodName);
			// default method body
			$removeMethod->setBody($this->getDefaultMethodBody($domainProperty,'remove'));
			$removeMethod->setTag('param',$domainProperty->getForeignClass()->getClassName().' $'.self::getParameterName($domainProperty, 'remove').' The '.$domainProperty->getForeignClass()->getName().' to be removed');
			$removeMethod->setTag('return','void');
			$removeMethod->addModifier('public');
		}
		
		$removeParameters = $removeMethod->getParameterNames();
		
		if(!in_array(self::getParameterName($domainProperty, 'remove'),$removeParameters)){
			$removeParameter = new Tx_ExtbaseKickstarter_Domain_Model_Class_MethodParameter(self::getParameterName($domainProperty, 'remove'));
			$removeParameter->setVarType($domainProperty->getForeignClass()->getClassName());
			$removeParameter->setTypeHint($domainProperty->getForeignClass()->getClassName());
			$removeMethod->setParameter($removeParameter);
		}
		
		if(!$removeMethod->hasDescription()){
			$removeMethod->setDescription('Removes a '.ucfirst($domainProperty->getForeignClass()->getName()));
		}
		return $removeMethod;
	}
	
	/**
	 * Builds a method that checks the current boolean state of a property
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_AbstractDomainObjectProperty $domainProperty
	 */
	protected function buildIsMethod($domainProperty){
		
		$isMethodName = $this->getMethodName($domainProperty,'is');
		
		if($this->classObject->methodExists($isMethodName)){
			$isMethod = $this->classObject->getMethod($isMethodName);
		}
		else {
			t3lib_div::devlog('new isMethod:'.$isMethodName,'extbase_kickstarter',1);
			$isMethod = new Tx_ExtbaseKickstarter_Domain_Model_Class_Method($isMethodName);
			// default method body
			$isMethod->setBody($this->getDefaultMethodBody($domainProperty,'is'));
			$isMethod->setTag('return','boolean');
			$isMethod->addModifier('public');
		}
		
		if(!$isMethod->hasDescription()){
			$isMethod->setDescription('Returns the boolean state of '.$domainProperty->getName());
		}
		return $isMethod;
	}
	
	/**
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_AbstractDomainObjectProperty $property
	 * @param string $methodPrefix (get,set,add,remove,is)
	 * @return string method name
	 */
	public static function getMethodName($domainProperty, $methodPrefix){
		$propertyName = $domainProperty->getName();
		switch($methodPrefix){
			case 'set'		: return 'set'.ucfirst($propertyName);
			
			case 'get'		: return 'get'.ucfirst($propertyName);
			
			case 'add'		: return 'add'.ucfirst(Tx_ExtbaseKickstarter_Utility_Inflector::singularize($propertyName));
			
			case 'remove'	: return 'remove'.ucfirst(Tx_ExtbaseKickstarter_Utility_Inflector::singularize($propertyName));
			
			case 'is'		: return 'is'.ucfirst($propertyName);
		}
	}
	
	/**
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_AbstractDomainObjectProperty $property
	 * @param string $methodType (get,set,add,remove,is)
	 * @return string method body
	 */
	public static function getDefaultMethodBody($domainProperty, $methodType){
		
		$propertyName = $domainProperty->getName();
		
		switch($methodType){
			
			case 'set'			: return "\$this->".$propertyName." = \$".self::getParameterName($domainProperty, $methodType).";\n";
			
			case 'get'			: return "return \$this->".$propertyName.";\n";
			
			case 'add'			: return "\$this->".$propertyName."->attach(\$".self::getParameterName($domainProperty, $methodType).");\n";
			
			case 'remove'		: return "\$this->".$propertyName."->detach(\$".self::getParameterName($domainProperty, $methodType).");";
			
			case 'is'			: return "return \$this->get".ucfirst($propertyName)."();\n";
			
		}
	}
	
	/**
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_AbstractDomainObjectProperty $property
	 * @param string $methodType (set,add,remove)
	 * @return string method body
	 */
	public static function getParameterName($domainProperty, $methodType){
		
		$propertyName = $domainProperty->getName();
		
		switch($methodType){
			
			case 'set'			: return $propertyName;
			
			case 'add'			: return Tx_ExtbaseKickstarter_Utility_Inflector::singularize($propertyName);
			
			case 'remove'		: return Tx_ExtbaseKickstarter_Utility_Inflector::singularize($propertyName).'ToRemove';
		}
	}
	
	public static function getDefaultInitializeMethodBody($domainObject){
		return '$this->'.t3lib_div::lcfirst($domainObject->getName()).'Repository = t3lib_div::makeInstance('.$omainObject->getDomainRepositoryClassName().');';
	}
	/**
	 * This method generates the class object, which is passed to the template
	 * it keeps all methods and properties including user modified method bodies and 
	 * comments that are required to create a controller class file
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject
	 * @return 
	 */
	public function generateControllerClassObject($domainObject){
		t3lib_div::devlog('------------------------------------- generateControllerClassObject('.$domainObject->getName().') ---------------------------------','extbase_kickstarter',1);

		$this->classObject = NULL;
		
		$className =  $domainObject->getControllerName();
		$classPath = 'Classes/Controller/' . $domainObject->getName() . 'Controller.php';
		
		if(Tx_ExtbaseKickstarter_Service_RoundTrip::getOverWriteSettingForPath($classPath,$this->extension) != 0){
			try {
				$this->classObject = $this->roundTripService->getControllerClass($domainObject);
			}
			catch(Exception $e){
				t3lib_div::devLog('Class '.$className.' could not be imported: '.$e->getMessage(), 'extbase_kickstarter');		
			}				
		}
		if($this->classObject == NULL) {
			return NULL;
		}
		
		if($domainObject->isAggregateRoot()){
			$propertyName = t3lib_div::lcfirst($domainObject->getName()).'Repository';
			//$domainObject->getDomainRepositoryClassName();
			// now add the property to class Object (or update an existing class Object property)
			if(!$this->classObject->propertyExists($propertyName)){
				$classProperty = new Tx_ExtbaseKickstarter_Domain_Model_Class_Property($propertyName);
				$classProperty->setTag('var',$domainObject->getDomainRepositoryClassName());
				$classProperty->addModifier('protected');
				$this->classObject->setProperty($classProperty);
			}
			$initializeMethodName = 'initializeAction';
			if(!$this->classObject->methodExists($initializeMethodName)){
				$initializeMethod = new Tx_ExtbaseKickstarter_Domain_Model_Class_Method($initializeMethodName);
				$initializeMethod->setDescription('Initializes the current action');
				$initializeMethod->setBody('$this->'.t3lib_div::lcfirst($domainObject->getName()).'Repository = t3lib_div::makeInstance('.$domainObject->getDomainRepositoryClassName().');');
				$initializeMethod->setTag('return','void');
				$initializeMethod->addModifier('public');
				$this->classObject->addMethod($initializeMethod);
			}
		}
		
		foreach($domainObject->getActions() as $action){
			$actionMethodName = $action->getName().'Action';
			if(!$this->classObject->methodExists($actionMethodName)){
				$actionMethod = new Tx_ExtbaseKickstarter_Domain_Model_Class_Method($actionMethodName);
				$actionMethod->setDescription('action '.$action->getName());
				$actionMethod->setBody('');
				$actionMethod->setTag('return','string The rendered ' . $action->getName() .' action');
				$actionMethod->addModifier('public');
				
				$this->classObject->addMethod($actionMethod);
			}
		}
		return $this->classObject;
	}
	
	/**
	 * This method generates the repository class object, which is passed to the template
	 * it keeps all methods and properties including user modified method bodies and comments 
	 * needed to create a repository class file
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject
	 * @return 
	 */
	public function generateRepositoryClassObject($domainObject){
		t3lib_div::devlog('------------------------------------- generateRepositoryClassObject('.$domainObject->getName().') ---------------------------------','extbase_kickstarter',1);
		
		$this->classObject = NULL;
		
		$className = $domainObject->getDomainRepositoryClassName();
		$classPath = 'Classes/Domain/Repository/' . $domainObject->getName() . 'Repository.php';
		if(Tx_ExtbaseKickstarter_Service_RoundTrip::getOverWriteSettingForPath($classPath,$this->extension) != 0){
			try {
				$this->classObject = $this->roundTripService->getRepositoryClass($domainObject);
			}
			catch(Exception $e){
				t3lib_div::devLog('Class '.$className.' could not be imported: '.$e->getMessage(), 'extbase_kickstarter');		
			}		
		}
		if($this->classObject == NULL) {
			return NULL;
		}
		return $this->classObject;
	}
	
	public function sortMethods($domainObject){
		
		$objectProperties = $domainObject->getProperties();
		$sortedProperties = array();
		$propertyRelatedMethods = array();
		$customMethods = array();
		
		// sort all properties and methods according to domainObject sort order 
		foreach($objectProperties as $objectProperty){
			if($this->classObject->propertyExists($objectProperty->getName())){
				$sortedProperties[$objectProperty->getName()] = $this->classObject->getProperty($objectProperty->getName());
				$methodPrefixes = array('get','set','add','remove','is');
				foreach($methodPrefixes as $methodPrefix){
					$methodName = $this->getMethodName($objectProperty,$methodPrefix);
					if($this->classObject->methodExists($methodName)){
						$propertyRelatedMethods[$methodName] = $this->classObject->getMethod($methodName);
					}
				}
			}
		}
		
		// add the properties that were not in the domainObject
		$classProperties = $this->classObject->getProperties();
		$sortedPropertyNames = array_keys($sortedProperties);
		foreach($classProperties as $classProperty){
			if(!in_array($classProperty->getName(),$sortedProperties)){
				$sortedProperties[$classProperty->getName()] = $classProperty;
			}
		}
		// add custom methods that were manually added to the class
		$classMethods = $this->classObject->getMethods();
		$propertyRelatedMethodNames = array_keys($propertyRelatedMethods);
		foreach($classMethods as $classMethod){
			if(!in_array($classMethod->getName(),$propertyRelatedMethodNames)){
				$customMethods[$classMethod->getName()] = $classMethod;
			}
		}
		$sortedMethods = array_merge($customMethods,$propertyRelatedMethods);
		t3lib_div::devlog('Methods after sorting','extbase_kickstarter',0,array_keys($sortedMethods));
		
		$this->classObject->setProperties($sortedProperties);
		$this->classObject->setMethods($sortedMethods);
	}
	
}

?>