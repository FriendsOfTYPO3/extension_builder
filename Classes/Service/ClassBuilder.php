<?php

declare(strict_types=1);

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

namespace EBT\ExtensionBuilder\Service;

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Domain\Model\AbstractObject;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\Method;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter;
use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Action;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation;
use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Domain\Model\File;
use EBT\ExtensionBuilder\Domain\Model\NamespaceObject;
use EBT\ExtensionBuilder\Exception\FileNotFoundException;
use EBT\ExtensionBuilder\Exception\SyntaxError;
use EBT\ExtensionBuilder\Parser\ClassFactory;
use EBT\ExtensionBuilder\Utility\Inflector;
use EBT\ExtensionBuilder\Utility\Tools;
use Exception;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Builds the required class objects for extbase extensions
 *
 * if an existing classFileObject is passed as argument, the existing class is loaded
 * and modified according to the current modeler configuration
 */
class ClassBuilder implements SingletonInterface
{
    public const VALIDATE_ANNOTATION = 'TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")';
    public const CASCADE_REMOVE_ANNOTATION = 'TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")';
    public const LAZY_ANNOTATION = 'TYPO3\CMS\Extbase\Annotation\ORM\Lazy';

    protected ExtensionBuilderConfigurationManager $configurationManager;
    protected ParserService $parserService;
    protected Printer $printerService;
    protected ClassFactory $classFactory;
    /**
     * The class file object created to container the generated class
     */
    protected ?File $classFileObject = null;
    protected ?ClassObject $classObject = null;
    /**
     * The template file object used for new created class files
     */
    protected ?File $templateFileObject = null;
    /**
     * The template class object used for new created classes
     */
    protected ?ClassObject $templateClassObject = null;
    protected ?Extension $extension = null;
    protected array $settings = [];
    protected string $extensionDirectory = '';

    public function injectConfigurationManager(ExtensionBuilderConfigurationManager $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
    }

    public function injectParserService(ParserService $parserService): void
    {
        $this->parserService = $parserService;
    }

    public function injectPrinterService(Printer $printerService): void
    {
        $this->printerService = $printerService;
    }

    public function injectClassFactory(ClassFactory $classFactory): void
    {
        $this->classFactory = $classFactory;
    }

    /**
     * @param Extension $extension
     *
     * @throws Exception
     */
    public function initialize(Extension $extension): void
    {
        $this->extension = $extension;
        $settings = $extension->getSettings();
        $this->settings = $settings['classBuilder'] ?? [];
        $this->extensionDirectory = $this->extension->getExtensionDir();
    }

    /**
     * This method generates the class schema object, which is passed to the template
     * it keeps all methods and properties including user modified method bodies and
     * comments needed to create a domain object class file
     *
     * @param DomainObject $domainObject
     * @param string $modelClassTemplatePath
     * @param File|null $existingClassFileObject
     *
     * @return File
     * @throws FileNotFoundException
     * @throws SyntaxError
     */
    public function generateModelClassFileObject(
        DomainObject $domainObject,
        string $modelClassTemplatePath,
        ?File $existingClassFileObject = null
    ): File {
        $this->classObject = null;
        $this->templateFileObject = $this->parserService->parseFile($modelClassTemplatePath);
        $this->templateClassObject = $this->templateFileObject->getFirstClass();
        if ($existingClassFileObject instanceof File) {
            $this->classFileObject = $existingClassFileObject;
            $this->classObject = $existingClassFileObject->getFirstClass();
            if ($this->classFileObject->getNamespace() === false) {
                $nameSpace = new NamespaceObject('dummy');
                $this->classFileObject->addNamespace($nameSpace);
            }
        }
        if ($this->classObject === null) {
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
     * @param DomainObject $domainObject
     */
    protected function createNewModelClassObject(DomainObject $domainObject): void
    {
        $this->classFileObject = clone $this->templateFileObject;
        $this->classObject = clone $this->templateFileObject->getFirstClass();
        $this->classObject->resetAll(); // start with plain class
        $this->classObject->setName($domainObject->getName());
        if ($domainObject->isEntity()) {
            $parentClass = $domainObject->getParentClass();
            if (empty($parentClass)) {
                $parentClass = $this->configurationManager->getParentClassForEntityObject($this->extension);
            }
        } else {
            $parentClass = $this->configurationManager->getParentClassForValueObject($this->extension);
        }
        $this->classObject->setParentClassName($parentClass);
        $this->classObject->setDescription($domainObject->getDescription());
    }

    /**
     * @param AbstractProperty $domainProperty
     */
    protected function addClassProperty(AbstractProperty $domainProperty): void
    {
        // TODO the following part still needs some enhancement:
        // what should be obligatory in existing properties and methods
        $propertyName = $domainProperty->getName();
        // add the property to class Object (or update an existing class Object property)
        if ($this->classObject->propertyExists($propertyName)) {
            $classProperty = $this->classObject->getProperty($propertyName);
            $classProperty->setTag('var', $domainProperty->getTypeForComment());
            $classProperty->setVarType($domainProperty->getTypeHint());
            if ($this->settings['setDefaultValuesForClassProperties'] !== false) {
                $classProperty->setDefault($domainProperty->getDefaultValue());
            }
            if ($domainProperty->isNullableProperty() === true && $domainProperty->getNullable() === true) {
                $classProperty->setDefault(null);
            }
        } else {
            $classProperty = clone $this->templateClassObject->getProperty('property');
            $classProperty->setName($propertyName);
            $classProperty->setTag('var', $domainProperty->getTypeForComment());
            $classProperty->setVarType($domainProperty->getTypeHint());
            if ($domainProperty->getDescription()) {
                $classProperty->setDescription($domainProperty->getDescription());
            } else {
                $classProperty->setDescription(str_replace(
                    'property',
                    $propertyName,
                    $classProperty->getDescription()
                ));
            }

            if ($domainProperty->getHasDefaultValue() && ($this->settings['setDefaultValuesForClassProperties'] ?? false) !== false) {
                $classProperty->setDefault($domainProperty->getDefaultValue());
            }
            if ($domainProperty->isNullableProperty() === true && $domainProperty->getNullable() === true) {
                $classProperty->setDefault(null);
            }

            if ($domainProperty instanceof ZeroToManyRelation && ($domainProperty->getRenderType() ?: 'inline') === 'inline') {
                $classProperty->setTag(self::CASCADE_REMOVE_ANNOTATION);
            }
        }

        if ($domainProperty->getRequired() && !$classProperty->isTaggedWith(self::VALIDATE_ANNOTATION)) {
            $classProperty->setTag(self::VALIDATE_ANNOTATION);
        } elseif (!$domainProperty->getRequired() && $classProperty->isTaggedWith(self::VALIDATE_ANNOTATION)) {
            $classProperty->removeTag(self::VALIDATE_ANNOTATION);
        }

        if ($domainProperty->getCascadeRemove() && !$classProperty->isTaggedWith(self::CASCADE_REMOVE_ANNOTATION)) {
            $classProperty->setTag(self::CASCADE_REMOVE_ANNOTATION);
        } elseif (!$domainProperty->getCascadeRemove() && $classProperty->isTaggedWith(self::CASCADE_REMOVE_ANNOTATION)) {
            $classProperty->removeTag(self::CASCADE_REMOVE_ANNOTATION);
        }

        if ($domainProperty->isRelation()) {
            /** @var AbstractRelation $domainProperty */
            if ($domainProperty->getLazyLoading() && !$classProperty->isTaggedWith(self::LAZY_ANNOTATION)) {
                $classProperty->setTag(self::LAZY_ANNOTATION);
            } elseif (!$domainProperty->getLazyLoading() && $classProperty->isTaggedWith(self::LAZY_ANNOTATION)) {
                $classProperty->removeTag(self::LAZY_ANNOTATION);
            }
        }

        $this->classObject->setProperty($classProperty);
    }

    /**
     * @param DomainObject $domainObject
     *
     * @throws SyntaxError
     * @throws FileNotFoundException
     */
    protected function addInitStorageObjectCalls(DomainObject $domainObject): void
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
            if (preg_match(
                '/\$this->initializeObject\\(\\)/',
                $this->printerService->render($constructorMethod->getBodyStmts())
            ) < 1) {
                $this->classObject->setMethod($this->classObject->getMethod('__construct'));
            }
            $initObjectMethod = clone $this->templateClassObject->getMethod('initializeObject');
            $methodBodyStmts = [];
            $templateBodyStmts = $initObjectMethod->getBodyStmts();
            $initObjectMethod->setModifier('public');
            foreach ($anyToManyRelationProperties as $relationProperty) {
                $methodBodyStmts = array_merge(
                    $methodBodyStmts,
                    $this->parserService->replaceNodeProperty(
                        $templateBodyStmts,
                        ['children' => $relationProperty->getName()],
                        ['Expr_PropertyFetch', 'Expr_Variable']
                    )
                );
            }
            $initObjectMethod->setBodyStmts($methodBodyStmts);
            $this->classObject->setMethod($initObjectMethod);
        } elseif ($this->classObject->methodExists('initializeObject')) {
            $this->classObject->getMethod('initializeObject')->setBodyStmts([]);
        }
    }

    /**
     * add all setter/getter/add/remove etc. methods
     *
     * @param AbstractProperty $domainProperty
     *
     * @throws FileNotFoundException
     * @throws SyntaxError
     */
    protected function setPropertyRelatedMethods(AbstractProperty $domainProperty): void
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

    protected function buildGetterMethod(AbstractProperty $domainProperty): Method
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
            $getterMethod->setTag('return', $domainProperty->getTypeForComment());
        }
        if (!$getterMethod->hasDescription()) {
            $getterMethod->setDescription('Returns the ' . $domainProperty->getName());
        }
        return $getterMethod;
    }

    protected function buildSetterMethod(AbstractProperty $domainProperty): Method
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
            $setterMethod->getParameterByPosition(0)
                ->setName($propertyName)
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
     * @param AbstractRelation $domainProperty
     *
     * @return Method
     * @throws FileNotFoundException
     * @throws SyntaxError
     */
    protected function buildAddMethod(AbstractRelation $domainProperty): Method
    {
        $propertyName = $domainProperty->getName();
        $addMethodName = self::getMethodName($domainProperty, 'add');

        if ($this->classObject->methodExists($addMethodName)) {
            $addMethod = $this->classObject->getMethod($addMethodName);
        } else {
            $addMethod = clone $this->templateClassObject->getMethod('addChild');
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
     * @param AbstractRelation $domainProperty
     *
     * @return Method
     * @throws FileNotFoundException
     * @throws SyntaxError
     */
    protected function buildRemoveMethod(AbstractRelation $domainProperty): Method
    {
        $propertyName = $domainProperty->getName();
        $removeMethodName = self::getMethodName($domainProperty, 'remove');
        $parameterName = Tools::getParameterName($domainProperty, 'remove');

        if ($this->classObject->methodExists($removeMethodName)) {
            $removeMethod = $this->classObject->getMethod($removeMethodName);
        } else {
            $removeMethod = clone $this->templateClassObject->getMethod('removeChild');
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
     * @param AbstractProperty $domainProperty
     *
     * @return Method
     */
    protected function buildIsMethod(AbstractProperty $domainProperty): Method
    {
        $isMethodName = self::getMethodName($domainProperty, 'is');

        if ($this->classObject->methodExists($isMethodName)) {
            $isMethod = $this->classObject->getMethod($isMethodName);
        } else {
            $isMethod = clone $this->templateClassObject->getMethod('isProperty');
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
     * @param Action $action
     * @param DomainObject $domainObject
     *
     * @return Method
     */
    protected function buildActionMethod(Action $action, DomainObject $domainObject): Method
    {
        $actionName = $action->getName();
        $actionMethodName = $actionName . 'Action';
        if ($this->templateClassObject->methodExists($actionMethodName)) {
            $actionMethod = $this->templateClassObject->getMethod($actionMethodName);
        } else {
            $actionMethod = clone $this->templateClassObject->getMethod('genericAction');
            $actionMethod->setName($actionMethodName);
            $actionMethod->setDescription('action ' . $action->getName());
        }
        if (in_array($actionName, ['show', 'edit', 'create', 'update', 'delete'])) {
            // these actions need a parameter
            if ($actionName === 'create') {
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
                $actionMethod->setTag('TYPO3\CMS\Extbase\Annotation\IgnoreValidation("' . $parameterName . '")');
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
     * @param AbstractProperty $domainProperty
     * @param string $methodType (get,set,add,remove,is)
     * @return string method name
     */
    public static function getMethodName(AbstractProperty $domainProperty, string $methodType): ?string
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
        return null;
    }

    protected function updateMethodBody(Method $method, array $replacements): void
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

    protected function updateDocComment(AbstractObject $object, array $replacements): void
    {
        $docComment = $object->getDocComment();
        // reset all tags (they will be restored from the parsed doc comment string)
        $object->setTags([]);
        $object->setDescriptionLines([]);
        // replace occurrences in tags and comments
        $pattern = array_keys($replacements);
        array_walk($pattern, static function (&$item): void {
            $item = '/' . $item . '/';
        });
        $parsedDocCommentString = preg_replace($pattern, array_values($replacements), $docComment);
        $object->setDocComment($parsedDocCommentString);
    }

    public static function getParameterName(AbstractProperty $domainProperty, string $methodType): ?string
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
        return null;
    }

    public static function getParamTag(AbstractProperty $domainProperty, string $methodType): ?string
    {
        switch ($methodType) {
            case 'set':
                return $domainProperty->getTypeForComment() . ' $' . $domainProperty->getName();
            case 'add':
                /** @var AbstractRelation $domainProperty */
                $paramTag = $domainProperty->getForeignClassName();
                $paramTag .= ' $' . self::getParameterName($domainProperty, 'add');
                return $paramTag;
            case 'remove':
                /** @var AbstractRelation $domainProperty */
                $paramTag = $domainProperty->getForeignClassName();
                $paramTag .= ' $' . self::getParameterName($domainProperty, 'remove');
                $paramTag .= ' The ' . $domainProperty->getForeignModelName() . ' to be removed';
                return $paramTag;
        }
        return null;
    }

    /**
     * This method generates the class object, which is passed to the template
     * it keeps all methods and properties including user modified method bodies and
     * comments that are required to create a controller class file
     *
     * @param DomainObject $domainObject
     * @param string $controllerClassTemplatePath
     * @param File|null $existingClassFileObject
     *
     * @return File
     * @throws FileNotFoundException
     */
    public function generateControllerClassFileObject(
        DomainObject $domainObject,
        string $controllerClassTemplatePath,
        ?File $existingClassFileObject = null
    ): File {
        $this->classObject = null;
        $className = $domainObject->getName() . 'Controller';
        $this->templateFileObject = $this->parserService->parseFile($controllerClassTemplatePath);
        $this->templateClassObject = $this->templateFileObject->getFirstClass();

        if ($existingClassFileObject instanceof File) {
            $this->classFileObject = $existingClassFileObject;
            $this->classObject = $existingClassFileObject->getFirstClass();
            if ($this->classFileObject->getNamespace() === false) {
                $nameSpace = new NamespaceObject('dummy');
                $this->classFileObject->addNamespace($nameSpace);
            }
        }

        if ($this->classObject === null) {
            $this->classFileObject = clone $this->templateFileObject;
            $this->classObject = clone $this->templateFileObject->getFirstClass();
            $this->classObject->resetAll();
            $this->classObject->setName($className);
            $this->classObject->setDescription($className);
            $parentClass = $this->settings['Controller']['parentClass'] ?? '\\TYPO3\\CMS\\Extbase\\Mvc\\Controller\\ActionController';
            $this->classObject->setParentClassName($parentClass);
        }

        if ($domainObject->isAggregateRoot()) {


            if($domainObject->getControllerScope() === "Backend") {
                $moduleTemplateName = 'moduleTemplate';
                if (!$this->classObject->propertyExists($moduleTemplateName)) {
                    /** @var AbstractProperty $classProperty */
                    $classProperty = $this->templateClassObject->getProperty('moduleTemplate');
                    $classProperty->setName($moduleTemplateName);
                    $classProperty->setDescription($moduleTemplateName);
                    // $classProperty->setTag('var', 'ModuleTemplate $moduleTemplate', true);
                    $this->classObject->setProperty($classProperty);
                }

                // Only set moduleTemplateFactory for Scope -> Backend
                $moduleTemplateFactoryName = 'moduleTemplateFactory';
                if (!$this->classObject->propertyExists($moduleTemplateFactoryName)) {
                    /** @var AbstractProperty $classProperty */
                    $classProperty = $this->templateClassObject->getProperty('moduleTemplateFactory');
                    $classProperty->setName($moduleTemplateFactoryName);
                    $classProperty->setDescription($moduleTemplateFactoryName);
                    // $classProperty->setTag('var', 'ModuleTemplate $moduleTemplate', true);
                    $this->classObject->setProperty($classProperty);
                }
            }

            $repositoryName = lcfirst($domainObject->getName() . 'Repository');
            // now add the property to class Object (or update an existing class Object property)
            if (!$this->classObject->propertyExists($repositoryName)) {
                /** @var AbstractProperty $classProperty */
                $classProperty = $this->templateClassObject->getProperty('domainObjectRepository');
                $classProperty->setName($repositoryName);
                $classProperty->setDescription($repositoryName);
                $classProperty->setTag('var', $domainObject->getFullyQualifiedDomainRepositoryClassName(), true);
                $this->classObject->setProperty($classProperty);
            }

            if (!$this->classObject->methodExists('__construct')) {
                $constructorMethod = $this->buildConstructorMethod($domainObject);
                $this->classObject->addMethod($constructorMethod);
            }

            if (!$this->classObject->methodExists('inject' . ucfirst($repositoryName))) {
                $injectRepositoryMethod = $this->buildInjectMethod($domainObject);
                $this->classObject->addMethod($injectRepositoryMethod);
            }

            if($domainObject->getControllerScope() === "Backend") {
                // Only set initializeAction for Scope -> Backend
                if (!$this->classObject->methodExists('initializeAction')) {
                    $initializeActionMethod = $this->buildInitializeActionMethod($domainObject);
                    $this->classObject->addMethod($initializeActionMethod);
                }
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

    protected function buildConstructorMethod(DomainObject $domainObject): Method
    {
        $constructorName = '__construct';
        if ($this->classObject->methodExists($constructorName)) {
            return $this->classObject->getMethod($constructorName);
        }

        $constructorMethod = clone $this->templateClassObject->getMethod('__construct')->setName($constructorName);

        return $constructorMethod;
    }

    protected function buildInitializeActionMethod(DomainObject $domainObject): Method
    {
        $initializeActionName = 'initializeAction';
        if ($this->classObject->methodExists($initializeActionName)) {
            return $this->classObject->getMethod($initializeActionName);
        }

        $initializeActionMethod = clone $this->templateClassObject->getMethod('initializeAction')->setName($initializeActionName);

        return $initializeActionMethod;
    }

    protected function buildInjectMethod(DomainObject $domainObject): Method
    {
        $repositoryName = $domainObject->getName() . 'Repository';
        $injectMethodName = 'inject' . $repositoryName;
        if ($this->classObject->methodExists($injectMethodName)) {
            return $this->classObject->getMethod($injectMethodName);
        }

        $injectMethod = clone $this->templateClassObject->getMethod('injectDomainObjectRepository')->setName($injectMethodName);
        $replacements = [
            preg_quote('\\VENDOR\\Package\\Domain\\Repository\\DomainObjectRepository', '/') => $domainObject->getFullyQualifiedDomainRepositoryClassName(),
            'domainObjectRepository' => lcfirst($repositoryName)
        ];
        $this->updateMethodBody($injectMethod, $replacements);
        $injectMethod->getParameterByPosition(0)
            ->setName(lcfirst($repositoryName))
            ->setVarType($domainObject->getFullyQualifiedDomainRepositoryClassName())
            ->setTypeHint($domainObject->getFullyQualifiedDomainRepositoryClassName());
        $injectMethod->removeTag('param');
        $injectMethod->updateParamTags();
        $this->updateDocComment($injectMethod, $replacements);
        return $injectMethod;
    }

    /**
     * This method generates the repository class object,
     * which is passed to the template
     * it keeps all methods and properties including
     * user modified method bodies and comments
     * needed to create a repository class file
     *
     * @param DomainObject $domainObject
     * @param string $repositoryTemplateClassPath
     * @param null $existingClassFileObject
     *
     * @return File
     * @throws FileNotFoundException
     */
    public function generateRepositoryClassFileObject(
        DomainObject $domainObject,
        string $repositoryTemplateClassPath,
        $existingClassFileObject = null
    ): File {
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

        if ($this->classObject === null) {
            $this->classFileObject = clone $this->templateFileObject;
            $this->classObject = clone $this->templateClassObject;
            $this->classObject->resetAll();
            $this->classObject->setName($className);
            $this->classObject->setDescription('The repository for ' . Inflector::pluralize($domainObject->getName()));
            $parentClass = $this->settings['Repository']['parentClass'] ?? '\\TYPO3\\CMS\\Extbase\\Persistence\\Repository';
            $this->classObject->setParentClassName($parentClass);
        }
        if ($domainObject->getSorting() && null === $this->classObject->getProperty('defaultOrderings')) {
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
     * @param DomainObject $domainObject
     */
    public function sortMethods($domainObject): void
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
