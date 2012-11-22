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
 * If roundtrip is enabled (second parameter in initialize method) the roundtrip service
 * is requested to provide a class object parsed from an existing class
 */

class Tx_ExtensionBuilder_Service_ClassBuilder implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * The current class object
	 * @var Tx_ExtensionBuilder_Domain_Model_Class_Class
	 */
	protected $classObject = NULL;

	/**
	 * @var Tx_ExtensionBuilder_Utility_ClassParser
	 */
	protected $classParser;

	/**
	 * @var Tx_ExtensionBuilder_Service_RoundTrip
	 */
	protected $roundTripService;

	/**
	 * @var Tx_ExtensionBuilder_Configuration_ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * This line is added to the constructor if there are storage objects to initialize
	 * @var string
	 */
	protected $initStorageObjectCall = "//Do not remove the next line: It would break the functionality\n\$this->initStorageObjects();";

	/**
	 *
	 * @var Tx_ExtensionBuilder_Service_CodeGenerator
	 */
	protected $codeGenerator;

	/**
	 * @var Tx_ExtensionBuilder_Domain_Model_Extension
	 */
	protected $extension;

	/**
	 * @param Tx_ExtensionBuilder_Configuration_ConfigurationManager $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_ExtensionBuilder_Configuration_ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * @param Tx_ExtensionBuilder_Service_RoundTrip $roundTripService
	 * @return void
	 */
	public function injectRoundtripService(Tx_ExtensionBuilder_Service_RoundTrip $roundTripService) {
		$this->roundTripService = $roundTripService;
		$this->roundTripService->injectClassBuilder($this);
	}

	/**
	 *
	 * @param Tx_ExtensionBuilder_Service_CodeGenerator $codeGenerator
	 * @param Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 * @param boolean roundtrip enabled?
	 *
	 * @return void
	 */
	public function initialize(Tx_ExtensionBuilder_Service_CodeGenerator $codeGenerator, Tx_ExtensionBuilder_Domain_Model_Extension $extension, $roundTripEnabled) {
		$this->codeGenerator = $codeGenerator;
		$this->extension = $extension;
		$settings = $extension->getSettings();
		if ($roundTripEnabled) {
			$this->roundTripService->initialize($this->extension);
		}
		$this->settings = $settings['classBuilder'];
		$this->extensionDirectory = $this->extension->getExtensionDir();
	}

	/**
	 * This method generates the class schema object, which is passed to the template
	 * it keeps all methods and properties including user modified method bodies and comments
	 * needed to create a domain object class file
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 * @param boolean mergeWithExistingClass
	 *
	 * @return Tx_ExtensionBuilder_Domain_Model_Class_Class
	 */
	public function generateModelClassObject($domainObject, $mergeWithExistingClass) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('------------------------------------- generateModelClassObject(' . $domainObject->getName() . ') ---------------------------------', 'extension_builder', 0);
		$this->classObject = NULL; // reference to the resulting class file,
		$fullQualifiedClassName = $domainObject->getFullQualifiedClassName();

		if ($mergeWithExistingClass) {
			try {
				$this->classObject = $this->roundTripService->getDomainModelClass($domainObject);
			}
			catch (Exception $e) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Class ' . $fullQualifiedClassName . ' could not be imported: ' . $e->getMessage(), 'extension_builder', 2);
			}
		}

		if ($this->classObject == NULL) {
			$this->classObject = new Tx_ExtensionBuilder_Domain_Model_Class_Class($domainObject->getName());
			$this->classObject->setNameSpace($this->extension->getNameSpace() . '\\Domain\\Model');
			if ($domainObject->isEntity()) {
				$parentClass = $domainObject->getParentClass();
				if(empty($parentClass)) {
					$parentClass = $this->configurationManager->getParentClassForEntityObject($this->extension->getExtensionKey());
				}
			} else {
				$parentClass = $this->configurationManager->getParentClassForValueObject($this->extension->getExtensionKey());
			}
			$this->classObject->setParentClass($parentClass);
		}
		if (!$this->classObject->hasDescription()) {
			$this->classObject->setDescription($domainObject->getDescription());
		}
		$this->addInitStorageObjectCalls($domainObject);

		//TODO the following part still needs some enhancement:
		//what should be obligatory in existing properties and methods
		foreach ($domainObject->getProperties() as $domainProperty) {
			$propertyName = $domainProperty->getName();
			// add the property to class Object (or update an existing class Object property)
			if ($this->classObject->propertyExists($propertyName)) {
				$classProperty = $this->classObject->getProperty($propertyName);
				//$classPropertyTags = $classProperty->getTags();
				//\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Property found: ' . $propertyName . ':' . $domainProperty->getTypeForComment(), 'extension_builder', 1, (array)$classProperty);
			}
			else {
				$classProperty = new Tx_ExtensionBuilder_Domain_Model_Class_Property($propertyName);
				$classProperty->setTag('var', $domainProperty->getTypeForComment());
				$classProperty->addModifier('protected');
				//\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('New property: ' . $propertyName . ':' . $domainProperty->getTypeForComment(), 'extension_builder', 1);
			}

			$classProperty->setAssociatedDomainObjectProperty($domainProperty);

			if ($domainProperty->getRequired()) {
				if (!$classProperty->isTaggedWith('validate')) {
					$validateTag = explode(' ', trim($domainProperty->getValidateAnnotation()));
					$classProperty->setTag('validate', $validateTag[1]);
				}
			}

			if ($domainProperty->isRelation() && $domainProperty->getLazyLoading()) {
				if (!$classProperty->isTaggedWith('lazy')) {
					$classProperty->setTag('lazy', '');
				}
			}

			if ($domainProperty->getHasDefaultValue()) {
				$classProperty->setDefault($domainProperty->getDefaultValue());
			}

			$this->classObject->setProperty($classProperty);

			if ($domainProperty->isNew()) {
				$this->setPropertyRelatedMethods($domainProperty);
			}
		}
		//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Methods before sorting','extension_builder',0,array_keys($this->classObject->getMethods()));
		//$this->sortMethods($domainObject);
		return $this->classObject;
	}

	/**
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 * @return void
	 */
	protected function addInitStorageObjectCalls(Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject) {
		$anyToManyRelationProperties = $domainObject->getAnyToManyRelationProperties();

		if (count($anyToManyRelationProperties) > 0) {
			if (!$this->classObject->methodExists('__construct')) {
				$constructorMethod = new Tx_ExtensionBuilder_Domain_Model_Class_Method('__construct');
				//$constructorMethod->setDescription('The constructor of this '.$domainObject->getName());
				if (count($anyToManyRelationProperties) > 0) {
					$constructorMethod->setBody($this->codeGenerator->getDefaultMethodBody($domainObject, NULL, 'Model', '', 'construct'));
				}
				$constructorMethod->addModifier('public');
				$constructorMethod->setTag('return', $domainObject->getName());
				$this->classObject->addMethod($constructorMethod);
			}
			$constructorMethod = $this->classObject->getMethod('__construct');
			if (preg_match('/\$this->initStorageObjects()/', $constructorMethod->getBody()) < 1) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Constructor method in Class ' . $this->classObject->getName() . ' was overwritten since the initStorageObjectCall was missing', 'extension_builder', 2, array('Original method' => $constructorMethod->getBody()));
				$constructorMethod->setBody($this->initStorageObjectCall);
				$this->classObject->setMethod($constructorMethod);
			}
			//initStorageObjects
			$initStorageObjectsMethod = new Tx_ExtensionBuilder_Domain_Model_Class_Method('initStorageObjects');
			$initStorageObjectsMethod->setDescription('Initializes all ObjectStorage properties.');
			$methodBody = "/**\n* Do not modify this method!\n* It will be rewritten on each save in the extension builder\n* You may modify the constructor of this class instead\n*/\n";
			foreach ($anyToManyRelationProperties as $relationProperty) {
				$methodBody .= "\$this->" . $relationProperty->getName() . " = new \\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage();\n";
			}
			$initStorageObjectsMethod->setBody($this->codeGenerator->getDefaultMethodBody($domainObject, NULL, 'Model', '', 'initStorageObjects'));
			$initStorageObjectsMethod->addModifier('protected');
			$initStorageObjectsMethod->setTag('return', 'void');
			$this->classObject->setMethod($initStorageObjectsMethod);
		} else if ($this->classObject->methodExists('initStorageObjects')) {
			$this->classObject->getMethod('initStorageObjects')->setBody('// empty');
		}
	}

	/**
	 * add all setter/getter/add/remove etc. methods
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject_AbstractProperty $domainProperty
	 *
	 * @return void
	 */
	protected function setPropertyRelatedMethods($domainProperty) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('setPropertyRelatedMethods:' . $domainProperty->getName(), 'extension_builder', 0, (array)$domainProperty);
		if (is_subclass_of($domainProperty, 'Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_AnyToManyRelation')) {
			$addMethod = $this->buildAddMethod($domainProperty);
			$removeMethod = $this->buildRemoveMethod($domainProperty);
			$this->classObject->setMethod($addMethod);
			$this->classObject->setMethod($removeMethod);
		}
		$getMethod = $this->buildGetterMethod($domainProperty);
		$setMethod = $this->buildSetterMethod($domainProperty);
		$this->classObject->setMethod($getMethod);
		$this->classObject->setMethod($setMethod);
		if ($domainProperty->getTypeForComment() == 'boolean') {
			$isMethod = $this->buildIsMethod($domainProperty);
			$this->classObject->setMethod($isMethod);
		}
	}


	/**
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject_AbstractProperty $domainProperty
	 *
	 * @return Tx_ExtensionBuilder_Domain_Model_Class_Method
	 */
	protected function buildGetterMethod($domainProperty) {

		// add (or update) a getter method
		$getterMethodName = $this->getMethodName($domainProperty, 'get');
		if ($this->classObject->methodExists($getterMethodName)) {
			$getterMethod = $this->classObject->getMethod($getterMethodName);
			//$getterMethodTags = $getterMethod->getTags();
			//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Existing getterMethod imported:' . $getterMethodName, 'extension_builder', 0, array('methodBody' => $getterMethod->getBody()));
		}
		else {
			$getterMethod = new Tx_ExtensionBuilder_Domain_Model_Class_Method($getterMethodName);
			//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('new getMethod:' . $getterMethodName, 'extension_builder', 0);
			// default method body
			$getterMethod->setBody($this->codeGenerator->getDefaultMethodBody(NULL, $domainProperty, 'Model', 'get', ''));
			$getterMethod->setTag('return', $domainProperty->getTypeForComment() . ' $' . $domainProperty->getName());
			$getterMethod->addModifier('public');
		}
		if (!$getterMethod->hasDescription()) {
			$getterMethod->setDescription('Returns the ' . $domainProperty->getName());
		}
		return $getterMethod;
	}

	/**
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject_AbstractProperty $domainProperty
	 *
	 * @return Tx_ExtensionBuilder_Domain_Model_Class_Method
	 */
	protected function buildSetterMethod($domainProperty) {
		$propertyName = $this->getParameterName($domainProperty, 'set');
		// add (or update) a setter method
		$setterMethodName = $this->getMethodName($domainProperty, 'set');
		if ($this->classObject->methodExists($setterMethodName)) {
			$setterMethod = $this->classObject->getMethod($setterMethodName);
			//$setterMethodTags = $setterMethod->getTags();
			//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Existing setterMethod imported:' . $setterMethodName, 'extension_builder', 0, array('methodBody' => $setterMethod->getBody()));
		}
		else {
			//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('new setMethod:' . $setterMethodName, 'extension_builder', 0);
			$setterMethod = new Tx_ExtensionBuilder_Domain_Model_Class_Method($setterMethodName);
			// default method body
			$setterMethod->setBody($this->codeGenerator->getDefaultMethodBody(NULL, $domainProperty, 'Model', 'set', ''));
			$setterMethod->setTag('param', $this->getParamTag($domainProperty, 'set'));
			$setterMethod->setTag('return', 'void');
			$setterMethod->addModifier('public');
		}
		if (!$setterMethod->hasDescription()) {
			$setterMethod->setDescription('Sets the ' . $propertyName);
		}
		$setterParameters = $setterMethod->getParameterNames();
		if (!in_array($propertyName, $setterParameters)) {
			$setterParameter = new Tx_ExtensionBuilder_Domain_Model_Class_MethodParameter($propertyName);
			$setterParameter->setVarType($domainProperty->getTypeForComment());
			if (is_subclass_of($domainProperty, 'Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_AbstractRelation')) {
				$setterParameter->setTypeHint($domainProperty->getTypeHint());
			}
			$setterMethod->setParameter($setterParameter);
		} else {
			current($setterMethod->getParameters())->setTypeHint($domainProperty->getTypeHint());
		}
		return $setterMethod;
	}


	/**
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_AbstractRelation $domainProperty
	 *
	 * @return Tx_ExtensionBuilder_Domain_Model_Class_Method
	 */
	protected function buildAddMethod($domainProperty) {

		$propertyName = $domainProperty->getName();

		$addMethodName = $this->getMethodName($domainProperty, 'add');

		if ($this->classObject->methodExists($addMethodName)) {
			$addMethod = $this->classObject->getMethod($addMethodName);
			\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Existing addMethod imported:' . $addMethodName, 'extension_builder', 0, array('methodBody' => $addMethod->getBody()));
		}
		else {
			\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('new addMethod:' . $addMethodName, 'extension_builder', 0);
			$addMethod = new Tx_ExtensionBuilder_Domain_Model_Class_Method($addMethodName);
			// default method body
			$addMethod->setBody($this->codeGenerator->getDefaultMethodBody(NULL, $domainProperty, 'Model', 'add', ''));
			$addMethod->setTag('param', $this->getParamTag($domainProperty, 'add'));

			$addMethod->setTag('return', 'void');
			$addMethod->addModifier('public');
		}
		$addParameters = $addMethod->getParameterNames();
		\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Existing setter parameter:','extension_builder', 0, $addParameters);
		if (!in_array(Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName), $addParameters)) {
			$addParameter = new Tx_ExtensionBuilder_Domain_Model_Class_MethodParameter($this->getParameterName($domainProperty, 'add'));
			$addParameter->setVarType($domainProperty->getForeignClassName());
			$addParameter->setTypeHint($domainProperty->getForeignClassName());
			$addMethod->setParameter($addParameter);
		} else {
				// we expect always the first (!) parameter to represent the object to add
			current($addMethod->getParameters())->setTypeHint($domainProperty->getForeignClassName());
		}
		if (!$addMethod->hasDescription()) {
			$addMethod->setDescription('Adds a ' . $domainProperty->getForeignModelName());
		}
		return $addMethod;
	}

	/**
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_AbstractRelation $domainProperty
	 *
	 * @return Tx_ExtensionBuilder_Domain_Model_Class_Method
	 */
	protected function buildRemoveMethod($domainProperty) {

		$removeMethodName = $this->getMethodName($domainProperty, 'remove');

		if ($this->classObject->methodExists($removeMethodName)) {
			$removeMethod = $this->classObject->getMethod($removeMethodName);
			//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Existing removeMethod imported:' . $removeMethodName, 'extension_builder', 0, array('methodBody' => $removeMethod->getBody()));
		}
		else {
			//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('new removeMethod:' . $removeMethodName, 'extension_builder', 0);
			$removeMethod = new Tx_ExtensionBuilder_Domain_Model_Class_Method($removeMethodName);
			// default method body
			$removeMethod->setBody($this->codeGenerator->getDefaultMethodBody(NULL, $domainProperty, 'Model', 'remove', ''));
			$removeMethod->setTag('param', $this->getParamTag($domainProperty, 'remove'));
			$removeMethod->setTag('return', 'void');
			$removeMethod->addModifier('public');
		}

		$removeParameters = $removeMethod->getParameterNames();

		if (!in_array($this->getParameterName($domainProperty, 'remove'), $removeParameters)) {
			$removeParameter = new Tx_ExtensionBuilder_Domain_Model_Class_MethodParameter($this->getParameterName($domainProperty, 'remove'));
			$removeParameter->setVarType($domainProperty->getForeignClassName());
			$removeParameter->setTypeHint($domainProperty->getForeignClassName());
			$removeMethod->setParameter($removeParameter);
		} else {
				// we expect always the first (!) parameter to represent the object to add
			current($removeMethod->getParameters())->setTypeHint($domainProperty->getForeignClassName());
		}

		if (!$removeMethod->hasDescription()) {
			$removeMethod->setDescription('Removes a ' . $domainProperty->getForeignModelName());
		}
		return $removeMethod;
	}

	/**
	 * Builds a method that checks the current boolean state of a property
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject_AbstractProperty $domainProperty
	 *
	 * @return Tx_ExtensionBuilder_Domain_Model_Class_Method
	 */
	protected function buildIsMethod($domainProperty) {

		$isMethodName = $this->getMethodName($domainProperty, 'is');

		if ($this->classObject->methodExists($isMethodName)) {
			$isMethod = $this->classObject->getMethod($isMethodName);
			//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Existing isMethod imported:' . $isMethodName, 'extension_builder', 0, array('methodBody' => $isMethod->getBody()));
		}
		else {
			//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('new isMethod:' . $isMethodName, 'extension_builder', 1);
			$isMethod = new Tx_ExtensionBuilder_Domain_Model_Class_Method($isMethodName);
			// default method body
			$isMethod->setBody($this->codeGenerator->getDefaultMethodBody(NULL, $domainProperty, 'Model', 'is', ''));
			$isMethod->setTag('return', 'boolean');
			$isMethod->addModifier('public');
		}

		if (!$isMethod->hasDescription()) {
			$isMethod->setDescription('Returns the boolean state of ' . $domainProperty->getName());
		}
		return $isMethod;
	}

	/**
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject_Action $action
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 *
	 * @return Tx_ExtensionBuilder_Domain_Model_Class_Method
	 */
	protected function buildActionMethod(Tx_ExtensionBuilder_Domain_Model_DomainObject_Action $action, Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject) {
		$actionName = $action->getName();
		$actionMethodName = $actionName . 'Action';
		$actionMethod = new Tx_ExtensionBuilder_Domain_Model_Class_Method($actionMethodName);
		$actionMethod->setDescription('action ' . $action->getName());
		$actionMethod->setBody($this->codeGenerator->getDefaultMethodBody($domainObject, NULL, 'Controller', '', $actionMethodName));
		$actionMethod->addModifier('public');
		if (in_array($actionName, array('show', 'edit', 'create', 'new', 'update', 'delete'))) {
			// these actions need a parameter
			if (in_array($actionName, array('create', 'new'))) {
				$parameterName = 'new' . $domainObject->getName();
			} else {
				$parameterName = \TYPO3\CMS\Core\Utility\GeneralUtility::lcfirst($domainObject->getName());
			}
			$parameter = new Tx_ExtensionBuilder_Domain_Model_Class_MethodParameter($parameterName);
			$parameter->setTypeHint($domainObject->getFullQualifiedClassName());
			$parameter->setVarType($domainObject->getFullQualifiedClassName());
			$parameter->setPosition(0);
			if ($actionName == 'new') {
				$parameter->setOptional(TRUE);
				$actionMethod->setTag('dontvalidate', '$' . $parameterName);
			}
			$actionMethod->setParameter($parameter);
		}
		$actionMethod->setTag('return', 'void');

		return $actionMethod;
	}

	/**
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject_AbstractProperty $property
	 * @param string $methodType (get,set,add,remove,is)
	 * @return string method name
	 */
	public function getMethodName($domainProperty, $methodType) {
		$propertyName = $domainProperty->getName();
		switch ($methodType) {
			case 'set'		:
				return 'set' . ucfirst($propertyName);

			case 'get'		:
				return 'get' . ucfirst($propertyName);

			case 'add'		:
				return 'add' . ucfirst(Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName));

			case 'remove'	:
				return 'remove' . ucfirst(Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName));

			case 'is'		:
				return 'is' . ucfirst($propertyName);
		}
	}

	/**
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject_AbstractProperty $property
	 * @param string $methodType (set,add,remove)
	 * @return string method body
	 */
	public function getParameterName($domainProperty, $methodType) {

		$propertyName = $domainProperty->getName();

		switch ($methodType) {

			case 'set'			:
				return $propertyName;

			case 'add'			:
				return Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName);

			case 'remove'		:
				return Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName) . 'ToRemove';
		}
	}

	public function getParamTag($domainProperty, $methodType) {

		switch ($methodType) {
			case 'set'		:
				return $domainProperty->getTypeForComment() . ' $' . $domainProperty->getName();

			case 'add'		:
				$paramTag = $domainProperty->getForeignClassName();
				$paramTag .= ' $' . $this->getParameterName($domainProperty, 'add');
				return $paramTag;

			case 'remove'	:
				$paramTag = $domainProperty->getForeignClassName();
				$paramTag .= ' $' . $this->getParameterName($domainProperty, 'remove');
				$paramTag .= ' The ' . $domainProperty->getForeignModelName() . ' to be removed';
				return $paramTag;
		}
	}

	/**
	 * This method generates the class object, which is passed to the template
	 * it keeps all methods and properties including user modified method bodies and
	 * comments that are required to create a controller class file
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 * @param boolean $mergeWithExistingClass
	 *
	 * @return Tx_ExtensionBuilder_Domain_Model_Class_Class
	 */
	public function generateControllerClassObject($domainObject, $mergeWithExistingClass) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('------------------------------------- generateControllerClassObject(' . $domainObject->getName() . ') ---------------------------------', 'extension_builder', 1);

		$this->classObject = NULL;
		$className = $domainObject->getName() . 'Controller';

		if ($mergeWithExistingClass) {
			try {
				$this->classObject = $this->roundTripService->getControllerClass($domainObject);
			}
			catch (Exception $e) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Class ' . $className . ' could not be imported: ' . $e->getMessage(), 'extension_builder');
			}
		}

		if ($this->classObject == NULL) {
			$this->classObject = new Tx_ExtensionBuilder_Domain_Model_Class_Class($className);
			$this->classObject->setNameSpace($this->extension->getNameSpace() . '\\Controller');
			if (isset($this->settings['Controller']['parentClass'])) {
				$parentClass = $this->settings['Controller']['parentClass'];
			} else {
				$parentClass = '\\TYPO3\\CMS\\Extbase\\Mvc\\Controller\\ActionController';
			}
			$this->classObject->setParentClass($parentClass);
		}

		if ($domainObject->isAggregateRoot()) {
			$propertyName = \TYPO3\CMS\Core\Utility\GeneralUtility::lcfirst($domainObject->getName()) . 'Repository';
			// now add the property to class Object (or update an existing class Object property)
			if (!$this->classObject->propertyExists($propertyName)) {
				$classProperty = new Tx_ExtensionBuilder_Domain_Model_Class_Property($propertyName);
				$classProperty->setTag('var', $domainObject->getDomainRepositoryClassName());
				$classProperty->setTag('inject', '');
				$classProperty->addModifier('protected');
				$this->classObject->setProperty($classProperty);
			} if($this->classObject->getProperty($propertyName)->isTaggedWith('inject')) {
				$this->classObject->getProperty($propertyName)->setTag('inject');
			}
		}
		foreach ($domainObject->getActions() as $action) {
			$actionMethodName = $action->getName() . 'Action';
			if (!$this->classObject->methodExists($actionMethodName)) {
				$actionMethod = $this->buildActionMethod($action, $domainObject);
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
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 * @param boolean $mergeWithExistingClass
	 *
	 * @return Tx_ExtensionBuilder_Domain_Model_Class_Class
	 */
	public function generateRepositoryClassObject($domainObject, $mergeWithExistingClass) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('------------------------------------- generateRepositoryClassObject(' . $domainObject->getName() . ') ---------------------------------', 'extension_builder', 1);

		$this->classObject = NULL;
		$className = $domainObject->getName() . 'Repository';
		if ($mergeWithExistingClass) {
			try {
				$this->classObject = $this->roundTripService->getRepositoryClass($domainObject);
			}
			catch (Exception $e) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Class ' . $className . ' could not be imported: ' . $e->getMessage(), 'extension_builder');
			}
		}

		if ($this->classObject == NULL) {
			$this->classObject = new Tx_ExtensionBuilder_Domain_Model_Class_Class($className);
			$this->classObject->setNameSpace($this->extension->getNameSpace() . '\\Domain\\Repository');
			if (isset($this->settings['Repository']['parentClass'])) {
				$parentClass = $this->settings['Repository']['parentClass'];
			} else {
				$parentClass = '\\TYPO3\\CMS\\Extbase\\Persistence\\Repository';
			}
			$this->classObject->setParentClass($parentClass);
		}

		return $this->classObject;
	}

	/**
	 * Not used right now
	 * TODO: Needs better implementation
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 * @return void
	 */
	public function sortMethods($domainObject) {

		$objectProperties = $domainObject->getProperties();
		$sortedProperties = array();
		$propertyRelatedMethods = array();
		$customMethods = array();

		// sort all properties and methods according to domainObject sort order
		foreach ($objectProperties as $objectProperty) {
			if ($this->classObject->propertyExists($objectProperty->getName())) {
				$sortedProperties[$objectProperty->getName()] = $this->classObject->getProperty($objectProperty->getName());
				$methodPrefixes = array('get', 'set', 'add', 'remove', 'is');
				foreach ($methodPrefixes as $methodPrefix) {
					$methodName = $this->getMethodName($objectProperty, $methodPrefix);
					if ($this->classObject->methodExists($methodName)) {
						$propertyRelatedMethods[$methodName] = $this->classObject->getMethod($methodName);
					}
				}
			}
		}

		// add the properties that were not in the domainObject
		$classProperties = $this->classObject->getProperties();
		$sortedPropertyNames = array_keys($sortedProperties);
		foreach ($classProperties as $classProperty) {
			if (!in_array($classProperty->getName(), $sortedProperties)) {
				$sortedProperties[$classProperty->getName()] = $classProperty;
			}
		}
		// add custom methods that were manually added to the class
		$classMethods = $this->classObject->getMethods();
		$propertyRelatedMethodNames = array_keys($propertyRelatedMethods);
		foreach ($classMethods as $classMethod) {
			if (!in_array($classMethod->getName(), $propertyRelatedMethodNames)) {
				$customMethods[$classMethod->getName()] = $classMethod;
			}
		}
		$sortedMethods = array_merge($customMethods, $propertyRelatedMethods);
		//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Methods after sorting', 'extension_builder', 0, array_keys($sortedMethods));

		$this->classObject->setProperties($sortedProperties);
		$this->classObject->setMethods($sortedMethods);
	}

}

?>
