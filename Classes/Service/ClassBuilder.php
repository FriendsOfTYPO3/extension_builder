<?php
namespace EBT\ExtensionBuilder\Service;
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
use EBT\ExtensionBuilder\Domain\Model;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty;
use EBT\ExtensionBuilder\Utility\Inflector;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Builds the required class objects for extbase extensions
 * If roundtrip is enabled (second parameter in initialize method)
 * the roundtrip service is requested to provide a class object
 * parsed from an existing class
 */

class ClassBuilder implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * The class file object created to container the generated class
	 *
	 * @var Model\File
	 */
	protected $classFileObject = NULL;

	/**
	 * The current class object
	 *
	 * @var Model\ClassObject\ClassObject
	 */
	protected $classObject = NULL;

	/**
	 * The template file object used for new created class files
	 *
	 * @var Model\File
	 */
	protected $templateFileObject = NULL;

	/**
	 * The template class object used for new created classes
	 * @var Model\ClassObject\ClassObject
	 */
	protected $templateClassObject = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Parser\ClassFactory
	 */
	protected $classFactory;

	/**
	 * @var Parser
	 */
	protected $parserService;

	/**
	 * @var Printer
	 */
	protected $printerService;

	/**
	 * @var RoundTrip
	 */
	protected $roundTripService;

	/**
	 * @var \EBT\ExtensionBuilder\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 *
	 * @var FileGenerator
	 */
	protected $fileGenerator;

	/**
	 * @var \EBT\ExtensionBuilder\Domain\Model\Extension
	 */
	protected $extension;

	/**
	 * @var array
	 */
	protected $settings = array();

	/**
	 * @param \EBT\ExtensionBuilder\Configuration\ConfigurationManager $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(\EBT\ExtensionBuilder\Configuration\ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * @param RoundTrip $roundTripService
	 * @return void
	 */
	public function injectRoundtripService(RoundTrip $roundTripService) {
		$this->roundTripService = $roundTripService;
		$this->roundTripService->injectClassBuilder($this);
	}

	/**
	 * @param Parser $parserService
	 * @return void
	 */
	public function injectParserService(Parser $parserService) {
		$this->parserService = $parserService;
	}

	/**
	 * @param Printer $printerService
	 * @return void
	 */
	public function injectPrinterService(Printer $printerService) {
		$this->printerService = $printerService;
	}

	/**
	 * @param \EBT\ExtensionBuilder\Parser\ClassFactory $classFactory
	 * @return void
	 */
	public function injectClassFactory(\EBT\ExtensionBuilder\Parser\ClassFactory $classFactory) {
		$this->classFactory = $classFactory;
	}

	/**
	 *
	 * @param FileGenerator $fileGenerator
	 * @param Model\Extension $extension
	 * @param boolean roundtrip enabled?
	 *
	 * @return void
	 */
	public function initialize(FileGenerator $fileGenerator, Model\Extension $extension, $roundTripEnabled) {
		$this->fileGenerator = $fileGenerator;
		$this->extension = $extension;
		$settings = $extension->getSettings();
		if ($roundTripEnabled) {
			$this->roundTripService->initialize($this->extension);
			\EBT\ExtensionBuilder\Parser\AutoLoader::register();
		}
		$this->settings = $settings['classBuilder'];
		$this->extensionDirectory = $this->extension->getExtensionDir();
	}

	/**
	 * This method generates the class schema object, which is passed to the template
	 * it keeps all methods and properties including user modified method bodies and
	 * comments needed to create a domain object class file
	 *
	 * @param Model\DomainObject $domainObject
	 * @param boolean $mergeWithExistingClass
	 *
	 * @return Model\ClassObject\ClassObject
	 */
	public function generateModelClassFileObject($domainObject, $modelClassTemplatePath, $mergeWithExistingClass) {
		$this->classObject = NULL;
		$fullQualifiedClassName = $domainObject->getFullQualifiedClassName();
		$this->templateFileObject = $this->parserService->parseFile($modelClassTemplatePath);
		$this->templateClassObject = $this->templateFileObject->getFirstClass();
		if ($mergeWithExistingClass) {
			try {
				$this->classFileObject = $this->roundTripService->getDomainModelClassFile($domainObject);
				if (!is_null($this->classFileObject)) {
					$this->classObject = $this->classFileObject->getFirstClass();
				}
			}
			catch (\Exception $e) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Class ' . $fullQualifiedClassName . ' could not be imported: ' . $e->getMessage(), 'extension_builder', 2);
			}
		}
		if ($this->classObject == NULL) {
			$this->createNewModelClassObject($domainObject);
		}
		if (!$this->classObject->hasDescription() && $domainObject->getDescription()) {
			$this->classObject->setDescription($domainObject->getDescription());
		}

		$this->addInitStorageObjectCalls($domainObject);

		foreach ($domainObject->getProperties() as $domainProperty) {
			$this->addClassProperty($domainProperty);
			if ($domainProperty->isNew()) {
				$this->setPropertyRelatedMethods($domainProperty);
			}
		}
		$this->classFileObject->getNamespace()
			->setName($this->extension->getNamespaceName() . '\\Domain\\Model')
			->setClasses(array($this->classObject));
		return $this->classFileObject;
	}

	/**
	 *
	 * create a new class object based on the template and the related domain object
	 *
	 * @param Model\DomainObject $domainObject
	 *
	 * @return void
	 */
	protected function createNewModelClassObject($domainObject) {
		$this->classFileObject = clone($this->templateFileObject);
		$this->classObject = clone($this->templateFileObject->getFirstClass());
		$this->classObject->resetAll(); // start with plain class
		$this->classObject->setName($domainObject->getName());
		if ($domainObject->isEntity()) {
			$parentClass = $domainObject->getParentClass();
			if (empty($parentClass)) {
				$parentClass = $this->configurationManager->getParentClassForEntityObject($this->extension->getExtensionKey());
			}
		} else {
			$parentClass = $this->configurationManager->getParentClassForValueObject($this->extension->getExtensionKey());
		}
		$this->classObject->setParentClassName($parentClass);
		$this->classObject->setDescription($domainObject->getDescription());
	}

	/**
	 * @param AbstractProperty $domainProperty
	 * @return void
	 */
	protected function addClassProperty($domainProperty) {
		// TODO the following part still needs some enhancement:
		// what should be obligatory in existing properties and methods
		$propertyName = $domainProperty->getName();
		// add the property to class Object (or update an existing class Object property)
		if ($this->classObject->propertyExists($propertyName)) {
			$classProperty = $this->classObject->getProperty($propertyName);
			// $classPropertyTags = $classProperty->getTags();
			// $this->logger->log('Property found: ' . $propertyName . ':' . $domainProperty->getTypeForComment(), 'extension_builder', 1, (array)$classProperty);
		} else {
			$classProperty = clone($this->templateClassObject->getProperty('property'));
			$classProperty->setName($propertyName);
			$classProperty->setTag('var', $domainProperty->getTypeForComment());
			if ($domainProperty->getDescription()) {
				$classProperty->setDescription($domainProperty->getDescription());
			} else {
				$classProperty->setDescription(str_replace('property', $propertyName, $classProperty->getDescription()));
			}

			if ($domainProperty->getHasDefaultValue()) {
				$classProperty->setDefault($domainProperty->getDefaultValue());
			}

			if ($domainProperty->isZeroToManyRelation()) {
					$classProperty->setTag('cascade','remove');
			}
			// $this->logger->log('New property: ' . $propertyName . ':' . $domainProperty->getTypeForComment(), 'extension_builder', 1);
		}

		//$classProperty->setAssociatedDomainObjectProperty($domainProperty);

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

		$this->classObject->setProperty($classProperty);
		if ($this->classObject->getName() == 'Main') {
			//var_dump($this->classObject->getProperties());
		}

	}

	/**
	 * @param Model\DomainObject $domainObject
	 * @return void
	 */
	protected function addInitStorageObjectCalls(Model\DomainObject $domainObject) {
		$anyToManyRelationProperties = $domainObject->getAnyToManyRelationProperties();
		if (count($anyToManyRelationProperties) > 0) {
			if (!$this->classObject->methodExists('__construct')) {
				$constructorMethod = $this->templateClassObject->getMethod('__construct');
				$constructorMethod->setDescription('__construct');
				//$constructorMethod->setTag('return', $domainObject->getName());
				$this->classObject->addMethod($constructorMethod);
			} else {
				$constructorMethod = $this->classObject->getMethod('__construct');
			}
			if (preg_match('/\$this->initStorageObjects()/', $this->printerService->render($constructorMethod->getBodyStmts())) < 1) {
				//\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Constructor method in Class ' . $this->classObject->getName() . ' was overwritten since the initStorageObjectCall was missing', 'extension_builder', 2, array('Original method' => $constructorMethod->getBody()));
				$this->classObject->setMethod($this->classObject->getMethod('__construct'));
			}
			//initStorageObjects
			$initStorageObjectsMethod = clone($this->templateClassObject->getMethod('initStorageObjects'));
			$methodBodyStmts = array();
			$templateBodyStmts = $initStorageObjectsMethod->getBodyStmts();
			$initStorageObjectsMethod->setModifier('protected');
			foreach ($anyToManyRelationProperties as $relationProperty) {
				$methodBodyStmts = array_merge($methodBodyStmts, $this->parserService->replaceNodeProperty($templateBodyStmts, array('children' => $relationProperty->getName()), '\PHPParser_Node_Expr_PropertyFetch'));
				//$methodBody .= "\$this->" . $relationProperty->getName() . " = new \\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage();\n";
			}
			$initStorageObjectsMethod->setBodyStmts($methodBodyStmts);
			$this->classObject->setMethod($initStorageObjectsMethod);
		} else if ($this->classObject->methodExists('initStorageObjects')) {
			$this->classObject->getMethod('initStorageObjects')->setBodyStmts(array());
		}
	}

	/**
	 * add all setter/getter/add/remove etc. methods
	 * @param Model\DomainObject\AbstractProperty $domainProperty
	 *
	 * @return void
	 */
	protected function setPropertyRelatedMethods($domainProperty) {
		//$this->logger->log('setPropertyRelatedMethods:' . $domainProperty->getName(), 'extension_builder', 0, (array)$domainProperty);
		if ($domainProperty->isAnyToManyRelation()) {
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
	 * @param Model\DomainObject\AbstractProperty $domainProperty
	 *
	 * @return Model\ClassObject\Method
	 */
	protected function buildGetterMethod($domainProperty) {
		$propertyName = $domainProperty->getName();
		// add (or update) a getter method
		$getterMethodName = \EBT\ExtensionBuilder\Utility\Tools::getMethodName($domainProperty, 'get');
		if ($this->classObject->methodExists($getterMethodName)) {
			$getterMethod = $this->classObject->getMethod($getterMethodName);
		} else {
			$getterMethod = clone $this->templateClassObject->getMethod('getProperty')->setName($getterMethodName);
			$replacements = array('property' => $propertyName);
			$this->updateMethodBody($getterMethod, $replacements);
			$this->updateDocComment($getterMethod, $replacements);
			$getterMethod->setTag('return', $domainProperty->getTypeForComment() . ' $' . $propertyName);
		}
		if (!$getterMethod->hasDescription()) {
			$getterMethod->setDescription('Returns the ' . $domainProperty->getName());
		}
		return $getterMethod;
	}

	/**
	 *
	 * @param Model\DomainObject\AbstractProperty $domainProperty
	 *
	 * @return Model\ClassObject\Method
	 */
	protected function buildSetterMethod($domainProperty) {

		$propertyName = $domainProperty->getName();
		// add (or update) a setter method
		$setterMethodName = \EBT\ExtensionBuilder\Utility\Tools::getMethodName($domainProperty, 'set');
		\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Building setter method:' . $setterMethodName, 'extension_builder', 2);;
		if ($this->classObject->methodExists($setterMethodName)) {
			$setterMethod = $this->classObject->getMethod($setterMethodName);
			//$setterMethodTags = $setterMethod->getTags();
			\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Existing setter method imported!', 'extension_builder', 2, $setterMethod->getTags());
		} else {
			//$this->logger->log('new setMethod:' . $setterMethodName, 'extension_builder', 0);
			$setterMethod = clone $this->templateClassObject->getMethod('setProperty');
			$setterMethod->setName('set' . ucfirst($propertyName));
			$replacements = array('property' => $propertyName);
			$this->updateMethodBody($setterMethod, $replacements);
			$this->updateDocComment($setterMethod, $replacements);
			$setterMethod->setTag('return', 'void');
			$setterMethod->getParameterByPosition(0)->setName($propertyName)
				->setTypeHint($domainProperty->getTypeHint())
				->setTypeForParamTag($domainProperty->getTypeForComment());

		}
		if (!$setterMethod->hasDescription()) {
			$setterMethod->setDescription('Sets the ' . $propertyName);
		}
		$setterParameters = $setterMethod->getParameterNames();
		if (!in_array($propertyName, $setterParameters)) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Setter for  ' . $propertyName . ' misses parameter!', 'extension_builder', 2, $setterParameters);
			$setterParameter = new Model\ClassObject\MethodParameter($propertyName);
			$setterParameter->setVarType($domainProperty->getTypeForComment());
			if (is_subclass_of($domainProperty, 'Model\\DomainObject\\Relation\\AbstractRelation')) {
				$setterParameter->setTypeHint($domainProperty->getTypeHint());
			}
			$setterMethod->setParameter($setterParameter);
		}
		return $setterMethod;
	}


	/**
	 *
	 * @param Model\DomainObject\AbstractProperty $domainProperty
	 *
	 * @return Model\ClassObject\Method
	 */
	protected function buildAddMethod($domainProperty) {

		$propertyName = $domainProperty->getName();
		$addMethodName = \EBT\ExtensionBuilder\Utility\Tools::getMethodName($domainProperty, 'add');

		if ($this->classObject->methodExists($addMethodName)) {
			$addMethod = $this->classObject->getMethod($addMethodName);
			//$this->logger->log('Existing addMethod imported:' . $addMethodName, 'extension_builder', 0, array('methodBody' => $addMethod->getBody()));
		} else {
			//$this->logger->log('new addMethod:' . $addMethodName, 'extension_builder', 0);
			$addMethod = clone($this->templateClassObject->getMethod('addChild'));
			$addMethod->setName('add' . ucfirst(\EBT\ExtensionBuilder\Utility\Inflector::singularize($propertyName)));

			$this->updateMethodBody(
				$addMethod,
				array(
					'child' => \EBT\ExtensionBuilder\Utility\Inflector::singularize($propertyName),
					'children' => $propertyName,
					'Child' => $domainProperty->getForeignModelName()
				)
			);
			$this->updateDocComment(
				$addMethod,
				array(
					'\bchild\b' => \EBT\ExtensionBuilder\Utility\Inflector::singularize($propertyName),
					'\bchildren\b' => $propertyName,
					'\bChild\b' => $domainProperty->getForeignModelName()
				)
			);

			$addMethod->setTag('param', \EBT\ExtensionBuilder\Utility\Tools::getParamTag($domainProperty, 'add'));
			$addMethod->getParameterByPosition(0)
				->setName(\EBT\ExtensionBuilder\Utility\Inflector::singularize($propertyName))
				->setVarType($domainProperty->getForeignClassName())
				->setTypeHint($domainProperty->getForeignClassName());
			$addMethod->setTag('return', 'void');
			$addMethod->addModifier('public');
		}
		$addParameters = $addMethod->getParameterNames();

		if (!in_array(\EBT\ExtensionBuilder\Utility\Inflector::singularize($propertyName), $addParameters)) {
			$addParameter = new Model\Parameter();
			$addParameter->setName(\EBT\ExtensionBuilder\Utility\Tools::getParameterName($domainProperty, 'add'));
			$addParameter->setVarType($domainProperty->getForeignClassName());
			//$addParameter->setTypeHint($domainProperty->getForeignClassName());
			$addMethod->setParameter($addParameter);
		}
		if (!$addMethod->hasDescription()) {
			$addMethod->setDescription('Adds a ' . $domainProperty->getForeignModelName());
		}
		return $addMethod;
	}

	/**
	 *
	 * @param Model\DomainObject\AbstractProperty $domainProperty
	 *
	 * @return Model\ClassObject\Method
	 */
	protected function buildRemoveMethod($domainProperty) {
		$propertyName = $domainProperty->getName();
		$removeMethodName = \EBT\ExtensionBuilder\Utility\Tools::getMethodName($domainProperty, 'remove');
		$parameterName = \EBT\ExtensionBuilder\Utility\Tools::getParameterName($domainProperty, 'remove');

		if ($this->classObject->methodExists($removeMethodName)) {
			$removeMethod = $this->classObject->getMethod($removeMethodName);
			//$this->logger->log('Existing removeMethod imported:' . $removeMethodName, 'extension_builder', 0, array('methodBody' => $removeMethod->getBody()));
		} else {
			//$this->logger->log('new removeMethod:' . $removeMethodName, 'extension_builder', 0);
			$removeMethod = clone($this->templateClassObject->getMethod('removeChild'));
			$removeMethod->setName('remove' . ucfirst(\EBT\ExtensionBuilder\Utility\Inflector::singularize($propertyName)));
			$removeMethod->setTag('param', \EBT\ExtensionBuilder\Utility\Tools::getParamTag($domainProperty, 'remove'), TRUE);
			$removeMethod->setTag('return', 'void');
			$removeMethod->addModifier('public');
			$removeMethod->getParameterByPosition(0)
				->setName($parameterName)
				->setVarType($domainProperty->getForeignClassName())
				->setTypeHint($domainProperty->getForeignClassName());
			$removeMethod->updateParamTags();
			$this->updateMethodBody(
				$removeMethod,
				array(
					'childToRemove' => $parameterName,
					'child' => $domainProperty->getForeignModelName(),
					'children' => $propertyName,
					'Child' => $domainProperty->getForeignModelName()
				)
			);
			$this->updateDocComment(
				$removeMethod,
				array(
					'\bchildToRemove\b' => $parameterName,
					'\bChild\b' => $domainProperty->getForeignModelName()
				)
			);
		}

		$removeParameters = $removeMethod->getParameterNames();
		if (!in_array(\EBT\ExtensionBuilder\Utility\Tools::getParameterName($domainProperty, 'remove'), $removeParameters)) {
			$removeParameter = new Model\ClassObject\MethodParameter(\EBT\ExtensionBuilder\Utility\Tools::getParameterName($domainProperty, 'remove'), TRUE);
			$removeParameter->setName(\EBT\ExtensionBuilder\Utility\Tools::getParameterName($domainProperty, 'remove'))
				->setVarType($domainProperty->getForeignClassName())
				->setTypeHint($domainProperty->getForeignClassName())
				->setTypeForParamTag($domainProperty->getTypeForComment());
			$removeMethod->setParameter($removeParameter);
		}

		if (!$removeMethod->hasDescription()) {
			$removeMethod->setDescription('Removes a ' . $domainProperty->getForeignModelName());
		}
		return $removeMethod;
	}

	/**
	 * Builds a method that checks the current boolean state of a property
	 *
	 * @param Model\DomainObject\AbstractProperty $domainProperty
	 *
	 * @return Model\ClassObject\Method
	 */
	protected function buildIsMethod($domainProperty) {

		$isMethodName = \EBT\ExtensionBuilder\Utility\Tools::getMethodName($domainProperty, 'is');

		if ($this->classObject->methodExists($isMethodName)) {
			$isMethod = $this->classObject->getMethod($isMethodName);
			//$this->logger->log('Existing isMethod imported:' . $isMethodName, 'extension_builder', 0, array('methodBody' => $isMethod->getBody()));
		} else {
			//$this->logger->log('new isMethod:' . $isMethodName, 'extension_builder', 1);
			$isMethod = clone($this->templateClassObject->getMethod('isProperty'));
			$isMethod->setName('is' . ucfirst($domainProperty->getName()));
			$isMethod->setTag('return', 'boolean');
			$replacements =  array('property' => $domainProperty->getName());
			$this->updateMethodBody($isMethod, $replacements);
			$this->updateDocComment($isMethod, $replacements);
			//$isMethod->addModifier('public');
		}

		if (!$isMethod->hasDescription()) {
			$isMethod->setDescription('Returns the boolean state of ' . $domainProperty->getName());
		}
		return $isMethod;
	}

	/**
	 *
	 * @param Model\DomainObject\Action $action
	 * @param Model\DomainObject $domainObject
	 *
	 * @return Model\ClassObject\Method
	 */
	protected function buildActionMethod(Model\DomainObject\Action $action, Model\DomainObject $domainObject) {
		$actionName = $action->getName();
		$actionMethodName = $actionName . 'Action';
		if ($this->templateClassObject->methodExists($actionMethodName)) {
			$actionMethod = $this->templateClassObject->getMethod($actionMethodName);
		} else {
			$actionMethod = clone($this->templateClassObject->getMethod('genericAction'));
			$actionMethod->setName($actionMethodName);
			$actionMethod->setDescription('action ' . $action->getName());
		}
		if (in_array($actionName, array('show', 'edit', 'create', 'new', 'update', 'delete'))) {
				// these actions need a parameter
			if (in_array($actionName, array('create', 'new'))) {
				$parameterName = 'new' . $domainObject->getName();
			} else {
				$parameterName = \TYPO3\CMS\Core\Utility\GeneralUtility::lcfirst($domainObject->getName());
			}
			$actionMethod->getParameterByPosition(0)
				->setName($parameterName)
				->setVarType($domainObject->getFullQualifiedClassName())
				->setTypeHint($domainObject->getFullQualifiedClassName());
			$actionMethod->updateParamTags();

			if ($actionName === 'new') {
				$actionMethod->setTag('ignorevalidation', '$' . $parameterName);
			} elseif ($actionName === 'edit') {
				$actionMethod->setTag('ignorevalidation', '$' . $parameterName);
			}
		}

		$replacements = array(
			'domainObjectRepository' => lcfirst($domainObject->getName()) . 'Repository',
			'domainObject' => lcfirst($domainObject->getName()),
			'domainObjects' => lcfirst(Inflector::pluralize($domainObject->getName())),
			'newDomainObject' => 'new' . $domainObject->getName()
		);
		$this->updateMethodBody($actionMethod, $replacements);
		$updatedDocComment = $this->updateDocComment($actionMethod, $replacements);
		$actionMethod->setDocComment($updatedDocComment);
		return $actionMethod;
	}

	/**
	 *
	 * @param Model\DomainObject\AbstractProperty $property
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
				return 'add' . ucfirst(Inflector::singularize($propertyName));

			case 'remove'	:
				return 'remove' . ucfirst(Inflector::singularize($propertyName));

			case 'is'		:
				return 'is' . ucfirst($propertyName);
		}
	}

	/**
	 * @param Model\ClassObject\Method $method
	 * @param array $replacements
	 * @return void
	 */
	protected function updateMethodBody($method, $replacements) {
		$stmts = $method->getBodyStmts();

		$stmts = current(
			$this->parserService->replaceNodeProperty(
				array($stmts),
				$replacements,
				NULL,
				'name'
			)
		);
		$stmts = current(
			$this->parserService->replaceNodeProperty(
				array($stmts),
				$replacements,
				NULL,
				'value'
			)
		);
		$method->setBodyStmts($stmts);
	}

	/**
	 * @param Model/AbstractObject $object
	 * @param array $replacements
	 */
	protected function updateDocComment($object, $replacements) {
		$docComment = $object->getDocComment();
		// reset all tags (they will be restored from the parsed doc comment string)
		$object->setTags(array());
		$object->setDescriptionLines(array());
		// replace occurences in tags and comments
		$pattern = array_keys($replacements);
		array_walk($pattern, function(&$item) { $item =  '/' . $item . '/';});
		$parsedDocCommentString = preg_replace($pattern, array_values($replacements), $docComment);
		$object->setDocComment($parsedDocCommentString);
	}

	/**
	 *
	 * @param Model\DomainObject\AbstractProperty $property
	 * @param string $methodType (set,add,remove)
	 * @return string method body
	 */
	public function getParameterName($domainProperty, $methodType) {

		$propertyName = $domainProperty->getName();

		switch ($methodType) {

			case 'set'			:
				return $propertyName;

			case 'add'			:
				return Inflector::singularize($propertyName);

			case 'remove'		:
				return Inflector::singularize($propertyName) . 'ToRemove';
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
	 * @param Model\DomainObject $domainObject
	 * @param boolean $mergeWithExistingClass
	 *
	 * @return Model\File
	 */
	public function generateControllerClassFileObject($domainObject, $controllerClassTemplatePath, $mergeWithExistingClass) {
		$this->classObject = NULL;
		$className = $domainObject->getName() . 'Controller';
		$this->templateFileObject = $this->parserService->parseFile($controllerClassTemplatePath);
		$this->templateClassObject = $this->templateFileObject->getFirstClass();
		if ($mergeWithExistingClass) {
			try {
				$this->classFileObject = $this->roundTripService->getControllerClassFile($domainObject);
				if (!is_null($this->classFileObject)) {
					$this->classObject = $this->classFileObject->getFirstClass();
				}
			}
			catch (\Exception $e) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Class ' . $className . ' could not be imported: ' . $e->getMessage(), 'extension_builder');
			}
		}

		if ($this->classObject == NULL) {
			$this->classFileObject = clone($this->templateFileObject);
			$this->classObject = clone($this->templateFileObject->getFirstClass());
			$this->classObject->resetAll();
			$this->classObject->setName($className);
			$this->classObject->setDescription($className);
			if (isset($this->settings['Controller']['parentClass'])) {
				$parentClass = $this->settings['Controller']['parentClass'];
			} else {
				$parentClass = '\\TYPO3\\CMS\\Extbase\\Mvc\\Controller\\ActionController';
			}
			$this->classObject->setParentClassName($parentClass);
		}
		if ($domainObject->isAggregateRoot()) {
			$repositoryName = \TYPO3\CMS\Core\Utility\GeneralUtility::lcfirst($domainObject->getName() . 'Repository');
			// now add the property to class Object (or update an existing class Object property)
			if (!$this->classObject->propertyExists($repositoryName)) {
				$classProperty = $this->templateClassObject->getProperty('domainObjectRepository');
				$classProperty->setName($repositoryName);
				$classProperty->setDescription($repositoryName);
				$classProperty->setTag('var', $domainObject->getFullyQualifiedDomainRepositoryClassName(), TRUE);
				$this->classObject->setProperty($classProperty);
			} if (!$this->classObject->getProperty($repositoryName)->isTaggedWith('inject')
					&& !$this->classObject->methodExists('inject' . ucfirst($repositoryName))) {
				$this->classObject->getProperty($repositoryName)->setTag('inject');
			}
		}
		foreach ($domainObject->getActions() as $action) {
			$actionMethodName = $action->getName() . 'Action';
			if (!$this->classObject->methodExists($actionMethodName)) {
				$actionMethod = $this->buildActionMethod($action, $domainObject);
				$this->classObject->addMethod($actionMethod);
			}
		}
		$this->classFileObject->getNamespace()
			->setName($this->extension->getNamespaceName() . '\\Controller')
			->setClasses(array($this->classObject));
		return $this->classFileObject;
	}

	/**
	 * This method generates the repository class object,
	 * which is passed to the template
	 * it keeps all methods and properties including
	 * user modified method bodies and comments
	 * needed to create a repository class file
	 *
	 * @param Model\DomainObject $domainObject
	 * @param boolean $mergeWithExistingClass
	 *
	 * @return Model\ClassObject\ClassObject
	 */
	public function generateRepositoryClassFileObject($domainObject, $repositoryTemplateClassPath, $mergeWithExistingClass) {
		$this->classObject = NULL;
		$className = $domainObject->getName() . 'Repository';
		$this->templateFileObject = $this->parserService->parseFile($repositoryTemplateClassPath);
		$this->templateClassObject = $this->templateFileObject->getFirstClass();
		if ($mergeWithExistingClass) {
			try {
				$this->classFileObject = $this->roundTripService->getRepositoryClassFile($domainObject);
				if (!is_null($this->classFileObject)) {
					$this->classObject = $this->classFileObject->getFirstClass();
				}
			}
			catch (\Exception $e) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Class ' . $className . ' could not be imported: ' . $e->getMessage(), 'extension_builder');
			}
		}

		if ($this->classObject == NULL) {
			$this->classFileObject = clone($this->templateFileObject);
			$this->classObject = clone($this->templateClassObject);
			$this->classObject->resetAll();
			$this->classObject->setName($className);
			$this->classObject->setNamespaceName($this->extension->getNamespaceName() . '\\Domain\\Repository');
			$this->classObject->setDescription('The repository for ' . Inflector::pluralize($domainObject->getName()));
			if (isset($this->settings['Repository']['parentClass'])) {
				$parentClass = $this->settings['Repository']['parentClass'];
			} else {
				$parentClass = '\\TYPO3\\CMS\\Extbase\\Persistence\\Repository';
			}
			$this->classObject->setParentClassName($parentClass);
		}
		$this->classFileObject->getNamespace()
			->setName($this->extension->getNamespaceName() . '\\Domain\\Repository')
			->setClasses(array($this->classObject));
		return $this->classFileObject;
	}

	/**
	 * Not used right now
	 * TODO: Needs better implementation
	 * @param Model\DomainObject $domainObject
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