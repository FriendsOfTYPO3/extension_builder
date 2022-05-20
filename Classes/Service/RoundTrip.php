<?php

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
use EBT\ExtensionBuilder\Domain\Exception\ExtensionException;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter;
use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation;
use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Domain\Model\File;
use EBT\ExtensionBuilder\Utility\Inflector;
use Exception;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;

/**
 * Performs all changes that are required to adapt the
 * existing classes and methods to the changes in the configurations
 */
class RoundTrip implements SingletonInterface
{
    /**
     * @var string
     */
    const SPLIT_TOKEN = '## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder';

    const OVERWRITE_SETTINGS_SKIP = -1;
    const OVERWRITE_SETTINGS_MERGE = 1;
    const OVERWRITE_SETTINGS_KEEP = 2;

    protected ParserService $parserService;
    protected ExtensionBuilderConfigurationManager $configurationManager;
    protected ?Extension $previousExtension = null;
    protected ?Extension $extension = null;
    /**
     * if an extension was renamed this property keeps the
     * original extensionDirectory
     * otherwise it is set to the current extensionDir
     */
    protected string $previousExtensionDirectory = '';
    /**
     * the directory of the current extension
     */
    protected string $extensionDirectory = '';
    /**
     * if an extension was renamed this property keeps the old key
     * otherwise it is set to the current extensionKey
     */
    protected string $previousExtensionKey = '';
    /**
     * @var DomainObject[]
     */
    protected array $previousDomainObjects = [];
    /**
     * @var DomainObject[]
     */
    protected array $renamedDomainObjects = [];
    /**
     * was the extension renamed?
     */
    protected bool $extensionRenamed = false;
    protected ?ClassObject $classObject = null;
    /**
     * The file object parsed from existing files
     */
    protected ?File $classFileObject = null;
    protected array $settings = [];

    public function injectParserService(ParserService $parserService): void
    {
        $this->parserService = $parserService;
    }

    public function injectExtensionBuilderConfigurationManager(
        ExtensionBuilderConfigurationManager $configurationManager
    ): void {
        $this->configurationManager = $configurationManager;
    }

    /**
     * If a JSON file is found in the extensions directory the previous version
     * of the extension is build to compare it with the new configuration coming
     * from the extension builder input
     *
     * @param Extension $extension
     *
     * @throws ExtensionException
     * @throws Exception
     * @throws InvalidConfigurationTypeException
     */
    public function initialize(Extension $extension): void
    {
        $this->extension = $extension;
        $this->extensionDirectory = $this->extension->getExtensionDir();

        if (!$this->parserService instanceof ParserService) {
            $this->parserService = GeneralUtility::makeInstance(ParserService::class);
        }
        $this->settings = $this->configurationManager->getExtensionBuilderSettings();
        // defaults
        $this->previousExtensionDirectory = $this->extensionDirectory;
        $this->previousExtensionKey = $this->extension->getExtensionKey();

        if ($extension->isRenamed()) {
            $this->previousExtensionDirectory = $extension->getPreviousExtensionDirectory();
            $this->previousExtensionKey = $extension->getOriginalExtensionKey();
            $this->extensionRenamed = true;
        }

        // Rename the old kickstarter.json file to ExtensionBuilder.json
        if (file_exists($this->previousExtensionDirectory . 'kickstarter.json')) {
            rename(
                $this->previousExtensionDirectory . 'kickstarter.json',
                $this->previousExtensionDirectory . ExtensionBuilderConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE
            );
        }

        if (file_exists($this->previousExtensionDirectory . ExtensionBuilderConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE)) {
            $extensionSchemaBuilder = GeneralUtility::makeInstance(ExtensionSchemaBuilder::class);
            $jsonConfig = $this->configurationManager->getExtensionBuilderConfiguration($this->previousExtensionKey, $extension->getStoragePath());
            $this->previousExtension = $extensionSchemaBuilder->build($jsonConfig);
            $previousDomainObjects = $this->previousExtension->getDomainObjects();
            /** @var DomainObject[] $previousDomainObjects */
            foreach ($previousDomainObjects as $oldDomainObject) {
                $this->previousDomainObjects[$oldDomainObject->getUniqueIdentifier()] = $oldDomainObject;
                self::log(
                    'Old domain object: ' . $oldDomainObject->getName() . ' - ' . $oldDomainObject->getUniqueIdentifier(),
                    0,
                    $jsonConfig
                );
            }

            /**
             * now we store all renamed domainObjects in an array to enable
             * detection of renaming in relationProperties (property->getForeignModel)
             * we also build an array with the new unique identifiers to detect
             * deleting of domainObjects
             */
            $currentDomainsObjects = [];
            foreach ($this->extension->getDomainObjects() as $domainObject) {
                /** @var DomainObject $domainObject */
                if (isset($this->previousDomainObjects[$domainObject->getUniqueIdentifier()])) {
                    if ($this->previousDomainObjects[$domainObject->getUniqueIdentifier()]->getName() != $domainObject->getName()) {
                        $renamedDomainObjects[$domainObject->getUniqueIdentifier()] = $domainObject;
                    }
                }
                $currentDomainsObjects[$domainObject->getUniqueIdentifier()] = $domainObject;
            }
            // remove deleted objects
            foreach ($previousDomainObjects as $oldDomainObject) {
                if (!isset($currentDomainsObjects[$oldDomainObject->getUniqueIdentifier()])) {
                    $this->removeDomainObjectFiles($oldDomainObject);
                }
            }
        }
    }

    /**
     * This method is the main part of the roundtrip functionality
     * It looks for a previous version of the current domain object and
     * parses the existing class file for that domain model
     * compares all properties and methods with the previous version.
     *
     * Methods are either removed/added or updated according to
     * the new property names
     *
     * @param DomainObject $currentDomainObject
     *
     * @return File|null
     * @throws Exception
     */
    public function getDomainModelClassFile(DomainObject $currentDomainObject): ?File
    {
        if (isset($this->previousDomainObjects[$currentDomainObject->getUniqueIdentifier()])) {
            self::log('domainObject identified:' . $currentDomainObject->getName());
            $oldDomainObject = $this->previousDomainObjects[$currentDomainObject->getUniqueIdentifier()];
            /** @var DomainObject $oldDomainObject */
            $extensionDir = $this->previousExtensionDirectory;
            $fileName = FileGenerator::getFolderForClassFile(
                $extensionDir,
                'Model',
                false
            ) . $oldDomainObject->getName() . '.php';
            if (file_exists($fileName)) {
                // import the classObject from the existing file
                $this->classFileObject = $this->parserService->parseFile($fileName);
                $this->classObject = $this->classFileObject->getFirstClass();
                if ($oldDomainObject->getName() != $currentDomainObject->getName() || $this->extensionRenamed) {
                    if (!$this->extensionRenamed) {
                        self::log(
                            'domainObject renamed. old: ' . $oldDomainObject->getName() . ' new: '
                            . $currentDomainObject->getName(),
                            'extension_builder'
                        );
                    }
                    $newClassName = $currentDomainObject->getName();
                    $this->classObject->setName($newClassName);
                    $this->classObject->setFileName($currentDomainObject->getName() . '.php');
                    $this->cleanUp(
                        FileGenerator::getFolderForClassFile($extensionDir, 'Model'),
                        $oldDomainObject->getName() . '.php'
                    );
                    $this->cleanUp($extensionDir . 'Configuration/TCA/', $oldDomainObject->getName() . '.php');
                } else {
                    $this->classObject->setName($currentDomainObject->getName());
                }

                $this->updateModelClassProperties($oldDomainObject, $currentDomainObject);

                $newActions = [];
                foreach ($currentDomainObject->getActions() as $newAction) {
                    $newActions[$newAction->getName()] = $newAction;
                }
                $oldActions = $oldDomainObject->getActions();

                if ((empty($newActions) && !$currentDomainObject->isAggregateRoot())
                    && (!empty($oldActions) || $oldDomainObject->isAggregateRoot())
                ) {
                    // remove the controller
                    $this->cleanUp(
                        FileGenerator::getFolderForClassFile($extensionDir, 'Controller'),
                        $oldDomainObject->getName() . 'Controller.php'
                    );
                }

                // the parent class settings configuration
                $parentClass = $currentDomainObject->getParentClass();
                $oldParentClass = $oldDomainObject->getParentClass();
                if (!empty($parentClass)) {
                    if ($oldParentClass != $parentClass) {
                        // the parent class was just new added
                        $this->classObject->setParentClassName($parentClass);
                    }
                } elseif (!empty($oldParentClass)) {
                    // the old object had a parent class setting, but it's removed now
                    if ($currentDomainObject->isEntity()) {
                        $parentClass = $this->configurationManager->getParentClassForEntityObject($this->extension);
                    } else {
                        $parentClass = $this->configurationManager->getParentClassForValueObject($this->extension);
                    }
                    $this->classObject->setParentClassName($parentClass);
                }

                if ($currentDomainObject->isEntity() && !$oldDomainObject->isEntity()) {
                    // the object type was changed in the modeler
                    $this->classObject->setParentClassName(
                        $this->configurationManager->getParentClassForEntityObject($this->extension)
                    );
                } elseif (!$currentDomainObject->isEntity() && $oldDomainObject->isEntity()) {
                    // the object type was changed in the modeler
                    $this->classObject->setParentClassName(
                        $this->configurationManager->getParentClassForValueObject($this->extension)
                    );
                }
                $this->classFileObject->setClasses([$this->classObject]);
                if ($this->extension->vendorNameChanged()) {
                    $this->updateVendorName();
                }
                return $this->classFileObject;
            }
        } else {
            self::log(
                'domainObject not identified:' . $currentDomainObject->getName(),
                0,
                $this->previousDomainObjects
            );
            $fileName = FileGenerator::getFolderForClassFile($this->extensionDirectory, 'Model', false);
            $fileName .= $currentDomainObject->getName() . '.php';
            if (file_exists($fileName)) {
                // import the classObject from the existing file
                $this->classFileObject = $this->parserService->parseFile($fileName);
                $this->classObject = $this->classFileObject->getFirstClass();
                $this->classObject->setFileName($fileName);
                $this->classObject->setName($currentDomainObject->getName());
                self::log(
                    'class file found:' . $currentDomainObject->getName() . '.php',
                    0,
                    $this->classObject->getNamespaceName()
                );
                $this->classFileObject->setClasses([$this->classObject]);
                return $this->classFileObject;
            }
        }
        return null;
    }

    /**
     * @param DomainObject $currentDomainObject
     *
     * @return File|null
     * @throws Exception
     */
    public function getControllerClassFile(DomainObject $currentDomainObject): ?File
    {
        $extensionDir = $this->previousExtensionDirectory;
        if (isset($this->previousDomainObjects[$currentDomainObject->getUniqueIdentifier()])) {
            $oldDomainObject = $this->previousDomainObjects[$currentDomainObject->getUniqueIdentifier()];
            $fileName = FileGenerator::getFolderForClassFile($extensionDir, 'Controller', false);
            $fileName .= $oldDomainObject->getName() . 'Controller.php';
            if (file_exists($fileName)) {
                $this->classFileObject = $this->parserService->parseFile($fileName);
                $this->classObject = $this->classFileObject->getFirstClass();
                $this->classObject->setName($currentDomainObject->getName() . 'Controller');
                if ($oldDomainObject->getName() != $currentDomainObject->getName() || $this->extensionRenamed) {
                    $this->mapOldControllerToCurrentClassObject($oldDomainObject, $currentDomainObject);
                } elseif ($oldDomainObject->isAggregateRoot() && !$currentDomainObject->isAggregateRoot()) {
                    $injectMethodName = 'inject' . lcfirst($oldDomainObject->getName()) . 'Repository';
                    $this->classObject->removeMethod($injectMethodName);
                }

                $newActions = [];
                foreach ($currentDomainObject->getActions() as $newAction) {
                    $newActions[$newAction->getName()] = $newAction;
                }
                $oldActions = $oldDomainObject->getActions();
                if (isset($this->previousDomainObjects[$currentDomainObject->getUniqueIdentifier()])) {
                    // now we remove old action methods
                    foreach ($oldActions as $oldAction) {
                        if (!isset($newActions[$oldAction->getName()])) {
                            // an action was removed
                            $this->classObject->removeMethod($oldAction->getName() . 'Action');
                            self::log(
                                'Action method removed:' . $oldAction->getName(),
                                0,
                                $this->classObject->getMethods()
                            );
                        }
                    }
                    // we don't have to add new ones, this will be done automatically by the class builder
                }
                if ($this->extension->vendorNameChanged()) {
                    $this->updateVendorName();
                }
                $this->classFileObject->setClasses([$this->classObject]);

                return $this->classFileObject;
            }

            return null;
        }

        $fileName = FileGenerator::getFolderForClassFile($extensionDir, 'Controller', false);
        $fileName .= $currentDomainObject->getName() . 'Controller.php';
        if (file_exists($fileName)) {
            $this->classFileObject = $this->parserService->parseFile($fileName);
            $this->classObject = $this->classFileObject->getFirstClass();
            $this->classObject->setFileName($fileName);
            $className = $currentDomainObject->getControllerClassName();
            $this->classObject->setName($className);
            if ($this->extension->vendorNameChanged()) {
                $this->updateVendorName();
            }
            $this->classFileObject->setClasses([$this->classObject]);

            return $this->classFileObject;
        }

        self::log('No existing controller class:' . $currentDomainObject->getName(), 2);
        return null;
    }

    /**
     * update all relevant namespace parts in tags, typehints etc.
     */
    protected function updateVendorName(): void
    {
        $this->classObject->setNamespaceName($this->renameVendor($this->classObject->getNamespaceName()));
        foreach ($this->classObject->getProperties() as $property) {
            foreach ($property->getTags() as $tagName => $tagValue) {
                if (is_array($tagValue)) {
                    $tagValue = $tagValue[0];
                }
                if (!empty($tagValue)) {
                    $tagValue = $this->renameVendor($tagValue);
                    $property->setTag($tagName, $tagValue, true);
                }
            }
        }
        foreach ($this->classObject->getMethods() as $method) {
            foreach ($method->getTags() as $tagName => $tagValue) {
                if (is_array($tagValue)) {
                    $tagValue = $tagValue[0];
                }
                if (!empty($tagValue)) {
                    $tagValue = $this->renameVendor($tagValue);
                    $method->setTag($tagName, $tagValue, true);
                }
            }
            foreach ($method->getParameters() as $parameter) {
                $typeHint = $parameter->getTypeHint();
                if (!empty($typeHint)) {
                    $parameter->setTypeHint($this->renameVendor($typeHint));
                }
                $varType = $parameter->getVarType();
                if (!empty($varType)) {
                    $parameter->setVarType($this->renameVendor($varType));
                }
            }
        }
    }

    protected function renameVendor($string)
    {
        return str_replace(
            '\\' . $this->extension->getOriginalVendorName() . '\\',
            '\\' . $this->extension->getVendorName() . '\\',
            $string
        );
    }

    /**
     * If a domainObject was renamed
     *
     * @param DomainObject $oldDomainObject
     * @param DomainObject $currentDomainObject
     *
     * @throws Exception
     */
    protected function mapOldControllerToCurrentClassObject(
        DomainObject $oldDomainObject,
        DomainObject $currentDomainObject
    ): void {
        $extensionDir = $this->previousExtensionDirectory;
        $newClassName = $currentDomainObject->getName() . 'Controller';
        $newName = $currentDomainObject->getName();
        $oldName = $oldDomainObject->getName();
        $this->classObject->setName($newClassName);
        $this->classObject->setDescription($this->replaceUpperAndLowerCase(
            $oldName,
            $newName,
            $this->classObject->getDescription()
        ));
        if ($oldDomainObject->isAggregateRoot()) {
            // should we keep the old properties comments and tags?
            $this->classObject->removeProperty(lcfirst($oldName) . 'Repository');
            $injectMethodName = 'inject' . $oldName . 'Repository';
            if ($currentDomainObject->isAggregateRoot()) {
                // update the initializeAction method body
                $initializeMethod = $this->classObject->getMethod('initializeAction');
                if ($initializeMethod != null) {
                    $initializeMethodBodyStmts = $initializeMethod->getBodyStmts();
                    $initializeMethodBodyStmts = str_replace(
                        lcfirst($oldName) . 'Repository',
                        lcfirst($newName) . 'Repository',
                        $initializeMethodBodyStmts
                    );
                    $initializeMethod->setBodyStmts($initializeMethodBodyStmts);
                    $this->classObject->setMethod($initializeMethod);
                }

                $injectMethod = $this->classObject->getMethod($injectMethodName);
                if ($injectMethod != null) {
                    $this->classObject->removeMethod($injectMethodName);
                    $initializeMethodBodyStmts = str_replace(
                        lcfirst($oldName),
                        lcfirst($newName),
                        $injectMethod->getBodyStmts()
                    );
                    $injectMethod->setBodyStmts($initializeMethodBodyStmts);
                    $injectMethod->setTag(
                        'param',
                        $currentDomainObject->getFullyQualifiedDomainRepositoryClassName() . ' $' . $newName . 'Repository'
                    );
                    $injectMethod->setName('inject' . $newName . 'Repository');
                    $parameter = new MethodParameter(lcfirst($newName) . 'Repository');
                    $parameter->setTypeHint($currentDomainObject->getFullyQualifiedDomainRepositoryClassName());
                    $parameter->setPosition(0);
                    $injectMethod->replaceParameter($parameter);
                    $this->classObject->setMethod($injectMethod);
                }

                foreach ($oldDomainObject->getActions() as $action) {
                    // here we have to update all the occurences of domain object names in action methods
                    $actionMethod = $this->classObject->getMethod($action->getName() . 'Action');
                    if ($actionMethod != null) {
                        $actionMethodBody = $actionMethod->getBodyStmts();
                        $newActionMethodBody = str_replace(
                            lcfirst($oldName) . 'Repository',
                            lcfirst($newName) . 'Repository',
                            $actionMethodBody
                        );
                        $actionMethod->setBodyStmts($newActionMethodBody);
                        $actionMethod->setTag('param', $currentDomainObject->getQualifiedClassName());

                        $parameters = $actionMethod->getParameters();
                        foreach ($parameters as &$parameter) {
                            if (strpos($parameter->getTypeHint(), $oldDomainObject->getFullQualifiedClassName()) > -1) {
                                $parameter->setTypeHint($currentDomainObject->getFullQualifiedClassName());
                                $parameter->setName(
                                    $this->replaceUpperAndLowerCase(
                                        $oldName,
                                        $newName,
                                        $parameter->getName()
                                    )
                                );
                                $actionMethod->replaceParameter($parameter);
                            }
                        }

                        $tags = $actionMethod->getTags();
                        foreach ($tags as $tagName => $tagValue) {
                            $tags[$tagName] = $this->replaceUpperAndLowerCase($oldName, $newName, $tagValue);
                        }
                        $actionMethod->setTags($tags);

                        $actionMethod->setDescription(
                            $this->replaceUpperAndLowerCase(
                                $oldName,
                                $newName,
                                $actionMethod->getDescription()
                            )
                        );

                        //TODO: this is not safe. Could rename unwanted variables
                        $actionMethod->setBodyStmts(
                            $this->replaceUpperAndLowerCase(
                                $oldName,
                                $newName,
                                $actionMethod->getBodyStmts()
                            )
                        );
                        $this->classObject->setMethod($actionMethod);
                    }
                }
            } else {
                $this->classObject->removeMethod('initializeAction');
                $this->classObject->removeMethod($injectMethodName);
                $this->cleanUp(
                    FileGenerator::getFolderForClassFile($extensionDir, 'Repository'),
                    $oldName . 'Repository.php'
                );
            }
        }

        $this->classObject->setFileName($newName . 'Controller.php');
        $this->cleanUp(FileGenerator::getFolderForClassFile($extensionDir, 'Controller'), $oldName . 'Controller.php');
    }

    /**
     * @param DomainObject $currentDomainObject
     *
     * @return File|null
     * @throws Exception
     */
    public function getRepositoryClassFile(DomainObject $currentDomainObject): ?File
    {
        $extensionDir = $this->previousExtensionDirectory;
        if (isset($this->previousDomainObjects[$currentDomainObject->getUniqueIdentifier()])) {
            $oldDomainObject = $this->previousDomainObjects[$currentDomainObject->getUniqueIdentifier()];
            $fileName = FileGenerator::getFolderForClassFile($extensionDir, 'Repository', false);
            $fileName .= $oldDomainObject->getName() . 'Repository.php';
            if (file_exists($fileName)) {
                $this->classFileObject = $this->parserService->parseFile($fileName);
                $this->classObject = $this->classFileObject->getFirstClass();
                $this->classObject->setName($currentDomainObject->getName() . 'Repository');
                if ($oldDomainObject->getName() != $currentDomainObject->getName() || $this->extensionRenamed) {
                    $newClassName = $currentDomainObject->getDomainRepositoryClassName();
                    $this->classObject->setName($newClassName);
                    $this->cleanUp(
                        FileGenerator::getFolderForClassFile($extensionDir, 'Repository'),
                        $oldDomainObject->getName() . 'Repository.php'
                    );
                }
                return $this->classFileObject;
            }
        } else {
            $fileName = FileGenerator::getFolderForClassFile($extensionDir, 'Repository', false);
            $fileName .= $currentDomainObject->getName() . 'Repository.php';
            if (file_exists($fileName)) {
                $this->classFileObject = $this->parserService->parseFile($fileName);
                $this->classObject = $this->classFileObject->getFirstClass();
                $this->classObject->setFileName($fileName);
                $this->classObject->setFileName($fileName);
                self::log('existing Repository class:' . $fileName, 0, (array)$this->classObject);
                return $this->classFileObject;
            }
        }
        self::log('No existing Repository class:' . $currentDomainObject->getName(), 2);
        return null;
    }

    /**
     * Compare the properties of each object and remove/update
     * the properties and the related methods
     *
     * @param DomainObject $oldDomainObject
     * @param DomainObject $newDomainObject
     *
     * return void (all actions are performed on $this->classObject
     */
    protected function updateModelClassProperties(
        DomainObject $oldDomainObject,
        DomainObject $newDomainObject
    ): void {
        $newProperties = [];
        foreach ($newDomainObject->getProperties() as $property) {
            $newProperties[$property->getUniqueIdentifier()] = $property;
        }

        // compare all old properties with new ones
        foreach ($oldDomainObject->getProperties() as $oldProperty) {
            /* @var AbstractProperty $oldProperty
             * @var AbstractProperty $newProperty
             */
            if (isset($newProperties[$oldProperty->getUniqueIdentifier()])) {
                $newProperty = $newProperties[$oldProperty->getUniqueIdentifier()];

                // relation type changed
                if ($oldProperty->isAnyToManyRelation() != $newProperty->isAnyToManyRelation()) {
                    // remove old methods since we won't convert getter and setter methods
                    //to add/remove methods
                    if ($oldProperty->isAnyToManyRelation()) {
                        $this->classObject->removeMethod('add' . ucfirst(Inflector::singularize($oldProperty->getName())));
                        $this->classObject->removeMethod('remove' . ucfirst(Inflector::singularize($oldProperty->getName())));
                    }
                    $this->classObject->removeMethod('get' . ucfirst($oldProperty->getName()));
                    $this->classObject->removeMethod('set' . ucfirst($oldProperty->getName()));
                    if ($oldProperty->isBoolean()) {
                        $this->classObject->removeMethod('is' . ucfirst(Inflector::singularize($oldProperty->getName())));
                    }
                    $this->classObject->removeProperty($oldProperty->getName());
                    self::log(
                        'property type changed => removed old property:' . $oldProperty->getName(),
                        1
                    );
                } else {
                    $this->updateProperty($oldProperty, $newProperty);
                    $newDomainObject->getPropertyByName($newProperty->getName())->setNew(false);
                }
            } else {
                $this->removePropertyAndRelatedMethods($oldProperty);
            }
        }
    }

    /**
     * Removes all related methods, if a property was removed
     * @param AbstractProperty $propertyToRemove
     */
    protected function removePropertyAndRelatedMethods(AbstractProperty $propertyToRemove): void
    {
        $propertyName = $propertyToRemove->getName();
        $this->classObject->removeProperty($propertyName);
        if ($propertyToRemove->isAnyToManyRelation()) {
            $this->classObject->removeMethod('add' . ucfirst(Inflector::singularize($propertyName)));
            $this->classObject->removeMethod('remove' . ucfirst(Inflector::singularize($propertyName)));
        }
        $this->classObject->removeMethod('get' . ucfirst($propertyName));
        $this->classObject->removeMethod('set' . ucfirst($propertyName));
        if ($propertyToRemove->isBoolean()) {
            $this->classObject->removeMethod('is' . ucfirst($propertyName));
        }
    }

    /**
     * Rename a property and update comment (var tag and description)
     * @param AbstractProperty $oldProperty
     * @param AbstractProperty $newProperty
     */
    protected function updateProperty(AbstractProperty $oldProperty, AbstractProperty $newProperty): void
    {
        $classProperty = $this->classObject->getProperty($oldProperty->getName());
        if ($classProperty) {
            $classProperty->setName($newProperty->getName());
            $classProperty->setTag('var', $newProperty->getTypeForComment());
            $newDescription = $newProperty->getDescription();
            if (empty($newDescription) || $newDescription == $newProperty->getName()) {
                $newDescription = str_replace(
                    $oldProperty->getName(),
                    $newProperty->getName(),
                    $classProperty->getDescription()
                );
            }
            $classProperty->setDescription($newDescription);
            $this->classObject->removeProperty($oldProperty->getName());
            $this->classObject->setProperty($classProperty);
            if ($this->relatedMethodsNeedUpdate($oldProperty, $newProperty)) {
                $this->updatePropertyRelatedMethods($oldProperty, $newProperty);
            }
        }
    }

    protected function relatedMethodsNeedUpdate(AbstractProperty $oldProperty, AbstractProperty $newProperty): bool
    {
        if ($this->extensionRenamed) {
            return true;
        }
        if ($newProperty->getName() != $oldProperty->getName()) {
            self::log('property renamed:' . $oldProperty->getName() . ' ' . $newProperty->getName());
            return true;
        }
        if ($newProperty->getTypeForComment() != $this->updateExtensionKey($oldProperty->getTypeForComment())) {
            self::log(
                'property type changed from ' . $this->updateExtensionKey($oldProperty->getTypeForComment())
                . ' to ' . $newProperty->getTypeForComment()
            );
            return true;
        }
        if ($newProperty->isRelation()) {
            /** @var AbstractRelation $oldProperty */
            // if only the related domain object was renamed
            $previousClassName = $this->updateExtensionKey($oldProperty->getForeignClassName());
            if ($this->getForeignClassName($newProperty) != $previousClassName) {
                self::log(
                    'related domainObject was renamed:' . $previousClassName . ' ->' . $this->getForeignClassName($newProperty)
                );
                return true;
            }
        }
        return false;
    }

    /**
     * replace occurrences of the old extension key with the new one
     * used to compare classNames
     * @param string $stringToParse
     * @return string
     */
    protected function updateExtensionKey(string $stringToParse): string
    {
        if (!$this->extensionRenamed) {
            return $stringToParse;
        }
        $separatorToken = '\\\\';
        if (strpos($stringToParse, $separatorToken) === false) {
            $separatorToken = '_';
        }
        return str_replace(
            $separatorToken . ucfirst($this->previousExtensionKey) . $separatorToken,
            $separatorToken . ucfirst($this->extension->getExtensionKey()) . $separatorToken,
            $stringToParse
        );
    }

    /**
     * @param AbstractProperty $oldProperty
     * @param AbstractProperty $newProperty
     */
    protected function updatePropertyRelatedMethods(AbstractProperty $oldProperty, AbstractProperty $newProperty): void
    {
        if ($newProperty->isAnyToManyRelation()) {
            $this->updateMethod($oldProperty, $newProperty, 'add');
            $this->updateMethod($oldProperty, $newProperty, 'remove');
        }
        $this->updateMethod($oldProperty, $newProperty, 'get');
        $this->updateMethod($oldProperty, $newProperty, 'set');
        if ($newProperty->isBoolean()) {
            $this->updateMethod($oldProperty, $newProperty, 'is');
        }
        if ($newProperty->getTypeForComment() != $this->updateExtensionKey($oldProperty->getTypeForComment())) {
            if ($oldProperty->isBoolean() && !$newProperty->isBoolean()) {
                $this->classObject->removeMethod(ClassBuilder::getMethodName($oldProperty, 'is'));
                self::log(
                    'Method removed:' . ClassBuilder::getMethodName($oldProperty, 'is'),
                    1,
                    $this->classObject->getMethods()
                );
            }
        }
    }

    /**
     * update means renaming of method name, parameter and replacing
     * parameter names in method body
     *
     * @param AbstractProperty $oldProperty
     * @param AbstractProperty $newProperty
     * @param string $methodType get,set,add,remove,is
     */
    protected function updateMethod(AbstractProperty $oldProperty, AbstractProperty $newProperty, string $methodType): void
    {
        $oldMethodName = ClassBuilder::getMethodName($oldProperty, $methodType);
        // the method to be merged
        $mergedMethod = $this->classObject->getMethod($oldMethodName);

        if (!$mergedMethod) {
            // no previous version of the method exists
            return;
        }
        $newMethodName = ClassBuilder::getMethodName($newProperty, $methodType);
        self::log('updateMethod:' . $oldMethodName . '=>' . $newMethodName, 'extension_builder');

        if ($oldProperty->getName() != $newProperty->getName()) {
            // rename the method
            $mergedMethod->setName($newMethodName);

            $oldMethodBody = $mergedMethod->getBodyStmts();

            $newMethodBody = $this->replacePropertyNameInMethodBody(
                $oldProperty->getName(),
                $newProperty->getName(),
                $oldMethodBody
            );
            $mergedMethod->setBodyStmts($newMethodBody);
        }

        // update the method parameters
        $methodParameters = $mergedMethod->getParameters();

        if (!empty($methodParameters)) {
            $parameterTags = $mergedMethod->getTagValues('param');
            if (!is_array($parameterTags)) {
                $parameterTags = [$parameterTags];
            }

            foreach ($methodParameters as $methodParameter) {
                $oldParameterName = $methodParameter->getName();
                if ($oldParameterName == ClassBuilder::getParameterName($oldProperty, $methodType)) {
                    $newParameterName = ClassBuilder::getParameterName($newProperty, $methodType);
                    $methodParameter->setName($newParameterName);
                    $newMethodBody = $this->replacePropertyNameInMethodBody(
                        $oldParameterName,
                        $newParameterName,
                        $mergedMethod->getBodyStmts()
                    );
                    $mergedMethod->setBodyStmts($newMethodBody);
                }
                $typeHint = $methodParameter->getTypeHint();
                if ($typeHint) {
                    if ($oldProperty->isRelation()) {
                        /** @var AbstractRelation $oldProperty */
                        if ($typeHint == $oldProperty->getForeignClassName()) {
                            $methodParameter->setTypeHint($this->updateExtensionKey($this->getForeignClassName($newProperty)));
                        }
                    }
                }
                $parameterTags[$methodParameter->getPosition()] = ClassBuilder::getParamTag($newProperty, $methodType);
                $mergedMethod->replaceParameter($methodParameter);
            }
            $mergedMethod->setTag('param', $parameterTags);
        }

        if ($mergedMethod->isTaggedWith('return') && $mergedMethod->getTagValue('return') != 'void') {
            $mergedMethod->setTag('return', $newProperty->getTypeForComment() . ' ' . $newProperty->getName());
        }

        // replace property names in description
        $mergedMethod->setDescription(str_replace(
            $oldProperty->getName(),
            $newProperty->getName(),
            $mergedMethod->getDescription()
        ));
        if ($oldProperty instanceof AbstractRelation && $newProperty instanceof AbstractRelation) {
            $mergedMethod->setDescription(
                str_replace(
                    $oldProperty->getForeignClassName(),
                    $newProperty->getForeignClassName(),
                    $mergedMethod->getDescription()
                )
            );
        }
        $this->classObject->removeMethod($oldMethodName);
        $this->classObject->addMethod($mergedMethod);
    }

    /**
     * @param string $search
     * @param string $replace
     * @param string $haystack
     *
     * @return string with replaced values
     */
    protected function replaceUpperAndLowerCase(string $search, string $replace, $haystack): string
    {
        $result = str_replace(ucfirst($search), ucfirst($replace), $haystack);
        return str_replace(lcfirst($search), lcfirst($replace), $result);
    }

    /**
     * Replace all occurrences of the old property name with the new name
     *
     * @param string $oldName
     * @param string $newName
     * @param string[] $methodBodyStmts
     * @return array
     */
    protected function replacePropertyNameInMethodBody(string $oldName, string $newName, array $methodBodyStmts): array
    {
        return $this->parserService->replaceNodeProperty(
            $methodBodyStmts,
            [$oldName => $newName]
        );
    }

    /**
     * if the foreign DomainObject was renamed, the relation has to be updated also
     *
     * @param AbstractRelation $relation
     * @return string|null className of foreign class
     */
    public function getForeignClassName(AbstractRelation $relation): ?string
    {
        if ($relation->getForeignModel()
            && isset($this->renamedDomainObjects[$relation->getForeignModel()->getUniqueIdentifier()])
        ) {
            $renamedObject = $this->renamedDomainObjects[$relation->getForeignModel()->getUniqueIdentifier()];
            return $renamedObject->getQualifiedClassName();
        }
        return $relation->getForeignClassName();
    }

    /**
     * remove domainObject related files if a domainObject was deleted
     *
     * @param DomainObject $domainObject
     *
     * @throws Exception
     */
    protected function removeDomainObjectFiles(DomainObject $domainObject): void
    {
        self::log('Remove domainObject ' . $domainObject->getName());
        $this->cleanUp(
            FileGenerator::getFolderForClassFile($this->previousExtensionDirectory, 'Model', false),
            $domainObject->getName() . '.php'
        );
        $this->cleanUp($this->previousExtensionDirectory . 'Configuration/TCA/', $domainObject->getName() . '.php');
        if ($domainObject->isAggregateRoot()) {
            $this->cleanUp(
                FileGenerator::getFolderForClassFile($this->previousExtensionDirectory, 'Controller', false),
                $domainObject->getName() . 'Controller.php'
            );
            $this->cleanUp(
                FileGenerator::getFolderForClassFile($this->previousExtensionDirectory, 'Repository', false),
                $domainObject->getName() . 'Repository.php'
            );
        }
        if (count($domainObject->getActions()) > 0) {
            $this->cleanUp(
                FileGenerator::getFolderForClassFile($this->previousExtensionDirectory, 'Controller', false),
                $domainObject->getName() . 'Controller.php'
            );
        }
        // other files
        $iconsDirectory = $this->extensionDirectory . 'Resources/Public/Icons/';
        $languageDirectory = $this->extensionDirectory . 'Resources/Private/Language/';
        $locallang_cshFile = $languageDirectory . 'locallang_csh_' . $domainObject->getDatabaseTableName() . '.xml';
        $iconFile = $iconsDirectory . $domainObject->getDatabaseTableName() . '.gif';
        if (file_exists($locallang_cshFile)) {
            // no overwrite settings check here...
            unlink($locallang_cshFile);
        }
        if (file_exists($iconFile)) {
            unlink($iconFile);
        }
    }

    /**
     * remove class files that are not required any more, due to
     * renaming of ModelObjects or changed types
     * @param string $path
     * @param string $fileName
     */
    public function cleanUp(string $path, string $fileName): void
    {
        if ($this->extensionRenamed) {
            // wo won't delete the old extension!
            return;
        }
        if (!is_file($path . $fileName)) {
            self::log('cleanUp File not found: ' . $path . $fileName, 'extension_builder', 1);
            return;
        }
        unlink($path . $fileName);
    }

    /**
     * @return array
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function getExtConfiguration(): array
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('extension_builder');
    }

    /**
     * finds a related overwrite setting to a path
     * and returns the overWrite setting
     * -1 for do not create at all
     * 0  for overwrite
     * 1  for merge (if possible)
     * 2  for keep existing file
     *
     * @param string $path of the file to get the settings for
     * @param Extension $extension
     *
     * @return int|null
     * @throws Exception
     */
    public static function getOverWriteSettingForPath(string $path, Extension $extension): ?int
    {
        $map = [
            'skip' => self::OVERWRITE_SETTINGS_SKIP,
            'merge' => self::OVERWRITE_SETTINGS_MERGE,
            'keep' => self::OVERWRITE_SETTINGS_KEEP,
        ];

        $settings = $extension->getSettings();
        if (!is_array($settings)) {
            throw new Exception('overWrite settings could not be parsed');
        }
        if (strpos($path, $extension->getExtensionDir()) === 0) {
            $path = str_replace($extension->getExtensionDir(), '', $path);
        }
        $pathParts = explode('/', $path);
        $overWriteSettings = $settings['overwriteSettings'] ?? [];

        foreach ($pathParts as $pathPart) {
            if (isset($overWriteSettings[$pathPart]) && is_array($overWriteSettings[$pathPart])) {
                // step one level deeper
                $overWriteSettings = $overWriteSettings[$pathPart];
            } else {
                return $map[$overWriteSettings[$pathPart] ?? ''] ?? null;
            }
        }

        return 0;
    }

    /**
     * parse existing tca and set appropriate properties
     *
     * @param Extension $extension
     *
     * @throws Exception
     */
    public static function prepareExtensionForRoundtrip(Extension $extension): void
    {
        foreach ($extension->getDomainObjects() as $domainObject) {
            $existingTca = self::getTcaForDomainObject($domainObject);
            if ($existingTca) {
                foreach ($domainObject->getAnyToManyRelationProperties() as $relationProperty) {
                    $relationTableName = GeneralUtility::camelCaseToLowerCaseUnderscored($relationProperty->getName());
                    if (isset($existingTca['columns'][$relationTableName]['config']['MM'])) {
                        self::log(
                            'Relation table for Model ' . $domainObject->getName() . ' relation ' . $relationProperty->getName(),
                            0,
                            $existingTca['columns'][$relationTableName]['config']
                        );
                        $relationProperty->setRelationTableName(
                            $existingTca['columns'][$relationTableName]['config']['MM']
                        );
                    }
                }
            }
            if (file_exists($extension->getExtensionDir() . 'Configuration/TCA/' . $domainObject->getName() . '.php')) {
                $extensionConfigurationJson = ExtensionBuilderConfigurationManager::getExtensionBuilderJson($extension->getExtensionKey());
                if ((float)($extensionConfigurationJson['log']['extension_builder_version']) <= 6.2) {
                    self::moveAdditionalTcaToOverrideFile($domainObject);
                }
            }
        }
    }

    /**
     * Returns the current TCA for a domain objects table if the
     * extension is installed
     * TODO: check for previous table name if an extension is renamed
     */
    protected static function getTcaForDomainObject(DomainObject $domainObject): ?array
    {
        $tableName = $domainObject->getDatabaseTableName();
        return $GLOBALS['TCA'][$tableName] ?? null;
    }

    /**
     * Move custom TCA in files generated by EB versions <= 6.2
     * to the appropriate overrides files
     *
     * @param DomainObject $domainObject
     *
     * @throws Exception
     */
    public static function moveAdditionalTcaToOverrideFile(DomainObject $domainObject): void
    {
        $tcaDir = $domainObject->getExtension()->getExtensionDir() . 'Configuration/TCA/';
        $existingTcaFile = $tcaDir . $domainObject->getName() . '.php';
        if (file_exists($existingTcaFile)) {
            $existingFileContent = file_get_contents($existingTcaFile);
            $fileParts = explode(self::SPLIT_TOKEN, $existingFileContent);
            if (count($fileParts) === 2) {
                $customFileContent = str_replace('$TCA[', '$GLOBALS[\'TCA\'][', $fileParts[1]);
                $customFileContent = '<?php ' . LF . self::SPLIT_TOKEN . LF . str_replace('?>', '', $customFileContent);
                if (!empty($customFileContent)) {
                    $overrideDir = $tcaDir . 'Overrides/';
                    if (!is_dir($overrideDir)) {
                        GeneralUtility::mkdir_deep($tcaDir . 'Overrides');
                    }
                    $success = GeneralUtility::writeFile(
                        $overrideDir . $domainObject->getDatabaseTableName() . '.php',
                        $customFileContent
                    );
                    if (!$success) {
                        throw new Exception('File ' . $overrideDir . $domainObject->getDatabaseTableName() . '.php could not be created!');
                    }

                    unlink($existingTcaFile);
                }
            }
        }
    }

    /**
     * @param Extension $extension
     * @param string|null $backupDir
     *
     * @throws Exception
     */
    public static function backupExtension(Extension $extension, ?string $backupDir): void
    {
        if (empty($backupDir)) {
            throw new Exception('Please define a backup directory in extension configuration!');
        }

        if (!GeneralUtility::validPathStr($backupDir)) {
            throw new Exception('Backup directory is not a valid path: ' . $backupDir);
        }

        if (PathUtility::isAbsolutePath($backupDir)) {
            if (!GeneralUtility::isAllowedAbsPath($backupDir)) {
                throw new Exception('Backup directory is not an allowed absolute path: ' . $backupDir);
            }
        } else {
            $backupDir = Environment::getProjectPath() . '/' . $backupDir;
        }
        // Add trailing slash
        if (strrpos($backupDir, '/') < strlen($backupDir) - 1) {
            $backupDir .= '/';
        }
        if (!is_dir($backupDir)) {
            GeneralUtility::mkdir_deep($backupDir);
        }

        if (!is_writable($backupDir)) {
            throw new Exception('Backup directory is not writable: ' . $backupDir);
        }

        $backupDir .= $extension->getExtensionKey();
        // create a subdirectory for this extension
        if (!is_dir($backupDir)) {
            GeneralUtility::mkdir($backupDir);
        }
        // Add trailing slash
        if (strrpos($backupDir, '/') < strlen($backupDir) - 1) {
            $backupDir .= '/';
        }
        $backupDir .= date('Y-m-d-') . time();
        if (!is_dir($backupDir)) {
            GeneralUtility::mkdir($backupDir);
        }
        // Remove trailing slash
        $extensionDir = substr($extension->getExtensionDir(), 0, -1);
        try {
            self::recurse_copy($extensionDir, $backupDir);
        } catch (Exception $e) {
            throw new Exception('Code generation aborted:' . $e->getMessage());
        }
        self::log('Backup created in ' . $backupDir);
    }

    protected static function log($message, $severity = 0, $data = []): void
    {
        // TODO implement logging
    }

    /**
     * @param string $src path to copy
     * @param string $dst destination
     *
     * @throws Exception
     */
    public static function recurse_copy(string $src, string $dst): void
    {
        $dir = opendir($src);
        GeneralUtility::mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if ($file !== '.' && $file !== '..') {
                if (is_dir($src . '/' . $file)) {
                    self::recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    $success = copy($src . '/' . $file, $dst . '/' . $file);
                    if (!$success) {
                        throw new Exception('Could not copy ' . $src . '/' . $file . ' to ' . $dst . '/' . $file);
                    }
                }
            }
        }
        closedir($dir);
    }
}
