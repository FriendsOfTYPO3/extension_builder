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
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ManyToManyRelation;
use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Domain\Model\File;
use EBT\ExtensionBuilder\Utility\Inflector;
use Exception;
use PhpParser\Error as PhpParserError;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;

/**
 * Performs all changes that are required to adapt the
 * existing classes and methods to the changes in the configurations
 */
class RoundTrip implements SingletonInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var string
     */
    public const SPLIT_TOKEN = '## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder';

    public const OVERWRITE_SETTINGS_SKIP = -1;
    public const OVERWRITE_SETTINGS_MERGE = 1;
    public const OVERWRITE_SETTINGS_KEEP = 2;

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

    private array $parseWarnings = [];

    public function __construct(
        private readonly ParserService $parserService,
        private readonly ExtensionBuilderConfigurationManager $configurationManager,
        private readonly ExtensionSchemaBuilder $extensionSchemaBuilder,
    ) {}

    public function getParseWarnings(): array
    {
        return $this->parseWarnings;
    }

    /**
     * Returns a structured list of changes the roundtrip would apply, without modifying any files.
     * Must be called after initialize().
     *
     * @param Extension $current
     * @return array{hasChanges: bool, modifiedFiles: array, deletedFiles: array}
     */
    public function previewChanges(Extension $current): array
    {
        $result = ['hasChanges' => false, 'modifiedFiles' => [], 'deletedFiles' => []];

        $currentByUid = [];
        foreach ($current->getDomainObjects() as $obj) {
            $currentByUid[$obj->getUniqueIdentifier()] = $obj;
        }

        foreach ($currentByUid as $uid => $currentObj) {
            if (!isset($this->previousDomainObjects[$uid])) {
                continue;
            }
            $entries = $this->computeObjectFileChanges($this->previousDomainObjects[$uid], $currentObj);
            if (!empty($entries)) {
                $result['modifiedFiles'] = array_merge($result['modifiedFiles'], $entries);
                $result['hasChanges'] = true;
            }
        }

        foreach ($this->previousDomainObjects as $uid => $oldObj) {
            if (isset($currentByUid[$uid])) {
                continue;
            }
            $dir = $this->previousExtensionDirectory;
            $modelFile = FileGenerator::getFolderForClassFile($dir, 'Model', false) . $oldObj->getName() . '.php';
            if (file_exists($modelFile)) {
                $result['deletedFiles'][] = str_replace($dir, '', $modelFile);
                $result['hasChanges'] = true;
            }
            if ($oldObj->isAggregateRoot()) {
                $controllerFile = FileGenerator::getFolderForClassFile($dir, 'Controller', false) . $oldObj->getName() . 'Controller.php';
                if (file_exists($controllerFile)) {
                    $result['deletedFiles'][] = str_replace($dir, '', $controllerFile);
                }
                $repoFile = FileGenerator::getFolderForClassFile($dir, 'Repository', false) . $oldObj->getName() . 'Repository.php';
                if (file_exists($repoFile)) {
                    $result['deletedFiles'][] = str_replace($dir, '', $repoFile);
                }
            }
        }

        return $result;
    }

    private function computeObjectFileChanges(DomainObject $old, DomainObject $current): array
    {
        $result = [];
        $dir = $this->previousExtensionDirectory;
        $renamed = $old->getName() !== $current->getName();
        $methodChanges = $this->computePropertyMethodChanges($old, $current);

        $modelFile = FileGenerator::getFolderForClassFile($dir, 'Model', false) . $old->getName() . '.php';
        if (file_exists($modelFile) && (!empty($methodChanges) || $renamed)) {
            $entry = ['path' => str_replace($dir, '', $modelFile), 'changes' => $methodChanges];
            if ($renamed) {
                $entry['renamedTo'] = $current->getName() . '.php';
            }
            $result[] = $entry;
        }

        if ($renamed) {
            $controllerFile = FileGenerator::getFolderForClassFile($dir, 'Controller', false) . $old->getName() . 'Controller.php';
            if (file_exists($controllerFile)) {
                $result[] = ['path' => str_replace($dir, '', $controllerFile), 'renamedTo' => $current->getName() . 'Controller.php', 'changes' => []];
            }
            $repoFile = FileGenerator::getFolderForClassFile($dir, 'Repository', false) . $old->getName() . 'Repository.php';
            if (file_exists($repoFile)) {
                $result[] = ['path' => str_replace($dir, '', $repoFile), 'renamedTo' => $current->getName() . 'Repository.php', 'changes' => []];
            }
        }

        return $result;
    }

    private function computePropertyMethodChanges(DomainObject $old, DomainObject $current): array
    {
        $changes = [];
        $oldByUid = [];
        foreach ($old->getProperties() as $p) {
            $oldByUid[$p->getUniqueIdentifier()] = $p;
        }
        $newByUid = [];
        foreach ($current->getProperties() as $p) {
            $newByUid[$p->getUniqueIdentifier()] = $p;
        }

        foreach ($oldByUid as $uid => $oldProp) {
            if (!isset($newByUid[$uid])) {
                foreach ($this->methodNamesForProperty($oldProp) as $method) {
                    $changes[] = ['type' => 'removed', 'method' => $method];
                }
            } elseif ($oldProp->getName() !== $newByUid[$uid]->getName()) {
                $newProp = $newByUid[$uid];
                $oldMethods = $this->methodNamesForProperty($oldProp);
                $newMethods = $this->methodNamesForProperty($newProp);
                foreach ($oldMethods as $key => $oldMethod) {
                    $newMethod = $newMethods[$key] ?? null;
                    if ($newMethod !== null && $oldMethod !== $newMethod) {
                        $changes[] = ['type' => 'renamed', 'from' => $oldMethod, 'to' => $newMethod];
                    }
                }
            }
        }

        foreach ($newByUid as $uid => $newProp) {
            if (!isset($oldByUid[$uid])) {
                foreach ($this->methodNamesForProperty($newProp) as $method) {
                    $changes[] = ['type' => 'added', 'method' => $method];
                }
            }
        }

        return $changes;
    }

    private function methodNamesForProperty(AbstractProperty $property): array
    {
        $name = $property->getName();
        $methods = [
            'get' => 'get' . ucfirst($name),
            'set' => 'set' . ucfirst($name),
        ];
        if ($property->isAnyToManyRelation()) {
            $methods['add'] = 'add' . ucfirst(Inflector::singularize($name));
            $methods['remove'] = 'remove' . ucfirst(Inflector::singularize($name));
        }
        if ($property->isBoolean()) {
            $methods['is'] = 'is' . ucfirst($name);
        }
        return $methods;
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
            $jsonConfig = $this->configurationManager->getExtensionBuilderConfiguration($this->previousExtensionKey, $extension->getStoragePath());
            if ($jsonConfig === null) {
                $this->logger?->warning(
                    'ExtensionBuilder.json exists but could not be parsed for extension "' . $this->previousExtensionKey . '"',
                    ['extensionKey' => $this->previousExtensionKey, 'storagePath' => $extension->getStoragePath()]
                );
                return;
            }
            $this->previousExtension = $this->extensionSchemaBuilder->build($jsonConfig);
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
                try {
                    $this->classFileObject = $this->parserService->parseFile($fileName);
                } catch (PhpParserError | Exception $e) {
                    $this->logger?->warning('Cannot parse ' . $fileName . ': ' . $e->getMessage());
                    $this->parseWarnings[] = 'Could not read ' . $fileName
                        . ' — the file may have a syntax error. Roundtrip was skipped for this class;'
                        . ' your custom code may not be preserved. Please check the file manually.';
                    return null;
                }
                $this->classObject = $this->classFileObject->getFirstClass();
                if ($oldDomainObject->getName() != $currentDomainObject->getName() || $this->extensionRenamed) {
                    if (!$this->extensionRenamed) {
                        self::log(
                            'domainObject renamed. old: ' . $oldDomainObject->getName() . ' new: '
                            . $currentDomainObject->getName(),
                            'extension_builder'
                        );
                        $oldTable = $oldDomainObject->getDatabaseTableName();
                        $newTable = $currentDomainObject->getDatabaseTableName();
                        $this->parseWarnings[] = 'Domain object renamed: database table must be renamed manually from '
                            . '"' . $oldTable . '" to "' . $newTable . '". '
                            . 'TCA will be regenerated with the new table name.';
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
                    // remove the repository when transitioning from AggregateRoot to non-AggregateRoot
                    if ($oldDomainObject->isAggregateRoot()) {
                        $this->cleanUp(
                            FileGenerator::getFolderForClassFile($extensionDir, 'Repository'),
                            $oldDomainObject->getName() . 'Repository.php'
                        );
                    }
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
                $this->updateExtensionKeyNamespaceSegment();
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
                try {
                    $this->classFileObject = $this->parserService->parseFile($fileName);
                } catch (PhpParserError | Exception $e) {
                    $this->logger?->warning('Cannot parse ' . $fileName . ': ' . $e->getMessage());
                    $this->parseWarnings[] = 'Could not read ' . $fileName
                        . ' — the file may have a syntax error. Roundtrip was skipped for this class;'
                        . ' your custom code may not be preserved. Please check the file manually.';
                    return null;
                }
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
                try {
                    $this->classFileObject = $this->parserService->parseFile($fileName);
                } catch (PhpParserError | Exception $e) {
                    $this->logger?->warning('Cannot parse ' . $fileName . ': ' . $e->getMessage());
                    $this->parseWarnings[] = 'Could not read ' . $fileName
                        . ' — the file may have a syntax error. Roundtrip was skipped for this class;'
                        . ' your custom code may not be preserved. Please check the file manually.';
                    return null;
                }
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
                $this->updateExtensionKeyNamespaceSegment();
                $this->classFileObject->setClasses([$this->classObject]);

                return $this->classFileObject;
            }

            return null;
        }

        $fileName = FileGenerator::getFolderForClassFile($extensionDir, 'Controller', false);
        $fileName .= $currentDomainObject->getName() . 'Controller.php';
        if (file_exists($fileName)) {
            try {
                $this->classFileObject = $this->parserService->parseFile($fileName);
            } catch (PhpParserError | Exception $e) {
                $this->logger?->warning('Cannot parse ' . $fileName . ': ' . $e->getMessage());
                return null;
            }
            $this->classObject = $this->classFileObject->getFirstClass();
            $this->classObject->setFileName($fileName);
            $className = $currentDomainObject->getControllerClassName();
            $this->classObject->setName($className);
            if ($this->extension->vendorNameChanged()) {
                $this->updateVendorName();
            }
            $this->updateExtensionKeyNamespaceSegment();
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

        // Update use/import statements in the file's namespaces
        foreach ($this->classFileObject->getNamespaces() as $namespace) {
            $updatedAliases = [];
            foreach ($namespace->getAliasDeclarations() as $aliasDeclaration) {
                $aliasDeclaration['name'] = $this->renameVendor($aliasDeclaration['name']);
                $updatedAliases[] = $aliasDeclaration;
            }
            $namespace->setAliasDeclarations($updatedAliases);
        }

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
            $returnType = $method->getReturnType();
            if (!empty($returnType)) {
                $method->setReturnType($this->renameVendor($returnType));
            }
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

    protected function renameVendor(string $string): string
    {
        $original = $this->extension->getOriginalVendorName();
        $new = $this->extension->getVendorName();
        // Replace fully-qualified form (\OldVendor\)
        $result = str_replace('\\' . $original . '\\', '\\' . $new . '\\', $string);
        // Replace unqualified import form (OldVendor\ at the start of the string, e.g. use statements)
        if (str_starts_with($result, $original . '\\')) {
            $result = $new . substr($result, strlen($original));
        }
        return $result;
    }

    /**
     * Update the extension key segment in a namespace or type string when the extension is renamed.
     * Replaces the CamelCase form of the old extension key with the new one.
     */
    protected function renameExtensionKey(string $string): string
    {
        $oldExtName = GeneralUtility::underscoredToUpperCamelCase($this->previousExtensionKey);
        $newExtName = GeneralUtility::underscoredToUpperCamelCase($this->extension->getExtensionKey());
        if ($oldExtName === $newExtName) {
            return $string;
        }
        $result = str_replace('\\' . $oldExtName . '\\', '\\' . $newExtName . '\\', $string);
        if (str_starts_with($result, $oldExtName . '\\')) {
            $result = $newExtName . substr($result, strlen($oldExtName));
        }
        return $result;
    }

    /**
     * Update all namespace references when only the extension key (not vendor) changed.
     */
    protected function updateExtensionKeyNamespaceSegment(): void
    {
        if (!$this->extensionRenamed) {
            return;
        }
        $oldExtName = GeneralUtility::underscoredToUpperCamelCase($this->previousExtensionKey);
        $newExtName = GeneralUtility::underscoredToUpperCamelCase($this->extension->getExtensionKey());
        if ($oldExtName === $newExtName) {
            return;
        }

        $this->classObject->setNamespaceName($this->renameExtensionKey($this->classObject->getNamespaceName()));

        foreach ($this->classFileObject->getNamespaces() as $namespace) {
            $updatedAliases = [];
            foreach ($namespace->getAliasDeclarations() as $aliasDeclaration) {
                $aliasDeclaration['name'] = $this->renameExtensionKey($aliasDeclaration['name']);
                $updatedAliases[] = $aliasDeclaration;
            }
            $namespace->setAliasDeclarations($updatedAliases);
        }

        foreach ($this->classObject->getProperties() as $property) {
            foreach ($property->getTags() as $tagName => $tagValue) {
                if (is_array($tagValue)) {
                    $tagValue = $tagValue[0];
                }
                if (!empty($tagValue)) {
                    $property->setTag($tagName, $this->renameExtensionKey((string)$tagValue), true);
                }
            }
        }
        foreach ($this->classObject->getMethods() as $method) {
            $returnType = $method->getReturnType();
            if (!empty($returnType)) {
                $method->setReturnType($this->renameExtensionKey($returnType));
            }
            foreach ($method->getParameters() as $parameter) {
                $typeHint = $parameter->getTypeHint();
                if (!empty($typeHint)) {
                    $parameter->setTypeHint($this->renameExtensionKey($typeHint));
                }
            }
        }
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
                    $initializeMethodBodyStmts = $this->parserService->replaceNodeProperty(
                        $initializeMethod->getBodyStmts(),
                        [lcfirst($oldName) . 'Repository' => lcfirst($newName) . 'Repository']
                    );
                    $initializeMethod->setBodyStmts($initializeMethodBodyStmts);
                    $this->classObject->setMethod($initializeMethod);
                }

                $injectMethod = $this->classObject->getMethod($injectMethodName);
                if ($injectMethod != null) {
                    $this->classObject->removeMethod($injectMethodName);
                    $initializeMethodBodyStmts = $this->parserService->replaceNodeProperty(
                        $injectMethod->getBodyStmts(),
                        [lcfirst($oldName) . 'Repository' => lcfirst($newName) . 'Repository']
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

                // Handle constructor injection (TYPO3 v12+)
                $constructor = $this->classObject->getMethod('__construct');
                if ($constructor !== null) {
                    $oldRepositoryClass = $oldDomainObject->getFullyQualifiedDomainRepositoryClassName();
                    foreach ($constructor->getParameters() as $constructorParam) {
                        if ($oldRepositoryClass !== '' && str_contains($constructorParam->getTypeHint(), $oldRepositoryClass)) {
                            $constructorParam->setTypeHint($currentDomainObject->getFullyQualifiedDomainRepositoryClassName());
                            $constructorParam->setName(lcfirst($newName) . 'Repository');
                            $constructor->replaceParameter($constructorParam);
                        }
                    }
                    $this->classObject->setMethod($constructor);
                }

                foreach ($oldDomainObject->getActions() as $action) {
                    // here we have to update all the occurences of domain object names in action methods
                    $actionMethod = $this->classObject->getMethod($action->getName() . 'Action');
                    if ($actionMethod != null) {
                        $newActionMethodBody = $this->parserService->replaceNodeProperty(
                            $actionMethod->getBodyStmts(),
                            [lcfirst($oldName) . 'Repository' => lcfirst($newName) . 'Repository']
                        );
                        $actionMethod->setBodyStmts($newActionMethodBody);
                        $actionMethod->setTag('param', $currentDomainObject->getQualifiedClassName());

                        $parameters = $actionMethod->getParameters();
                        foreach ($parameters as &$parameter) {
                            if (str_contains($parameter->getTypeHint(), $oldDomainObject->getFullQualifiedClassName())) {
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
                            if (is_string($tagValue)) {
                                $tags[$tagName] = $this->replaceUpperAndLowerCase($oldName, $newName, $tagValue);
                            }
                        }
                        $actionMethod->setTags($tags);

                        $actionMethod->setDescription(
                            $this->replaceUpperAndLowerCase(
                                $oldName,
                                $newName,
                                $actionMethod->getDescription()
                            )
                        );

                        $actionMethod->setBodyStmts(
                            $this->parserService->replaceNodeProperty(
                                $actionMethod->getBodyStmts(),
                                [
                                    ucfirst($oldName) => ucfirst($newName),
                                    lcfirst($oldName) => lcfirst($newName),
                                ]
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
                try {
                    $this->classFileObject = $this->parserService->parseFile($fileName);
                } catch (PhpParserError | Exception $e) {
                    $this->logger?->warning('Cannot parse ' . $fileName . ': ' . $e->getMessage());
                    $this->parseWarnings[] = 'Could not read ' . $fileName
                        . ' — the file may have a syntax error. Roundtrip was skipped for this class;'
                        . ' your custom code may not be preserved. Please check the file manually.';
                    return null;
                }
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
                if ($this->extension->vendorNameChanged()) {
                    $this->updateVendorName();
                }
                $this->updateExtensionKeyNamespaceSegment();
                return $this->classFileObject;
            }
        } else {
            $fileName = FileGenerator::getFolderForClassFile($extensionDir, 'Repository', false);
            $fileName .= $currentDomainObject->getName() . 'Repository.php';
            if (file_exists($fileName)) {
                try {
                    $this->classFileObject = $this->parserService->parseFile($fileName);
                } catch (PhpParserError | Exception $e) {
                    $this->logger?->warning('Cannot parse ' . $fileName . ': ' . $e->getMessage());
                    $this->parseWarnings[] = 'Could not read ' . $fileName
                        . ' — the file may have a syntax error. Roundtrip was skipped for this class;'
                        . ' your custom code may not be preserved. Please check the file manually.';
                    return null;
                }
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
                    if ($oldProperty instanceof ManyToManyRelation) {
                        $this->parseWarnings[] = 'Relation type changed from manyToMany: MM table '
                            . '"' . $oldProperty->getRelationTableName() . '" data will not be migrated '
                            . 'automatically and may be lost.';
                    } elseif ($newProperty instanceof ManyToManyRelation) {
                        $this->parseWarnings[] = 'Relation type changed to manyToMany: a new MM table '
                            . '"' . $newProperty->getRelationTableName() . '" will be created. '
                            . 'Existing single-value relation data will not be migrated.';
                    }
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
                    if ($oldProperty->getName() !== $newProperty->getName()) {
                        $oldColumn = $oldDomainObject->getDatabaseTableName() . '.' . $oldProperty->getName();
                        $newColumn = $newDomainObject->getDatabaseTableName() . '.' . $newProperty->getName();
                        $this->parseWarnings[] = 'Property renamed: database column must be renamed manually '
                            . 'from "' . $oldColumn . '" to "' . $newColumn . '".';
                    }
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
        if (!str_contains($stringToParse, $separatorToken)) {
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
    protected function replaceUpperAndLowerCase(string $search, string $replace, string $haystack): string
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
        $iconFile = $iconsDirectory . $domainObject->getDatabaseTableName() . '.gif';
        foreach (['xml', 'xlf'] as $cshExt) {
            $cshFile = $languageDirectory . 'locallang_csh_' . $domainObject->getDatabaseTableName() . '.' . $cshExt;
            if (file_exists($cshFile)) {
                unlink($cshFile);
            }
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
            self::log('cleanUp File not found: ' . $path . $fileName, 'extension_builder');
            return;
        }
        unlink($path . $fileName);
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
        if (str_starts_with($path, $extension->getExtensionDir())) {
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
                        mkdir($tcaDir . 'Overrides', 0777, true);
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
     * Returns a list of available backups for an extension, newest first.
     *
     * @return array<array{directory: string, label: string, fileCount: int}>
     */
    public static function listBackups(string $extensionKey, ?string $backupDir): array
    {
        if (empty($backupDir) || !GeneralUtility::validPathStr($backupDir)) {
            return [];
        }
        if (!PathUtility::isAbsolutePath($backupDir)) {
            $backupDir = Environment::getProjectPath() . '/' . $backupDir;
        }
        $extensionBackupDir = rtrim($backupDir, '/') . '/' . $extensionKey . '/';
        if (!is_dir($extensionBackupDir)) {
            return [];
        }
        $backups = [];
        $entries = scandir($extensionBackupDir, SCANDIR_SORT_DESCENDING);
        if ($entries === false) {
            return [];
        }
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $fullPath = $extensionBackupDir . $entry;
            if (!is_dir($fullPath)) {
                continue;
            }
            $fileCount = iterator_count(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($fullPath, RecursiveDirectoryIterator::SKIP_DOTS)
                )
            );
            $parts = explode('-', $entry);
            $timestamp = end($parts);
            $label = is_numeric($timestamp)
                ? date('Y-m-d H:i:s', (int)$timestamp)
                : $entry;
            $backups[] = ['directory' => $entry, 'label' => $label, 'fileCount' => $fileCount];
        }
        return $backups;
    }

    /**
     * Restores an extension from a backup directory.
     * Creates a nested backup of the current state before restoring.
     *
     * @throws Exception
     */
    public static function restoreBackup(Extension $extension, string $backupDirectory, ?string $backupDir): void
    {
        if (!preg_match('/^[\w\-]+$/', $backupDirectory)) {
            throw new Exception('Invalid backup directory name: ' . $backupDirectory);
        }
        if (empty($backupDir) || !GeneralUtility::validPathStr($backupDir)) {
            throw new Exception('Backup directory is not configured.');
        }
        if (!PathUtility::isAbsolutePath($backupDir)) {
            $backupDir = Environment::getProjectPath() . '/' . $backupDir;
        }
        $backupSourceDir = rtrim($backupDir, '/') . '/' . $extension->getExtensionKey() . '/' . $backupDirectory;
        if (!is_dir($backupSourceDir)) {
            throw new Exception('Backup not found: ' . $backupDirectory);
        }

        // Safety: back up current state before overwriting
        self::backupExtension($extension, $backupDir);

        // Remove current extension dir and restore from backup
        $extensionDir = rtrim($extension->getExtensionDir(), '/');
        GeneralUtility::rmdir($extensionDir, true);
        self::recurse_copy($backupSourceDir, $extensionDir);
        self::log('Extension restored from backup: ' . $backupDirectory);
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
            mkdir($backupDir, 0777, true);
        }

        if (!is_writable($backupDir)) {
            throw new Exception('Backup directory is not writable: ' . $backupDir);
        }

        $backupDir .= $extension->getExtensionKey();
        // create a subdirectory for this extension
        if (!is_dir($backupDir)) {
            mkdir($backupDir);
        }
        // Add trailing slash
        if (strrpos($backupDir, '/') < strlen($backupDir) - 1) {
            $backupDir .= '/';
        }
        $backupDir .= date('Y-m-d-') . time();
        if (!is_dir($backupDir)) {
            mkdir($backupDir);
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
        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(self::class);
        match ((int)$severity) {
            2 => $logger->warning($message, $data),
            1 => $logger->info($message, $data),
            default => $logger->debug($message, $data),
        };
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
        if (!is_dir($dst)) {
            mkdir($dst);
        }
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
