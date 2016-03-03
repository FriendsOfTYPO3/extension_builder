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

use EBT\ExtensionBuilder\Configuration\ConfigurationManager;
use EBT\ExtensionBuilder\Domain\Model;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation;
use EBT\ExtensionBuilder\Utility\Inflector;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Performs all changes that are required to adapt the
 * existing classes and methods to the changes in the configurations
 */
class RoundTrip implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var string
     */
    const SPLIT_TOKEN = '## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder';
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\Extension
     */
    protected $previousExtension = null;
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\Extension
     */
    protected $extension = null;
    /**
     * if an extension was renamed this property keeps the
     * original extensionDirectory
     * otherwise it is set to the current extensionDir
     *
     * @var string
     */
    protected $previousExtensionDirectory = '';
    /**
     * the directory of the current extension
     *
     * @var string
     */
    protected $extensionDirectory = '';
    /**
     * if an extension was renamed this property keeps the old key
     * otherwise it is set to the current extensionKey
     *
     * @var string
     */
    protected $previousExtensionKey = '';
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject[]
     */
    protected $previousDomainObjects = array();
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject[]
     */
    protected $renamedDomainObjects = array();
    /**
     * @var \EBT\ExtensionBuilder\Service\Parser
     * @inject
     *
     */
    protected $parserService = null;
    /**
     * @var \EBT\ExtensionBuilder\Configuration\ConfigurationManager
     * @inject
     *
     */
    protected $configurationManager = null;
    /**
     * was the extension renamed?
     *
     * @var bool
     */
    protected $extensionRenamed = false;
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
     */
    protected $classObject = null;
    /**
     * The file object parsed from existing files
     * @var \EBT\ExtensionBuilder\Domain\Model\File
     */
    protected $classFileObject = null;
    /**
     * @var array
     */
    protected $settings = array();

    /**
     * If a JSON file is found in the extensions directory the previous version
     * of the extension is build to compare it with the new configuration coming
     * from the extension builder input
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     */
    public function initialize(Model\Extension $extension)
    {
        $this->extension = $extension;
        $this->extensionDirectory = $this->extension->getExtensionDir();

        if (!$this->parserService instanceof \EBT\ExtensionBuilder\Service\Parser) {
            $this->parserService = GeneralUtility::makeInstance('EBT\\ExtensionBuilder\\Service\\Parser');
        }
        $this->settings = $this->configurationManager->getExtensionBuilderSettings();
        // defaults
        $this->previousExtensionDirectory = $this->extensionDirectory;
        $this->previousExtensionKey = $this->extension->getExtensionKey();

        if ($extension->isRenamed()) {
            $this->previousExtensionDirectory = $extension->getPreviousExtensionDirectory();
            $this->previousExtensionKey = $extension->getOriginalExtensionKey();
            $this->extensionRenamed = true;
            GeneralUtility::devLog('Extension renamed: ' . $this->previousExtensionKey . ' => ' . $this->extension->getExtensionKey(), 'extension_builder', 1, array('$previousExtensionDirectory ' => $this->previousExtensionDirectory));
        }

        // Rename the old kickstarter.json file to ExtensionBuilder.json
        if (file_exists($this->previousExtensionDirectory . 'kickstarter.json')) {
            rename(
                $this->previousExtensionDirectory . 'kickstarter.json',
                $this->previousExtensionDirectory . ConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE
            );
        }

        if (file_exists($this->previousExtensionDirectory . ConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE)) {
            $extensionSchemaBuilder = GeneralUtility::makeInstance('EBT\\ExtensionBuilder\\Service\\ExtensionSchemaBuilder');
            $jsonConfig = $this->configurationManager->getExtensionBuilderConfiguration($this->previousExtensionKey);
            GeneralUtility::devLog(
                'old JSON:' . $this->previousExtensionDirectory . 'ExtensionBuilder.json',
                'extension_builder',
                0,
                $jsonConfig
            );
            $this->previousExtension = $extensionSchemaBuilder->build($jsonConfig);
            $previousDomainObjects = $this->previousExtension->getDomainObjects();
            /** @var $previousDomainObjects \EBT\ExtensionBuilder\Domain\Model\DomainObject[] */
            foreach ($previousDomainObjects as $oldDomainObject) {
                $this->previousDomainObjects[$oldDomainObject->getUniqueIdentifier()] = $oldDomainObject;
                $this->log(
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
            $currentDomainsObjects = array();
            foreach ($this->extension->getDomainObjects() as $domainObject) {
                /** @var \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject */
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
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject The new domain object
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject OR null
     */
    public function getDomainModelClassFile(Model\DomainObject $currentDomainObject)
    {
        if (isset($this->previousDomainObjects[$currentDomainObject->getUniqueIdentifier()])) {
            $this->log('domainObject identified:' . $currentDomainObject->getName());
            $oldDomainObject = $this->previousDomainObjects[$currentDomainObject->getUniqueIdentifier()];
            /** @var \EBT\ExtensionBuilder\Domain\Model\DomainObject $oldDomainObject */
            $extensionDir = $this->previousExtensionDirectory;
            $fileName = FileGenerator::getFolderForClassFile($extensionDir, 'Model', false) . $oldDomainObject->getName() . '.php';
            if (file_exists($fileName)) {
                // import the classObject from the existing file
                $this->classFileObject = $this->parserService->parseFile($fileName);
                $this->classObject = $this->classFileObject->getFirstClass();
                if ($oldDomainObject->getName() != $currentDomainObject->getName() || $this->extensionRenamed) {
                    if (!$this->extensionRenamed) {
                        $this->log('domainObject renamed. old: ' . $oldDomainObject->getName() . ' new: ' . $currentDomainObject->getName(), 'extension_builder');
                    }
                    $newClassName = $currentDomainObject->getName();
                    $this->classObject->setName($newClassName);
                    $this->classObject->setFileName($currentDomainObject->getName() . '.php');
                    $this->cleanUp(FileGenerator::getFolderForClassFile($extensionDir, 'Model'), $oldDomainObject->getName() . '.php');
                    $this->cleanUp($extensionDir . 'Configuration/TCA/', $oldDomainObject->getName() . '.php');
                } else {
                    $this->classObject->setName($currentDomainObject->getName());
                }

                $this->updateModelClassProperties($oldDomainObject, $currentDomainObject);

                $newActions = array();
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
                        $parentClass = $this->configurationManager->getParentClassForEntityObject($this->extension->getExtensionKey());
                    } else {
                        $parentClass = $this->configurationManager->getParentClassForValueObject($this->extension->getExtensionKey());
                    }
                    $this->classObject->setParentClassName($parentClass);
                }

                if ($currentDomainObject->isEntity() && !$oldDomainObject->isEntity()) {
                    // the object type was changed in the modeler
                    $this->classObject->setParentClassName(
                        $this->configurationManager->getParentClassForEntityObject($this->extension->getExtensionKey())
                    );
                } elseif (!$currentDomainObject->isEntity() && $oldDomainObject->isEntity()) {
                    // the object type was changed in the modeler
                    $this->classObject->setParentClassName(
                        $this->configurationManager->getParentClassForValueObject($this->extension->getExtensionKey())
                    );
                }
                $this->classFileObject->setClasses(array($this->classObject));
                if ($this->extension->vendorNameChanged()) {
                    $this->updateVendorName();
                }
                return $this->classFileObject;
            } else {
                GeneralUtility::devLog('class file didn\'t exist:' . $fileName, 'extension_builder', 0);
            }
        } else {
            $this->log('domainObject not identified:' . $currentDomainObject->getName(), 0, $this->previousDomainObjects);
            $fileName = FileGenerator::getFolderForClassFile($this->extensionDirectory, 'Model', false);
            $fileName .= $currentDomainObject->getName() . '.php';
            if (file_exists($fileName)) {
                // import the classObject from the existing file
                $this->classFileObject = $this->parserService->parseFile($fileName);
                $this->classObject = $this->classFileObject->getFirstClass();
                $this->classObject->setFileName($fileName);
                $this->classObject->setName($currentDomainObject->getName());
                $this->log('class file found:' . $currentDomainObject->getName() . '.php', 0, $this->classObject->getNamespaceName());
                $this->classFileObject->setClasses(array($this->classObject));
                return $this->classFileObject;
            }
        }
        return null;
    }

    /**
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\File|null
     */
    public function getControllerClassFile(Model\DomainObject $currentDomainObject)
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
                    $injectMethodName = 'inject' . GeneralUtility::lcfirst($oldDomainObject->getName()) . 'Repository';
                    $this->classObject->removeMethod($injectMethodName);
                }

                $newActions = array();
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
                            $this->log('Action method removed:' . $oldAction->getName(), 0, $this->classObject->getMethods());
                        }
                    }
                    // we don't have to add new ones, this will be done automatically by the class builder
                }
                if ($this->extension->vendorNameChanged()) {
                    $this->updateVendorName();
                }
                $this->classFileObject->setClasses(array($this->classObject));

                return $this->classFileObject;
            } else {
                GeneralUtility::devLog('class file didn\'t exist:' . $fileName, 'extension_builder', 2);
                return null;
            }
        } else {
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
                $this->classFileObject->setClasses(array($this->classObject));

                return $this->classFileObject;
            } else {
                $this->log('No existing controller class:' . $fileName, 2);
            }
        }
        $this->log('No existing controller class:' . $currentDomainObject->getName(), 2);
        return null;
    }

    /**
     * update all relevant namespace parts in tags, typehints etc.
     */
    protected function updateVendorName()
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
        return str_replace('\\' . $this->extension->getOriginalVendorName() . '\\', '\\' . $this->extension->getVendorName() . '\\', $string);
    }

    /**
     * If a domainObject was renamed
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $oldDomainObject
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $currentDomainObject
     * @return void
     */
    protected function mapOldControllerToCurrentClassObject(Model\DomainObject $oldDomainObject, Model\DomainObject $currentDomainObject)
    {
        $extensionDir = $this->previousExtensionDirectory;
        $newClassName = $currentDomainObject->getName() . 'Controller';
        $newName = $currentDomainObject->getName();
        $oldName = $oldDomainObject->getName();
        $this->classObject->setName($newClassName);
        $this->classObject->setDescription($this->replaceUpperAndLowerCase($oldName, $newName, $this->classObject->getDescription()));
        if ($oldDomainObject->isAggregateRoot()) {

            // should we keep the old properties comments and tags?
            $this->classObject->removeProperty(GeneralUtility::lcfirst($oldName) . 'Repository');
            $injectMethodName = 'inject' . $oldName . 'Repository';
            if ($currentDomainObject->isAggregateRoot()) {
                // update the initializeAction method body
                $initializeMethod = $this->classObject->getMethod('initializeAction');
                if ($initializeMethod != null) {
                    $initializeMethodBodyStmts = $initializeMethod->getBodyStmts();
                    $initializeMethodBodyStmts = str_replace(
                        GeneralUtility::lcfirst($oldName) . 'Repository',
                        GeneralUtility::lcfirst($newName) . 'Repository',
                        $initializeMethodBodyStmts
                    );
                    $initializeMethod->setBodyStmts($initializeMethodBodyStmts);
                    $this->classObject->setMethod($initializeMethod);
                }

                $injectMethod = $this->classObject->getMethod($injectMethodName);
                if ($injectMethod != null) {
                    $this->classObject->removeMethod($injectMethodName);
                    $initializeMethodBodyStmts = str_replace(
                        GeneralUtility::lcfirst($oldName),
                        GeneralUtility::lcfirst($newName),
                        $injectMethod->getBodyStmts()
                    );
                    $injectMethod->setBodyStmts($initializeMethodBodyStmts);
                    $injectMethod->setTag('param', $currentDomainObject->getFullyQualifiedDomainRepositoryClassName() . ' $' . $newName . 'Repository');
                    $injectMethod->setName('inject' . $newName . 'Repository');
                    $parameter = new Model\ClassObject\MethodParameter(GeneralUtility::lcfirst($newName) . 'Repository');
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
                            GeneralUtility::lcfirst($oldName) . 'Repository',
                            GeneralUtility::lcfirst($newName) . 'Repository',
                            $actionMethodBody
                        );
                        $actionMethod->setBodyStmts($newActionMethodBody);
                        $actionMethod->setTag('param', $currentDomainObject->getQualifiedClassName());

                        $parameters = $actionMethod->getParameters();
                        foreach ($parameters as &$parameter) {
                            if (strpos($parameter->getTypeHint(), $oldDomainObject->getFullQualifiedClassName()) > -1) {
                                $parameter->setTypeHint($currentDomainObject->getFullQualifiedClassName());
                                $parameter->setName($this->replaceUpperAndLowerCase($oldName, $newName, $parameter->getName()));
                                $actionMethod->replaceParameter($parameter);
                            }
                        }

                        $tags = $actionMethod->getTags();
                        foreach ($tags as $tagName => $tagValue) {
                            $tags[$tagName] = $this->replaceUpperAndLowerCase($oldName, $newName, $tagValue);
                        }
                        $actionMethod->setTags($tags);

                        $actionMethod->setDescription($this->replaceUpperAndLowerCase($oldName, $newName, $actionMethod->getDescription()));

                        //TODO: this is not safe. Could rename unwanted variables
                        $actionMethod->setBodyStmts($this->replaceUpperAndLowerCase($oldName, $newName, $actionMethod->getBodyStmts()));
                        $this->classObject->setMethod($actionMethod);
                    }
                }
            } else {
                $this->classObject->removeMethod('initializeAction');
                $this->classObject->removeMethod($injectMethodName);
                $this->cleanUp(FileGenerator::getFolderForClassFile($extensionDir, 'Repository'), $oldName . 'Repository.php');
            }
        }

        $this->classObject->setFileName($newName . 'Controller.php');
        $this->cleanUp(FileGenerator::getFolderForClassFile($extensionDir, 'Controller'), $oldName . 'Controller.php');
    }

    /**
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $currentDomainObject
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject|null
     */
    public function getRepositoryClassFile(Model\DomainObject $currentDomainObject)
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
            } else {
                GeneralUtility::devLog('class file didn\'t exist:' . $fileName, 'extension_builder', 2);
            }
        } else {
            $fileName = FileGenerator::getFolderForClassFile($extensionDir, 'Repository', false);
            $fileName .= $currentDomainObject->getName() . 'Repository.php';
            if (file_exists($fileName)) {
                $this->classFileObject = $this->parserService->parseFile($fileName);
                $this->classObject = $this->classFileObject->getFirstClass();
                $this->classObject->setFileName($fileName);
                $this->classObject->setFileName($fileName);
                $this->log('existing Repository class:' . $fileName, 0, (array)$this->classObject);
                return $this->classFileObject;
            }
        }
        $this->log('No existing Repository class:' . $currentDomainObject->getName(), 2);
        return null;
    }

    /**
     * Compare the properties of each object and remove/update
     * the properties and the related methods
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $oldDomainObject
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $newDomainObject
     *
     * return void (all actions are performed on $this->classObject
     */
    protected function updateModelClassProperties(Model\DomainObject $oldDomainObject, Model\DomainObject $newDomainObject)
    {
        $newProperties = array();
        foreach ($newDomainObject->getProperties() as $property) {
            $newProperties[$property->getUniqueIdentifier()] = $property;
        }

        // compare all old properties with new ones
        foreach ($oldDomainObject->getProperties() as $oldProperty) {
            /* @var  Model\DomainObject\AbstractProperty $oldProperty
             * @var  Model\DomainObject\AbstractProperty $newProperty
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
                    $this->log(
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
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $propertyToRemove
     *
     * @return void
     */
    protected function removePropertyAndRelatedMethods($propertyToRemove)
    {
        $propertyName = $propertyToRemove->getName();
        $this->classObject->removeProperty($propertyName);
        if ($propertyToRemove->isAnyToManyRelation()) {
            $this->classObject->removeMethod('add' . ucfirst(Inflector::singularize($propertyName)));
            $this->classObject->removeMethod('remove' . ucfirst(Inflector::singularize($propertyName)));
            GeneralUtility::devLog('Methods removed: ' . 'add' . ucfirst(Inflector::singularize($propertyName)), 'extension_builder');
        }
        $this->classObject->removeMethod('get' . ucfirst($propertyName));
        $this->classObject->removeMethod('set' . ucfirst($propertyName));
        if ($propertyToRemove->isBoolean()) {
            $this->classObject->removeMethod('is' . ucfirst($propertyName));
        }
        GeneralUtility::devLog('Methods removed: ' . 'get' . ucfirst($propertyName), 'extension_builder');
    }

    /**
     * Rename a property and update comment (var tag and description)
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $oldProperty
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $newProperty
     *
     * @return void
     */
    protected function updateProperty($oldProperty, $newProperty)
    {
        $classProperty = $this->classObject->getProperty($oldProperty->getName());
        if ($classProperty) {
            $classProperty->setName($newProperty->getName());
            $classProperty->setTag('var', $newProperty->getTypeForComment());
            $newDescription = $newProperty->getDescription();
            if (empty($newDescription) || $newDescription == $newProperty->getName()) {
                $newDescription = str_replace($oldProperty->getName(), $newProperty->getName(), $classProperty->getDescription());
            }
            $classProperty->setDescription($newDescription);
            $this->classObject->removeProperty($oldProperty->getName());
            $this->classObject->setProperty($classProperty);
            if ($this->relatedMethodsNeedUpdate($oldProperty, $newProperty)) {
                $this->updatePropertyRelatedMethods($oldProperty, $newProperty);
            }
        }
    }

    /**
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $oldProperty
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $newProperty
     *
     * @return bool
     */
    protected function relatedMethodsNeedUpdate($oldProperty, $newProperty)
    {
        if ($this->extensionRenamed) {
            return true;
        }
        if ($newProperty->getName() != $oldProperty->getName()) {
            $this->log('property renamed:' . $oldProperty->getName() . ' ' . $newProperty->getName());
            return true;
        }
        if ($newProperty->getTypeForComment() != $this->updateExtensionKey($oldProperty->getTypeForComment())) {
            $this->log(
                'property type changed from ' . $this->updateExtensionKey($oldProperty->getTypeForComment())
                . ' to ' . $newProperty->getTypeForComment()
            );
            return true;
        }
        if ($newProperty->isRelation()) {
            /** @var $oldProperty \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation */
            // if only the related domain object was renamed
            $previousClassName = $this->updateExtensionKey($oldProperty->getForeignClassName());
            if ($this->getForeignClassName($newProperty) != $previousClassName) {
                $this->log(
                    'related domainObject was renamed:' . $previousClassName . ' ->' . $this->getForeignClassName($newProperty)
                );
                return true;
            }
        }
        return false;
    }

    /**
     * replace occurences of the old extension key with the new one
     * used to compare classNames
     * @param $stringToParse
     * @return string
     */
    protected function updateExtensionKey($stringToParse)
    {
        if (!$this->extensionRenamed) {
            return $stringToParse;
        }
        $separatorToken = '\\\\';
        if (strpos($stringToParse, $separatorToken) === false) {
            $separatorToken = '_';
        }
        $result = str_replace(
            $separatorToken . ucfirst($this->previousExtensionKey) . $separatorToken,
            $separatorToken . ucfirst($this->extension->getExtensionKey()) . $separatorToken,
            $stringToParse
        );
        return $result;
    }

    /**
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $oldProperty
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $newProperty
     *
     * @return void
     */
    protected function updatePropertyRelatedMethods($oldProperty, $newProperty)
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
                $this->log(
                    'Method removed:' . ClassBuilder::getMethodName($oldProperty, 'is'),
                    1,
                    $this->classObject->getMethods());
            }
        }
    }

    /**
     * update means renaming of method name, parameter and replacing
     * parameter names in method body
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $oldProperty
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $newProperty
     * @param string $methodType get,set,add,remove,is
     *
     * @return void
     */
    protected function updateMethod($oldProperty, $newProperty, $methodType)
    {
        $oldMethodName = ClassBuilder::getMethodName($oldProperty, $methodType);
        // the method to be merged
        $mergedMethod = $this->classObject->getMethod($oldMethodName);

        if (!$mergedMethod) {
            // no previous version of the method exists
            return;
        }
        $newMethodName = ClassBuilder::getMethodName($newProperty, $methodType);
        $this->log('updateMethod:' . $oldMethodName . '=>' . $newMethodName, 'extension_builder');

        if ($oldProperty->getName() != $newProperty->getName()) {
            // rename the method
            $mergedMethod->setName($newMethodName);

            $oldMethodBody = $mergedMethod->getBodyStmts();
            $oldComment = $mergedMethod->getDocComment();

            $newMethodBody = $this->replacePropertyNameInMethodBody($oldProperty->getName(), $newProperty->getName(), $oldMethodBody);
            $mergedMethod->setBodyStmts($newMethodBody);
        }

        // update the method parameters
        $methodParameters = $mergedMethod->getParameters();

        if (!empty($methodParameters)) {
            $parameterTags = $mergedMethod->getTagValues('param');
            foreach ($methodParameters as $methodParameter) {
                $oldParameterName = $methodParameter->getName();
                if ($oldParameterName == ClassBuilder::getParameterName($oldProperty, $methodType)) {
                    $newParameterName = ClassBuilder::getParameterName($newProperty, $methodType);
                    $methodParameter->setName($newParameterName);
                    $newMethodBody = $this->replacePropertyNameInMethodBody($oldParameterName, $newParameterName, $mergedMethod->getBodyStmts());
                    $mergedMethod->setBodyStmts($newMethodBody);
                }
                $typeHint = $methodParameter->getTypeHint();
                if ($typeHint) {
                    if ($oldProperty->isRelation()) {
                        /** @var $oldProperty \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation */
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

        $returnTagValue = $mergedMethod->getTagValue('return');
        if ($returnTagValue != 'void') {
            $mergedMethod->setTag('return', $newProperty->getTypeForComment() . ' ' . $newProperty->getName());
        }

        // replace property names in description
        $mergedMethod->setDescription(str_replace($oldProperty->getName(), $newProperty->getName(), $mergedMethod->getDescription()));
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
    protected function replaceUpperAndLowerCase($search, $replace, $haystack)
    {
        $result = str_replace(ucfirst($search), ucfirst($replace), $haystack);
        $result = str_replace(GeneralUtility::lcfirst($search), GeneralUtility::lcfirst($replace), $result);
        return $result;
    }

    /**
     * Replace all occurences of the old property name with the new name
     *
     * @param string $oldName
     * @param string $newName
     * @param string $methodBodyStmts
     *
     * @return string
     */
    protected function replacePropertyNameInMethodBody($oldName, $newName, $methodBodyStmts)
    {
        return $this->parserService->replaceNodeProperty(
            $methodBodyStmts,
            array($oldName => $newName)
        );
    }

    /**comments
     * if the foreign DomainObject was renamed, the relation has to be updated also
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation $relation
     * @return string className of foreign class
     */
    public function getForeignClassName($relation)
    {
        if ($relation->getForeignModel() && isset($this->renamedDomainObjects[$relation->getForeignModel()->getUniqueIdentifier()])) {
            /** @var $renamedObject \EBT\ExtensionBuilder\Domain\Model\DomainObject */
            $renamedObject = $this->renamedDomainObjects[$relation->getForeignModel()->getUniqueIdentifier()];
            return $renamedObject->getQualifiedClassName();
        } else {
            return $relation->getForeignClassName();
        }
    }

    /**
     * remove domainObject related files if a domainObject was deleted
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     * @return void
     */
    protected function removeDomainObjectFiles(Model\DomainObject $domainObject)
    {
        $this->log('Remove domainObject ' . $domainObject->getName());
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
            GeneralUtility::devLog('locallang_csh file removed: ' . $locallang_cshFile, 'extension_builder', 1);
        }
        if (file_exists($iconFile)) {
            unlink($iconFile);
            GeneralUtility::devLog('icon file removed: ' . $iconFile, 'extension_builder', 1);
        }
    }

    /**
     * remove class files that are not required any more, due to
     * renaming of ModelObjects or changed types
     * @param string $path
     * @param string $fileName
     * @return void
     */
    public function cleanUp($path, $fileName)
    {
        if ($this->extensionRenamed) {
            // wo won't delete the old extension!
            return;
        }
        if (!is_file($path . $fileName)) {
            GeneralUtility::devLog('cleanUp File not found: ' . $path . $fileName, 'extension_builder', 1);
            return;
        }
        unlink($path . $fileName);
    }

    /**
     *
     * @return array
     */
    public static function getExtConfiguration()
    {
        $extConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extension_builder']);
        return $extConfiguration;
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
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     * @return int overWriteSetting
     */
    public static function getOverWriteSettingForPath($path, $extension)
    {
        $map = array(
            'skip' => -1,
            'merge' => 1,
            'keep' => 2
        );

        $settings = $extension->getSettings();
        if (!is_array($settings)) {
            throw new \Exception('overWrite settings could not be parsed');
        }
        if (strpos($path, $extension->getExtensionDir()) === 0) {
            $path = str_replace($extension->getExtensionDir(), '', $path);
        }
        $pathParts = explode('/', $path);
        $overWriteSettings = $settings['overwriteSettings'];

        foreach ($pathParts as $pathPart) {
            if (isset($overWriteSettings[$pathPart]) && is_array($overWriteSettings[$pathPart])) {
                // step one level deeper
                $overWriteSettings = $overWriteSettings[$pathPart];
            } else {
                return $map[$overWriteSettings[$pathPart]];
            }
        }

        return 0;
    }

    /**
     * parse existing tca and set appropriate properties
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     * @return void
     */
    public static function prepareExtensionForRoundtrip(&$extension)
    {
        foreach ($extension->getDomainObjects() as $domainObject) {
            $existingTca = self::getTcaForDomainObject($domainObject);
            if ($existingTca) {
                foreach ($domainObject->getAnyToManyRelationProperties() as $relationProperty) {
                    if (isset($existingTca['columns'][$relationProperty->getName()]['config']['MM'])) {
                        self::log(
                            'Relation table for Model ' . $domainObject->getName() . ' relation ' . $relationProperty->getName(),
                            0,
                            $existingTca['columns'][$relationProperty->getName()]['config']
                        );
                        $relationProperty->setRelationTableName(
                            $existingTca['columns'][$relationProperty->getName()]['config']['MM']
                        );
                    }
                }
            }
            if (file_exists($extension->getExtensionDir() . 'Configuration/TCA/' . $domainObject->getName() . '.php')) {
                $extensionConfigurationJson = \EBT\ExtensionBuilder\Configuration\ConfigurationManager::getExtensionBuilderJson($extension->getExtensionKey());
                if (floatval($extensionConfigurationJson['log']['extension_builder_version']) <= 6.2) {
                    self::moveAdditionalTcaToOverrideFile($domainObject);
                }
            }
        }
    }

    /**
     * Returns the current TCA for a domain objects table if the
     * extension is installed
     * TODO: check for previous table name if an extension is renamed
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     *
     * @return array
     */
    protected static function getTcaForDomainObject($domainObject)
    {
        $tableName = $domainObject->getDatabaseTableName();
        if (isset($GLOBALS['TCA'][$tableName])) {
            return $GLOBALS['TCA'][$tableName];
        } else {
            return null;
        }
    }

    /**
     * Move custom TCA in files generated by EB versions <= 6.2
     * to the appropriate overrides files
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     */
    public static function moveAdditionalTcaToOverrideFile($domainObject)
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
                        \TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep($tcaDir, 'Overrides');
                    }
                    $success = \TYPO3\CMS\Core\Utility\GeneralUtility::writeFile($overrideDir . $domainObject->getDatabaseTableName() . '.php', $customFileContent);
                    if (!$success) {
                        throw new \Exception('File ' . $overrideDir . $domainObject->getDatabaseTableName() . '.php could not be created!');
                    } else {
                        unlink($existingTcaFile);
                    }
                }
            }
        }
    }

    /**
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     * @param string $backupDir
     *
     * @return void
     */
    public static function backupExtension(Model\Extension $extension, $backupDir)
    {
        if (empty($backupDir)) {
            throw new \Exception('Please define a backup directory in extension configuration!');
        } elseif (!GeneralUtility::validPathStr($backupDir)) {
            throw new \Exception('Backup directory is not a valid path: ' . $backupDir);
        } elseif (GeneralUtility::isAbsPath($backupDir)) {
            if (!GeneralUtility::isAllowedAbsPath($backupDir)) {
                throw new \Exception('Backup directory is not an allowed absolute path: ' . $backupDir);
            }
        } else {
            $backupDir = PATH_site . $backupDir;
        }
        if (strrpos($backupDir, '/') < strlen($backupDir) - 1) {
            $backupDir .= '/';
        }
        if (!is_dir($backupDir)) {
            throw new \Exception('Backup directory does not exist: ' . $backupDir);
        } elseif (!is_writable($backupDir)) {
            throw new \Exception('Backup directory is not writable: ' . $backupDir);
        }

        $backupDir .= $extension->getExtensionKey();
        // create a subdirectory for this extension
        if (!is_dir($backupDir)) {
            GeneralUtility::mkdir($backupDir);
        }
        if (strrpos($backupDir, '/') < strlen($backupDir) - 1) {
            $backupDir .= '/';
        }
        $backupDir .= date('Y-m-d-') . time();
        if (!is_dir($backupDir)) {
            GeneralUtility::mkdir($backupDir);
        }
        $extensionDir = substr($extension->getExtensionDir(), 0, strlen($extension->getExtensionDir()) - 1);
        try {
            self::recurse_copy($extensionDir, $backupDir);
        } catch (\Exception $e) {
            throw new \Exception('Code generation aborted:' . $e->getMessage());
        }
        self::log('Backup created in ' . $backupDir);
    }

    protected static function log($message, $severity = 0, $data = array())
    {
        GeneralUtility::devLog(
            $message,
            'extension_builder',
            $severity,
            $data
        );
    }

    /**
     *
     * @param string $src path to copy
     * @param string $dst destination
     *
     * @return void
     */
    public static function recurse_copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    $success = copy($src . '/' . $file, $dst . '/' . $file);
                    if (!$success) {
                        throw new \Exception('Could not copy ' . $src . '/' . $file . ' to ' . $dst . '/' . $file);
                    }
                }
            }
        }
        closedir($dir);
    }
}
