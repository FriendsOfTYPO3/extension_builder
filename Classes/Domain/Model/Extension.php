<?php

namespace EBT\ExtensionBuilder\Domain\Model;

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

use EBT\ExtensionBuilder\Domain\Exception\ExtensionException;
use EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Schema for a whole extension
 */
class Extension
{
    const STATE_ALPHA = 0;
    const STATE_BETA = 1;
    const STATE_STABLE = 2;
    const STATE_EXPERIMENTAL = 3;
    const STATE_TEST = 4;

    /**
     * the extension key
     *
     * @var string
     */
    protected $extensionKey = '';
    /**
     * @var string
     */
    protected $vendorName = '';
    /**
     * extension's name
     *
     * @var string
     */
    protected $name = '';
    /**
     * extension directory
     *
     * @var string
     */
    protected $extensionDir = '';
    /**
     * extension's version
     *
     * @var string
     */
    protected $version = '';
    /**
     * @var string
     */
    protected $description = '';
    /**
     * The original extension key (if an extension was renamed)
     *
     * @var string
     */
    protected $originalExtensionKey = '';
    /**
     * The initial vendorname (if the vendor name was changed)
     *
     * @var string
     */
    protected $originalVendorName = '';
    /**
     * @var array
     */
    protected $settings = [];
    /**
     * @var string
     */
    protected $category;
    /**
     * @var bool
     */
    protected $supportVersioning = true;
    /**
     * @var bool
     */
    protected $supportLocalization = true;
    /**
     * @var bool
     */
    protected $generateDocumentationTemplate = true;
    /**
     * @var string
     */
    protected $sourceLanguage = 'en';
    /**
     * The extension's state. One of the STATE_* constants.
     * @var int
     */
    protected $state = 0;
    /**
     * an array keeping all md5 hashes of all files in the extension to detect modifications
     *
     * @var string[]
     */
    protected $md5Hashes = [];
    /**
     * all domain objects
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject[]
     */
    protected $domainObjects = [];
    /**
     * the Persons working on the Extension
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\Person[]
     */
    protected $persons = [];
    /**
     * plugins
     *
     * @var array<\EBT\ExtensionBuilder\Domain\Model\Plugin>
     */
    private $plugins = [];
    /**
     * backend modules
     *
     * @var array<\EBT\ExtensionBuilder\Domain\Model\BackendModule>
     */
    private $backendModules = [];
    /**
     * was the extension renamed?
     * @var bool
     */
    private $renamed = false;
    /**
     * @var array
     */
    private $dependencies = [];
    /**
     * the lowest required TYPO3 version
     * @var float
     */
    private $targetVersion = 6.0;
    /**
     * @var string
     */
    protected $previousExtensionDirectory = '';
    /**
     * @var string
     */
    protected $previousExtensionKey = '';

    /**
     * @var string|null
     */
    protected $storagePath;

    /**
     * @return string
     */
    public function getExtensionKey(): string
    {
        return $this->extensionKey;
    }

    /**
     * @return string
     */
    public function getExtensionName(): string
    {
        return GeneralUtility::underscoredToUpperCamelCase($this->extensionKey);
    }

    /**
     * @param string $extensionKey
     */
    public function setExtensionKey(string $extensionKey): void
    {
        $this->extensionKey = $extensionKey;
    }

    /**
     * @return string
     */
    public function getOriginalExtensionKey(): string
    {
        return $this->originalExtensionKey;
    }

    /**
     * @param string $extensionKey
     */
    public function setOriginalExtensionKey(string $extensionKey): void
    {
        $this->originalExtensionKey = $extensionKey;
    }

    /**
     * @param array $settings
     */
    public function setSettings($settings): void
    {
        $this->settings = $settings;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getExtensionDir(): string
    {
        if (empty($this->extensionDir)) {
            if (empty($this->extensionKey)) {
                throw new \Exception('ExtensionDir can only be created if an extensionKey is defined first');
            }
            $this->extensionDir = ($this->storagePath ?? Environment::getPublicPath() . '/typo3conf/ext/') . $this->extensionKey . '/';
        }
        return $this->extensionDir;
    }

    /**
     * @param string $extensionDir
     */
    public function setExtensionDir(string $extensionDir): void
    {
        $this->extensionDir = $extensionDir;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $vendorName
     */
    public function setVendorName(string $vendorName): void
    {
        $this->vendorName = $vendorName;
    }

    /**
     * @return string
     */
    public function getVendorName(): string
    {
        return $this->vendorName;
    }

    /**
     * @return string
     */
    public function getOriginalVendorName(): string
    {
        return $this->originalVendorName;
    }

    /**
     * @param string $vendorName
     */
    public function setOriginalVendorName(string $vendorName): void
    {
        $this->originalVendorName = $vendorName;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version): void
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState(int $state): void
    {
        $this->state = $state;
    }

    /**
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject[]
     */
    public function getDomainObjects(): array
    {
        return $this->domainObjects;
    }

    /**
     * An array of domain objects for which a controller should be built.
     * Retruns ttrue if there are any actions defined for these domain objects
     *
     * @return array
     */
    public function getDomainObjectsForWhichAControllerShouldBeBuilt(): array
    {
        $domainObjects = [];
        foreach ($this->domainObjects as $domainObject) {
            if (count($domainObject->getActions()) > 0) {
                $domainObjects[] = $domainObject;
            }
        }
        return $domainObjects;
    }

    /**
     * get all domainobjects that are mapped to existing tables
     * @return array|null
     */
    public function getDomainObjectsThatNeedMappingStatements(): ?array
    {
        $domainObjectsThatNeedMappingStatements = [];
        foreach ($this->domainObjects as $domainObject) {
            if ($domainObject->getNeedsMappingStatement()) {
                $domainObjectsThatNeedMappingStatements[] = $domainObject;
            }
        }
        if (!empty($domainObjectsThatNeedMappingStatements)) {
            return $domainObjectsThatNeedMappingStatements;
        }

        return null;
    }

    /**
     * return tables that need a type field to enable
     * single table inheritance or mapping to an existing table
     */
    public function getTablesForTypeFieldDefinitions(): array
    {
        $tableNames = [];
        foreach ($this->getDomainObjects() as $domainObject) {
            if ($domainObject->isMappedToExistingTable() || $domainObject->getParentClass()) {
                $tableNames[] = $domainObject->getMapToTable();
            }
        }
        return array_unique($tableNames);
    }

    /**
     * get all domainobjects that are mapped to existing tables
     * @return array|null
     */
    public function getClassHierarchy(): ?array
    {
        $classHierarchy = [];
        foreach ($this->domainObjects as $domainObject) {
            if ($domainObject->isSubclass()) {
                $parentClass = $domainObject->getParentClass();
                if (strpos($parentClass, '\\') === 0) {
                    $parentClass = substr($parentClass, 1);
                }
                if (!is_array($classHierarchy[$parentClass])) {
                    $classHierarchy[$parentClass] = [];
                }
                $classHierarchy[$parentClass][] = $domainObject;
            }
        }
        if (!empty($classHierarchy)) {
            return $classHierarchy;
        }

        return null;
    }

    /**
     * needed to get the right order for models
     * extending other models parents have to be ordered before their children
     */
    public function getDomainObjectsInHierarchicalOrder(): array
    {
        $domainObjects = $this->getDomainObjects();
        $sortByParent = static function (DomainObject $domainObject1, DomainObject $domainObject2) {
            if ($domainObject1->getParentClass() === $domainObject2->getFullQualifiedClassName()) {
                return 1;
            }
            if ($domainObject2->getParentClass() === $domainObject1->getFullQualifiedClassName()) {
                return -1;
            }
            return 0;
        };
        usort($domainObjects, $sortByParent);
        return $domainObjects;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject1
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject2
     * @param array $classHierarchy
     * @return bool
     */
    protected function isParentOf($domainObject1, $domainObject2, $classHierarchy): bool
    {
        return $domainObject2->getParentClass() === $domainObject1->getFullQualifiedClassName();
    }

    /**
     * Add a domain object to the extension. Creates the reverse link as well.
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     *
     * @throws \EBT\ExtensionBuilder\Domain\Exception\ExtensionException
     */
    public function addDomainObject(DomainObject $domainObject): void
    {
        $domainObject->setExtension($this);
        foreach (array_keys($this->domainObjects) as $existingDomainObjectName) {
            if (strtolower($domainObject->getName()) == strtolower($existingDomainObjectName)) {
                throw new ExtensionException(
                    'Duplicate domain object name "' . $domainObject->getName() . '".',
                    ExtensionValidator::ERROR_DOMAINOBJECT_DUPLICATE
                );
            }
        }
        $this->domainObjects[$domainObject->getName()] = $domainObject;
    }

    /**
     * @param string $domainObjectName
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject
     */
    public function getDomainObjectByName($domainObjectName): ?DomainObject
    {
        return $this->domainObjects[$domainObjectName] ?? null;
    }

    /**
     * returns the extension key a prefix tx_  and without underscore
     */
    public function getShortExtensionKey(): string
    {
        return 'tx_' . str_replace('_', '', $this->getExtensionKey());
    }

    /**
     * returns the extension key without underscore
     * (Used in Typoscript module signature)
     */
    public function getUnprefixedShortExtensionKey()
    {
        return str_replace('_', '', $this->getExtensionKey());
    }

    /**
     * Returns the Persons
     *
     * @return array<\EBT\ExtensionBuilder\Domain\Model\Person>
     */
    public function getPersons(): array
    {
        return $this->persons;
    }

    /**
     * Sets the Persons
     *
     * @param array <\EBT\ExtensionBuilder\Domain\Model\Person> $persons
     * @return void
     */
    public function setPersons(array $persons): void
    {
        $this->persons = $persons;
    }

    /**
     * Adds a Person to the end of the current Set of Persons.
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\Person $person
     * @return void
     */
    public function addPerson($person): void
    {
        $this->persons[] = $person;
    }

    /**
     * Setter for plugin
     *
     * @param array<\EBT\ExtensionBuilder\Domain\Model\Plugin> $plugins
     * @return void
     */
    public function setPlugins(array $plugins): void
    {
        $this->plugins = $plugins;
    }

    /**
     * Getter for $plugin
     *
     * @return array<\EBT\ExtensionBuilder\Domain\Model\Plugin>
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    /**
     * @return bool
     */
    public function hasPlugins(): bool
    {
        return count($this->plugins) > 0;
    }

    /**
     * Add $plugin
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\Plugin
     * @return void
     */
    public function addPlugin(Plugin $plugin): void
    {
        $this->plugins[] = $plugin;
    }

    /**
     * Setter for backendModule
     *
     * @param array<\EBT\ExtensionBuilder\Domain\Model\BackendModule> $backendModules
     * @return void
     */
    public function setBackendModules(array $backendModules): void
    {
        $this->backendModules = $backendModules;
    }

    /**
     * Getter for $backendModule
     *
     * @return array<\EBT\ExtensionBuilder\Domain\Model\Plugin>
     */
    public function getBackendModules(): array
    {
        return $this->backendModules;
    }

    /**
     * Add $backendModule
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\BackendModule
     * @return void
     */
    public function addBackendModule(BackendModule $backendModule): void
    {
        $this->backendModules[] = $backendModule;
    }

    /**
     * @return bool
     */
    public function hasBackendModules(): bool
    {
        return count($this->backendModules) > 0;
    }

    public function getReadableState(): string
    {
        switch ($this->getState()) {
            case self::STATE_ALPHA:
                return 'alpha';
            case self::STATE_BETA:
                return 'beta';
            case self::STATE_STABLE:
                return 'stable';
            case self::STATE_EXPERIMENTAL:
                return 'experimental';
            case self::STATE_TEST:
                return 'test';
        }
        return '';
    }

    /**
     * @return string
     */
    public function getCssClassName(): string
    {
        return 'tx-' . str_replace('_', '-', $this->getExtensionKey());
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    public function isModified($filePath): bool
    {
        if (is_file($filePath) && isset($this->md5Hashes[$filePath])) {
            if (md5_file($filePath) != $this->md5Hashes[$filePath]) {
                return true;
            }
        }
        return false;
    }

    /**
     * setter for md5 hashes
     *
     * @param array $md5Hashes
     *
     * @return void
     */
    public function setMD5Hashes(array $md5Hashes): void
    {
        $this->md5Hashes = $md5Hashes;
    }

    /**
     * getter for md5 hashes
     * @return array $md5Hashes
     */
    public function getMD5Hashes(): array
    {
        return $this->md5Hashes;
    }

    /**
     * calculates all md5 hashes
     *
     * @param string $filePath
     */
    public function setMD5Hash($filePath): void
    {
        $this->md5Hashes[$filePath] = md5_file($filePath);
    }

    /**
     * Get the previous extension directory
     * if the extension was renamed it is different from $this->extensionDir
     *
     * @return string
     */
    public function getPreviousExtensionDirectory(): string
    {
        if ($this->isRenamed()) {
            $originalExtensionKey = $this->getOriginalExtensionKey();
            $this->previousExtensionDirectory = Environment::getPublicPath() . '/typo3conf/ext/' . $originalExtensionKey . '/';
            $this->previousExtensionKey = $originalExtensionKey;
            return $this->previousExtensionDirectory;
        }

        return $this->extensionDir;
    }

    /**
     * @return string|null
     */
    public function getStoragePath(): ?string
    {
        return $this->storagePath;
    }

    /**
     * @param string|null $storagePath
     */
    public function setStoragePath(?string $storagePath): void
    {
        if ($storagePath !== null) {
            $storagePath = rtrim($storagePath, '/') . '/';
        }
        $this->storagePath = $storagePath;
    }

    /**
     * @return bool
     */
    public function isRenamed(): bool
    {
        $originalExtensionKey = $this->getOriginalExtensionKey();
        if (!empty($originalExtensionKey) && $originalExtensionKey != $this->getExtensionKey()) {
            $this->renamed = true;
        }
        return $this->renamed;
    }

    /**
     * @return bool
     */
    public function vendorNameChanged(): bool
    {
        $originalVendorName = $this->getOriginalVendorName();
        if (!empty($originalVendorName) && $originalVendorName != $this->getVendorName()) {
            $this->renamed = true;
        }
        return $this->renamed;
    }

    /**
     * @return string
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    /**
     * @param bool $supportVersioning
     */
    public function setSupportVersioning($supportVersioning): void
    {
        $this->supportVersioning = $supportVersioning;
    }

    /**
     * @return bool
     */
    public function getSupportVersioning(): bool
    {
        return $this->supportVersioning;
    }

    /**
     * @param array $dependencies
     */
    public function setDependencies($dependencies): void
    {
        $this->dependencies = $dependencies;
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @param float $targetVersion
     */
    public function setTargetVersion($targetVersion): void
    {
        $this->targetVersion = $targetVersion;
    }

    /**
     * @return float
     */
    public function getTargetVersion(): float
    {
        return $this->targetVersion;
    }

    /**
     * @return string
     */
    public function getNamespaceName(): string
    {
        return $this->getVendorName() . '\\' . $this->getExtensionName();
    }

    /**
     * @param bool $supportLocalization
     */
    public function setSupportLocalization(bool $supportLocalization): void
    {
        $this->supportLocalization = $supportLocalization;
    }

    /**
     * @return bool
     */
    public function getSupportLocalization(): bool
    {
        return $this->supportLocalization;
    }

    /**
     * @param bool $generateDocumentationTemplate
     */
    public function setGenerateDocumentationTemplate(bool $generateDocumentationTemplate): void
    {
        $this->generateDocumentationTemplate = $generateDocumentationTemplate;
    }

    /**
     * @return bool
     */
    public function getGenerateDocumentationTemplate(): bool
    {
        return $this->generateDocumentationTemplate;
    }

    /**
     * @return string
     */
    public function getSourceLanguage(): string
    {
        return $this->sourceLanguage;
    }

    /**
     * @param string $sourceLanguage
     */
    public function setSourceLanguage(string $sourceLanguage): void
    {
        $this->sourceLanguage = $sourceLanguage;
    }

    /**
     * @return array
     */
    public function getComposerInfo(): array
    {
        $composerExtensionKey = strtolower(str_replace('_', '-', $this->extensionKey));
        $info = [
            'name' => strtolower($this->vendorName) . '/' . $composerExtensionKey,
            'type' => 'typo3-cms-extension',
            'description' => $this->description,
            'authors' => [],
            'require' => [
                'typo3/cms-core' => '^10.4'
            ],
            'autoload' => [
                'psr-4' => [
                    $this->getNamespaceName() . '\\' => 'Classes'
                ]
            ],
            'autoload-dev' => [
                'psr-4' => [
                    $this->getNamespaceName() . '\\Tests\\' => 'Tests'
                ]
            ],
            'replace' => [
                strtolower($this->vendorName) . '/' . $composerExtensionKey => 'self.version',
                'typo3-ter/' . $composerExtensionKey => 'self.version'
            ]
        ];
        foreach ($this->persons as $person) {
            $author = [
                'name' => $person->getName()
            ];
            if ($person->getRole() !== '') {
                $author['role'] = $person->getRole();
            }
            $info['authors'][] = $author;
        }
        return $info;
    }
}
