<?php
namespace EBT\ExtensionBuilder\Service;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Domain\Model;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter;
use EBT\ExtensionBuilder\Domain\Model\NamespaceObject;
use EBT\ExtensionBuilder\Parser\ClassFactory;
use EBT\ExtensionBuilder\Utility\Inflector;
use EBT\ExtensionBuilder\Utility\Tools;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Builds the required class objects for extbase extensions
 *
 * if an existing classFileObject is passed as argument, the existing class is loaded
 * and modified according to the current modeler configuration
 */
class ClassBuilder implements SingletonInterface
{
    /**
     * The class file object created to container the generated class
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\File
     */
    protected $classFileObject = null;
    /**
     * The current class object
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
     */
    protected $classObject = null;
    /**
     * The template file object used for new created class files
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\File
     */
    protected $templateFileObject = null;
    /**
     * The template class object used for new created classes
     * @var \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
     */
    protected $templateClassObject = null;
    /**
     * @var \EBT\ExtensionBuilder\Parser\ClassFactory
     */
    protected $classFactory = null;
    /**
     * @var \EBT\ExtensionBuilder\Service\ParserService
     */
    protected $parserService = null;
    /**
     * @var \EBT\ExtensionBuilder\Service\Printer
     */
    protected $printerService = null;
    /**
     * @var \EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager
     */
    protected $configurationManager = null;
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\Extension
     */
    protected $extension = null;
    /**
     * @var array
     */
    protected $settings = [];
    /**
     * @var string
     */
    protected $extensionDirectory = '';

    /**
     * @param \EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ExtensionBuilderConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param \EBT\ExtensionBuilder\Service\ParserService $parserService
     * @return void
     */
    public function injectParserService(ParserService $parserService)
    {
        $this->parserService = $parserService;
    }

    /**
     * @param \EBT\ExtensionBuilder\Service\Printer $printerService
     * @return void
     */
    public function injectPrinterService(Printer $printerService)
    {
        $this->printerService = $printerService;
    }

    /**
     * @param \EBT\ExtensionBuilder\Parser\ClassFactory $classFactory
     * @return void
     */
    public function injectClassFactory(ClassFactory $classFactory)
    {
        $this->classFactory = $classFactory;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     *
     * @return void
     * @throws \Exception
     */
    public function initialize(Model\Extension $extension)
    {
        $this->extension = $extension;
        $settings = $extension->getSettings();
        $this->settings = $settings['classBuilder'];
        $this->extensionDirectory = $this->extension->getExtensionDir();
    }

    /**
     * This method generates the class schema object, which is passed to the template
     * it keeps all methods and properties including user modified method bodies and
     * comments needed to create a domain object class file
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     * @param string $modelClassTemplatePath
     * @param \EBT\ExtensionBuilder\Domain\Model\File $existingClassFileObject
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\File
     * @throws \EBT\ExtensionBuilder\Exception\FileNotFoundException
     * @throws \EBT\ExtensionBuilder\Exception\SyntaxError
     */
    public function generateModelClassFileObject($domainObject, $modelClassTemplatePath, $existingClassFileObject = null)
    {
        $this->classObject = null;
        $this->templateFileObject = $this->parserService->parseFile($modelClassTemplatePath);
        $this->templateClassObject = $this->templateFileObject->getFirstClass();
        if ($existingClassFileObject) {
            $this->classFileObject = $existingClassFileObject;
            $this->classObject = $existingClassFileObject->getFirstClass();
            if ($this->classFileObject->getNamespace() === false) {
                $nameSpace = new NamespaceObject('dummy');
                $this->classFileObject->addNamespace($nameSpace);
            }
        }
        if ($this->classObject == null) {
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
            ->setClasses([$this->classObject]);
        return $this->classFileObject;
    }

    /**
     * Create a new class object based on the template and the related domain object
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     *
     * @return void
     */
    protected function createNewModelClassObject($domainObject)
    {
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
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $domainProperty
     * @return void
     */
    protected function addClassProperty($domainProperty)
    {
        // TODO the following part still needs some enhancement:
        // what should be obligatory in existing properties and methods
        $propertyName = $domainProperty->getName();
        // add the property to class Object (or update an existing class Object property)
        if ($this->classObject->propertyExists($propertyName)) {
            $classProperty = $this->classObject->getProperty($propertyName);
            if ($this->settings['setDefaultValuesForClassProperties'] !== false) {
                $classProperty->setDefault($domainProperty->getDefaultValue());
            }
        } else {
            $classProperty = clone($this->templateClassObject->getProperty('property'));
            $classProperty->setName($propertyName);
            $classProperty->setTag('var', $domainProperty->getTypeForComment());
            if ($domainProperty->getDescription()) {
                $classProperty->setDescription($domainProperty->getDescription());
            } else {
                $classProperty->setDescription(str_replace('property', $propertyName, $classProperty->getDescription()));
            }

            if ($domainProperty->getHasDefaultValue() && $this->settings['setDefaultValuesForClassProperties'] !== false) {
                $classProperty->setDefault($domainProperty->getDefaultValue());
            }

            if ($domainProperty->isZeroToManyRelation()) {
                $classProperty->setTag('cascade', 'remove');
            }
        }

        if ($domainProperty->getRequired()) {
            if (!$classProperty->isTaggedWith('validate')) {
                $validateTag = explode(' ', trim($domainProperty->getValidateAnnotation()));
                $classProperty->setTag('validate', $validateTag[1]);
            }
        }

        if ($domainProperty->getCascadeRemove()) {
            if (!$classProperty->isTaggedWith('cascade')) {
                $validateTag = explode(' ', trim($domainProperty->getCascadeRemoveAnnotation()));
                $classProperty->setTag('cascade', $validateTag[1]);
            }
        }

        if ($domainProperty->isRelation()) {
            /** @var $domainProperty \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation */
            if ($domainProperty->getLazyLoading()) {
                if (!$classProperty->isTaggedWith('lazy')) {
                    $classProperty->setTag('lazy', '');
                }
            }
        }

        $this->classObject->setProperty($classProperty);
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     *
     * @return void
     * @throws \EBT\ExtensionBuilder\Exception\SyntaxError
     * @throws \EBT\ExtensionBuilder\Exception\FileNotFoundException
     */
    protected function addInitStorageObjectCalls(Model\DomainObject $domainObject)
    {
        $anyToManyRelationProperties = $domainObject->getAnyToManyRelationProperties();
        if (count($anyToManyRelationProperties) > 0) {
            if (!$this->classObject->methodExists('__construct')) {
                $constructorMethod = $this->templateClassObject->getMethod('__construct');
                $constructorMethod->setDescription('__construct');
                $this->classObject->addMethod($constructorMethod);
            } else {
                $constructorMethod = $this->classObject->getMethod('__construct');
            }
            if (preg_match('/\$this->initStorageObjects()/', $this->printerService->render($constructorMethod->getBodyStmts())) < 1) {
                $this->classObject->setMethod($this->classObject->getMethod('__construct'));
            }
            $initStorageObjectsMethod = clone($this->templateClassObject->getMethod('initStorageObjects'));
            $methodBodyStmts = [];
            $templateBodyStmts = $initStorageObjectsMethod->getBodyStmts();
            $initStorageObjectsMethod->setModifier('protected');
            foreach ($anyToManyRelationProperties as $relationProperty) {
                $methodBodyStmts = array_merge($methodBodyStmts, $this->parserService->replaceNodeProperty($templateBodyStmts, ['children' => $relationProperty->getName()], ['Expr_PropertyFetch', 'Expr_Variable']));
            }
            $initStorageObjectsMethod->setBodyStmts($methodBodyStmts);
            $this->classObject->setMethod($initStorageObjectsMethod);
        } elseif ($this->classObject->methodExists('initStorageObjects')) {
            $this->classObject->getMethod('initStorageObjects')->setBodyStmts([]);
        }
    }

    /**
     * add all setter/getter/add/remove etc. methods
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $domainProperty
     *
     * @return void
     * @throws \EBT\ExtensionBuilder\Exception\FileNotFoundException
     * @throws \EBT\ExtensionBuilder\Exception\SyntaxError
     */
    protected function setPropertyRelatedMethods($domainProperty)
    {
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
        if (strpos($domainProperty->getTypeForComment(), 'bool') === 0) {
            $isMethod = $this->buildIsMethod($domainProperty);
            $this->classObject->setMethod($isMethod);
        }
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $domainProperty
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method
     */
    protected function buildGetterMethod($domainProperty)
    {
        $propertyName = $domainProperty->getName();
        // add (or update) a getter method
        $getterMethodName = self::getMethodName($domainProperty, 'get');
        if ($this->classObject->methodExists($getterMethodName)) {
            $getterMethod = $this->classObject->getMethod($getterMethodName);
        } else {
            $getterMethod = clone $this->templateClassObject->getMethod('getProperty')->setName($getterMethodName);
            $replacements = ['property' => $propertyName];
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
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $domainProperty
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method
     */
    protected function buildSetterMethod($domainProperty)
    {
        $propertyName = $domainProperty->getName();
        // add (or update) a setter method
        $setterMethodName = self::getMethodName($domainProperty, 'set');
        if ($this->classObject->methodExists($setterMethodName)) {
            $setterMethod = $this->classObject->getMethod($setterMethodName);
        } else {
            $setterMethod = clone $this->templateClassObject->getMethod('setProperty');
            $setterMethod->setName('set' . ucfirst($propertyName));
            $replacements = ['property' => $propertyName];
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
            $setterParameter = new MethodParameter($propertyName);
            $setterParameter->setVarType($domainProperty->getTypeForComment());
            if (is_subclass_of($domainProperty, 'Model\\DomainObject\\Relation\\AbstractRelation')) {
                $setterParameter->setTypeHint($domainProperty->getTypeHint());
            }
            $setterMethod->setParameter($setterParameter);
        }
        return $setterMethod;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation $domainProperty
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method
     * @throws \EBT\ExtensionBuilder\Exception\FileNotFoundException
     * @throws \EBT\ExtensionBuilder\Exception\SyntaxError
     */
    protected function buildAddMethod($domainProperty)
    {
        $propertyName = $domainProperty->getName();
        $addMethodName = self::getMethodName($domainProperty, 'add');

        if ($this->classObject->methodExists($addMethodName)) {
            $addMethod = $this->classObject->getMethod($addMethodName);
        } else {
            $addMethod = clone($this->templateClassObject->getMethod('addChild'));
            $addMethod->setName('add' . ucfirst(Inflector::singularize($propertyName)));

            $this->updateMethodBody(
                $addMethod,
                [
                    'child' => Inflector::singularize($propertyName),
                    'children' => $propertyName,
                    'Child' => $domainProperty->getForeignModelName()
                ]
            );
            $this->updateDocComment(
                $addMethod,
                [
                    '\bchild\b' => Inflector::singularize($propertyName),
                    '\bchildren\b' => $propertyName,
                    '\bChild\b' => $domainProperty->getForeignModelName()
                ]
            );

            $addMethod->setTag('param', Tools::getParamTag($domainProperty, 'add'));
            $addMethod->getParameterByPosition(0)
                ->setName(Inflector::singularize($propertyName))
                ->setVarType($domainProperty->getForeignClassName())
                ->setTypeHint($domainProperty->getForeignClassName());
            $addMethod->setTag('return', 'void');
            $addMethod->addModifier('public');
        }
        $addParameters = $addMethod->getParameterNames();

        if (!in_array(Inflector::singularize($propertyName), $addParameters)) {
            $addParameter = new MethodParameter(Tools::getParameterName($domainProperty, 'add'));
            $addParameter->setVarType($domainProperty->getForeignClassName());
            $addMethod->setParameter($addParameter);
        }
        if (!$addMethod->hasDescription()) {
            $addMethod->setDescription('Adds a ' . $domainProperty->getForeignModelName());
        }
        return $addMethod;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation $domainProperty
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method
     * @throws \EBT\ExtensionBuilder\Exception\FileNotFoundException
     * @throws \EBT\ExtensionBuilder\Exception\SyntaxError
     */
    protected function buildRemoveMethod($domainProperty)
    {
        $propertyName = $domainProperty->getName();
        $removeMethodName = self::getMethodName($domainProperty, 'remove');
        $parameterName = Tools::getParameterName($domainProperty, 'remove');

        if ($this->classObject->methodExists($removeMethodName)) {
            $removeMethod = $this->classObject->getMethod($removeMethodName);
        } else {
            $removeMethod = clone($this->templateClassObject->getMethod('removeChild'));
            $removeMethod->setName('remove' . ucfirst(Inflector::singularize($propertyName)));
            $removeMethod->setTag('param', Tools::getParamTag($domainProperty, 'remove'), true);
            $removeMethod->setTag('return', 'void');
            $removeMethod->addModifier('public');
            $removeMethod->getParameterByPosition(0)
                ->setName($parameterName)
                ->setVarType($domainProperty->getForeignClassName())
                ->setTypeHint($domainProperty->getForeignClassName());
            $removeMethod->updateParamTags();
            $this->updateMethodBody(
                $removeMethod,
                [
                    'childToRemove' => $parameterName,
                    'child' => $domainProperty->getForeignModelName(),
                    'children' => $propertyName,
                    'Child' => $domainProperty->getForeignModelName()
                ]
            );
            $this->updateDocComment(
                $removeMethod,
                [
                    '\bchildToRemove\b' => $parameterName,
                    '\bChild\b' => $domainProperty->getForeignModelName()
                ]
            );
        }

        $removeParameters = $removeMethod->getParameterNames();
        if (!in_array(Tools::getParameterName($domainProperty, 'remove'), $removeParameters)) {
            $removeParameter = new MethodParameter(Tools::getParameterName($domainProperty, 'remove'));
            $removeParameter->setName(Tools::getParameterName($domainProperty, 'remove'))
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
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $domainProperty
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method
     */
    protected function buildIsMethod($domainProperty)
    {
        $isMethodName = self::getMethodName($domainProperty, 'is');

        if ($this->classObject->methodExists($isMethodName)) {
            $isMethod = $this->classObject->getMethod($isMethodName);
        } else {
            $isMethod = clone($this->templateClassObject->getMethod('isProperty'));
            $isMethod->setName('is' . ucfirst($domainProperty->getName()));
            $isMethod->setTag('return', 'bool');
            $replacements = ['property' => $domainProperty->getName()];
            $this->updateMethodBody($isMethod, $replacements);
            $this->updateDocComment($isMethod, $replacements);
        }

        if (!$isMethod->hasDescription()) {
            $isMethod->setDescription('Returns the boolean state of ' . $domainProperty->getName());
        }
        return $isMethod;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\Action $action
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method
     */
    protected function buildActionMethod(Model\DomainObject\Action $action, Model\DomainObject $domainObject)
    {
        $actionName = $action->getName();
        $actionMethodName = $actionName . 'Action';
        if ($this->templateClassObject->methodExists($actionMethodName)) {
            $actionMethod = $this->templateClassObject->getMethod($actionMethodName);
        } else {
            $actionMethod = clone($this->templateClassObject->getMethod('genericAction'));
            $actionMethod->setName($actionMethodName);
            $actionMethod->setDescription('action ' . $action->getName());
        }
        if (in_array($actionName, ['show', 'edit', 'create', 'update', 'delete'])) {
            // these actions need a parameter
            if (in_array($actionName, ['create'])) {
                $parameterName = 'new' . $domainObject->getName();
            } else {
                $parameterName = lcfirst($domainObject->getName());
            }
            $actionMethod->getParameterByPosition(0)
                ->setName($parameterName)
                ->setVarType($domainObject->getFullQualifiedClassName())
                ->setTypeHint($domainObject->getFullQualifiedClassName());
            $actionMethod->updateParamTags();

            if ($actionName === 'edit') {
                $actionMethod->setTag('ignorevalidation', '$' . $parameterName);
            }
        }

        $replacements = [
            'domainObjectRepository' => lcfirst($domainObject->getName()) . 'Repository',
            'domainObject' => lcfirst($domainObject->getName()),
            'domainObjects' => lcfirst(Inflector::pluralize($domainObject->getName())),
            'newDomainObject' => 'new' . $domainObject->getName()
        ];
        $this->updateMethodBody($actionMethod, $replacements);
        $this->updateDocComment($actionMethod, $replacements);
        return $actionMethod;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $domainProperty
     * @param string $methodType (get,set,add,remove,is)
     * @return string method name
     */
    public static function getMethodName($domainProperty, $methodType)
    {
        $propertyName = $domainProperty->getName();
        switch ($methodType) {
            case 'set':
                return 'set' . ucfirst($propertyName);

            case 'get':
                return 'get' . ucfirst($propertyName);

            case 'add':
                return 'add' . ucfirst(Inflector::singularize($propertyName));

            case 'remove':
                return 'remove' . ucfirst(Inflector::singularize($propertyName));

            case 'is':
                return 'is' . ucfirst($propertyName);
        }
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method $method
     * @param array $replacements
     * @return void
     */
    protected function updateMethodBody($method, $replacements)
    {
        $stmts = $method->getBodyStmts();

        $stmts = $this->parserService->replaceNodeProperty(
            $stmts,
            $replacements,
            null,
            'name'
        );
        $stmts = $this->parserService->replaceNodeProperty(
            $stmts,
            $replacements,
            null,
            'value'
        );
        $method->setBodyStmts($stmts);
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\AbstractObject $object
     * @param array $replacements
     */
    protected function updateDocComment($object, $replacements)
    {
        $docComment = $object->getDocComment();
        // reset all tags (they will be restored from the parsed doc comment string)
        $object->setTags([]);
        $object->setDescriptionLines([]);
        // replace occurrences in tags and comments
        $pattern = array_keys($replacements);
        array_walk($pattern, function (&$item) {
            $item = '/' . $item . '/';
        });
        $parsedDocCommentString = preg_replace($pattern, array_values($replacements), $docComment);
        $object->setDocComment($parsedDocCommentString);
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $domainProperty
     * @param string $methodType (set,add,remove)
     * @return string method body
     */
    public static function getParameterName($domainProperty, $methodType)
    {
        $propertyName = $domainProperty->getName();

        switch ($methodType) {

            case 'set':
                return $propertyName;

            case 'add':
                return Inflector::singularize($propertyName);

            case 'remove':
                return Inflector::singularize($propertyName) . 'ToRemove';
        }
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $domainProperty
     * @param string $methodType
     *
     * @return string
     */
    public static function getParamTag($domainProperty, $methodType)
    {
        switch ($methodType) {
            case 'set':
                return $domainProperty->getTypeForComment() . ' $' . $domainProperty->getName();

            case 'add':
                /** @var $domainProperty \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation */
                $paramTag = $domainProperty->getForeignClassName();
                $paramTag .= ' $' . self::getParameterName($domainProperty, 'add');
                return $paramTag;

            case 'remove':
                /** @var $domainProperty \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation */
                $paramTag = $domainProperty->getForeignClassName();
                $paramTag .= ' $' . self::getParameterName($domainProperty, 'remove');
                $paramTag .= ' The ' . $domainProperty->getForeignModelName() . ' to be removed';
                return $paramTag;
        }
    }

    /**
     * This method generates the class object, which is passed to the template
     * it keeps all methods and properties including user modified method bodies and
     * comments that are required to create a controller class file
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     * @param $controllerClassTemplatePath
     * @param \EBT\ExtensionBuilder\Domain\Model\File $existingClassFileObject
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\File
     * @throws \EBT\ExtensionBuilder\Exception\FileNotFoundException
     */
    public function generateControllerClassFileObject($domainObject, $controllerClassTemplatePath, $existingClassFileObject = null)
    {
        $this->classObject = null;
        $className = $domainObject->getName() . 'Controller';
        $this->templateFileObject = $this->parserService->parseFile($controllerClassTemplatePath);
        $this->templateClassObject = $this->templateFileObject->getFirstClass();

        if ($existingClassFileObject) {
            $this->classFileObject = $existingClassFileObject;
            $this->classObject = $existingClassFileObject->getFirstClass();
            if ($this->classFileObject->getNamespace() === false) {
                $nameSpace = new NamespaceObject('dummy');
                $this->classFileObject->addNamespace($nameSpace);
            }
        }

        if ($this->classObject == null) {
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
            $repositoryName = lcfirst($domainObject->getName() . 'Repository');
            // now add the property to class Object (or update an existing class Object property)
            if (!$this->classObject->propertyExists($repositoryName)) {
                $classProperty = $this->templateClassObject->getProperty('domainObjectRepository');
                $classProperty->setName($repositoryName);
                $classProperty->setDescription($repositoryName);
                $classProperty->setTag('var', $domainObject->getFullyQualifiedDomainRepositoryClassName(), true);
                $this->classObject->setProperty($classProperty);
            }
            if (!$this->classObject->getProperty($repositoryName)->isTaggedWith('inject')
                && !$this->classObject->methodExists('inject' . ucfirst($repositoryName))
            ) {
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
            ->setClasses([$this->classObject]);
        return $this->classFileObject;
    }

    /**
     * This method generates the repository class object,
     * which is passed to the template
     * it keeps all methods and properties including
     * user modified method bodies and comments
     * needed to create a repository class file
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     * @param $repositoryTemplateClassPath
     * @param \EBT\ExtensionBuilder\Domain\Model\File $existingClassFileObject
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\File
     * @throws \EBT\ExtensionBuilder\Exception\FileNotFoundException
     */
    public function generateRepositoryClassFileObject($domainObject, $repositoryTemplateClassPath, $existingClassFileObject = null)
    {
        $this->classObject = null;
        $className = $domainObject->getName() . 'Repository';
        $this->templateFileObject = $this->parserService->parseFile($repositoryTemplateClassPath);
        $this->templateClassObject = $this->templateFileObject->getFirstClass();
        if ($existingClassFileObject) {
            $this->classFileObject = $existingClassFileObject;
            $this->classObject = $existingClassFileObject->getFirstClass();
            if ($this->classFileObject->getNamespace() === false) {
                $nameSpace = new NamespaceObject('dummy');
                $this->classFileObject->addNamespace($nameSpace);
            }
        }

        if ($this->classObject == null) {
            $this->classFileObject = clone($this->templateFileObject);
            $this->classObject = clone($this->templateClassObject);
            $this->classObject->resetAll();
            $this->classObject->setName($className);
            $this->classObject->setDescription('The repository for ' . Inflector::pluralize($domainObject->getName()));
            if (isset($this->settings['Repository']['parentClass'])) {
                $parentClass = $this->settings['Repository']['parentClass'];
            } else {
                $parentClass = '\\TYPO3\\CMS\\Extbase\\Persistence\\Repository';
            }
            $this->classObject->setParentClassName($parentClass);
        }
        if ($domainObject->getSorting() && is_null($this->classObject->getProperty('defaultOrderings'))) {
            $defaultOrderings = $this->templateClassObject->getProperty('defaultOrderings');
            $this->classObject->addProperty($defaultOrderings);
        }
        $this->classFileObject->getNamespace()
            ->setName($this->extension->getNamespaceName() . '\\Domain\\Repository')
            ->setClasses([$this->classObject]);
        return $this->classFileObject;
    }

    /**
     * Not used right now
     * TODO: Needs better implementation
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     * @return void
     */
    public function sortMethods($domainObject)
    {
        $objectProperties = $domainObject->getProperties();
        $sortedProperties = [];
        $propertyRelatedMethods = [];
        $customMethods = [];

        // sort all properties and methods according to domainObject sort order
        foreach ($objectProperties as $objectProperty) {
            if ($this->classObject->propertyExists($objectProperty->getName())) {
                $sortedProperties[$objectProperty->getName()] = $this->classObject->getProperty($objectProperty->getName());
                $methodPrefixes = ['get', 'set', 'add', 'remove', 'is'];
                foreach ($methodPrefixes as $methodPrefix) {
                    $methodName = self::getMethodName($objectProperty, $methodPrefix);
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

        $this->classObject->setProperties($sortedProperties);
        $this->classObject->setMethods($sortedMethods);
    }
}
