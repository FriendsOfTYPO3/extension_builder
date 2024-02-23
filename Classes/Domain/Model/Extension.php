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

namespace EBT\ExtensionBuilder\Domain\Model;

use EBT\ExtensionBuilder\Domain\Exception\ExtensionException;
use EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator;
use Exception;
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
     */
    protected string $extensionKey = '';
    protected string $vendorName = '';
    protected string $name = '';
    protected string $extensionDir = '';
    protected string $version = '';
    protected string $description = '';
    /**
     * The original extension key (if an extension was renamed)
     */
    protected string $originalExtensionKey = '';
    /**
     * The initial vendorname (if the vendor name was changed)
     */
    protected string $originalVendorName = '';
    protected array $settings = [];
    protected ?string $category = null;
    protected bool $supportVersioning = true;
    protected bool $supportLocalization = true;
    protected bool $generateDocumentationTemplate = false;
    protected bool $generateEmptyGitRepository = false;
    protected bool $generateEditorConfig = false;
    protected string $sourceLanguage = 'en';
    /**
     * The extension's state. One of the STATE_* constants.
     */
    protected int $state = 0;
    /**
     * an array keeping all md5 hashes of all files in the extension to detect modifications
     *
     * @var string[]
     */
    protected array $md5Hashes = [];
    /**
     * all domain objects
     *
     * @var DomainObject[]
     */
    protected array $domainObjects = [];
    /**
     * the Persons working on the Extension
     *
     * @var Person[]
     */
    protected array $persons = [];
    /**
     * plugins
     *
     * @var array<Plugin>
     */
    private array $plugins = [];
    /**
     * backend modules
     *
     * @var array<BackendModule>
     */
    private array $backendModules = [];
    /**
     * was the extension renamed?
     */
    private bool $renamed = false;
    /**
     * @var array
     */
    private array $dependencies = [];
    /**
     * the lowest required TYPO3 version
     */
    private float $targetVersion = 10.0;
    protected string $previousExtensionDirectory = '';
    protected string $previousExtensionKey = '';
    protected ?string $storagePath;

    public function getExtensionKey(): string
    {
        return $this->extensionKey;
    }

    public function getExtensionName(): string
    {
        return GeneralUtility::underscoredToUpperCamelCase($this->extensionKey);
    }

    public function setExtensionKey(string $extensionKey): void
    {
        $this->extensionKey = $extensionKey;
    }

    public function getOriginalExtensionKey(): string
    {
        return $this->originalExtensionKey;
    }

    public function setOriginalExtensionKey(string $extensionKey): void
    {
        $this->originalExtensionKey = $extensionKey;
    }

    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getExtensionDir(): string
    {
        if (empty($this->extensionDir)) {
            if (empty($this->extensionKey)) {
                throw new Exception('ExtensionDir can only be created if an extensionKey is defined first');
            }
            $this->extensionDir = ($this->storagePath ?? Environment::getPublicPath() . '/typo3conf/ext/') . $this->extensionKey . '/';
        }
        return $this->extensionDir;
    }

    public function setExtensionDir(string $extensionDir): void
    {
        $this->extensionDir = $extensionDir;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setVendorName(string $vendorName): void
    {
        $this->vendorName = $vendorName;
    }

    public function getVendorName(): string
    {
        return $this->vendorName;
    }

    public function getOriginalVendorName(): string
    {
        return $this->originalVendorName;
    }

    public function setOriginalVendorName(string $vendorName): void
    {
        $this->originalVendorName = $vendorName;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function setState(int $state): void
    {
        $this->state = $state;
    }

    /**
     * @return DomainObject[]
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
     * get all domain objects that are mapped to existing tables
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

    protected function isParentOf(DomainObject $domainObject1, DomainObject $domainObject2): bool
    {
        return $domainObject2->getParentClass() === $domainObject1->getFullQualifiedClassName();
    }

    /**
     * Add a domain object to the extension. Creates the reverse link as well.
     *
     * @param DomainObject $domainObject
     *
     * @throws ExtensionException
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

    public function getDomainObjectByName(string $domainObjectName): ?DomainObject
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
    public function getUnprefixedShortExtensionKey(): string
    {
        return str_replace('_', '', $this->getExtensionKey());
    }

    /**
     * Returns the Persons
     *
     * @return array<Person>
     */
    public function getPersons(): array
    {
        return $this->persons;
    }

    /**
     * Sets the Persons
     *
     * @param array<Person> $persons
     */
    public function setPersons(array $persons): void
    {
        $this->persons = $persons;
    }

    /**
     * Adds a Person to the end of the current Set of Persons.
     *
     * @param Person $person
     */
    public function addPerson(Person $person): void
    {
        $this->persons[] = $person;
    }

    public function setPlugins(array $plugins): void
    {
        $this->plugins = $plugins;
    }

    public function getPlugins(): array
    {
        return $this->plugins;
    }

    public function hasPlugins(): bool
    {
        return count($this->plugins) > 0;
    }

    public function addPlugin(Plugin $plugin): void
    {
        $this->plugins[] = $plugin;
    }

    public function setBackendModules(array $backendModules): void
    {
        $this->backendModules = $backendModules;
    }

    public function getBackendModules(): array
    {
        return $this->backendModules;
    }

    public function addBackendModule(BackendModule $backendModule): void
    {
        $this->backendModules[] = $backendModule;
    }

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

    public function getCssClassName(): string
    {
        return 'tx-' . str_replace('_', '-', $this->getExtensionKey());
    }

    public function isModified(string $filePath): bool
    {
        if (isset($this->md5Hashes[$filePath]) && is_file($filePath)) {
            if (md5_file($filePath) != $this->md5Hashes[$filePath]) {
                return true;
            }
        }
        return false;
    }

    public function setMD5Hashes(array $md5Hashes): void
    {
        $this->md5Hashes = $md5Hashes;
    }

    public function getMD5Hashes(): array
    {
        return $this->md5Hashes;
    }

    public function setMD5Hash(string $filePath): void
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

    public function getStoragePath(): ?string
    {
        return $this->storagePath;
    }

    public function setStoragePath(?string $storagePath): void
    {
        if ($storagePath !== null) {
            $storagePath = rtrim($storagePath, '/') . '/';
        }
        $this->storagePath = $storagePath;
    }

    public function isRenamed(): bool
    {
        $originalExtensionKey = $this->getOriginalExtensionKey();
        if (!empty($originalExtensionKey) && $originalExtensionKey != $this->getExtensionKey()) {
            $this->renamed = true;
        }
        return $this->renamed;
    }

    public function vendorNameChanged(): bool
    {
        $originalVendorName = $this->getOriginalVendorName();
        if (!empty($originalVendorName) && $originalVendorName != $this->getVendorName()) {
            $this->renamed = true;
        }
        return $this->renamed;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    public function setSupportVersioning(bool $supportVersioning): void
    {
        $this->supportVersioning = $supportVersioning;
    }

    public function getSupportVersioning(): bool
    {
        return $this->supportVersioning;
    }

    public function setDependencies(array $dependencies): void
    {
        $this->dependencies = $dependencies;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function setTargetVersion(float $targetVersion): void
    {
        $this->targetVersion = $targetVersion;
    }

    public function getTargetVersion(): float
    {
        return $this->targetVersion;
    }

    public function getNamespaceName(): string
    {
        return $this->getVendorName() . '\\' . $this->getExtensionName();
    }

    public function setSupportLocalization(bool $supportLocalization): void
    {
        $this->supportLocalization = $supportLocalization;
    }

    public function getSupportLocalization(): bool
    {
        return $this->supportLocalization;
    }

    public function setGenerateDocumentationTemplate(bool $generateDocumentationTemplate): void
    {
        $this->generateDocumentationTemplate = $generateDocumentationTemplate;
    }

    public function getGenerateDocumentationTemplate(): bool
    {
        return $this->generateDocumentationTemplate;
    }

    public function setGenerateEmptyGitRepository(bool $generateEmptyGitRepository): void
    {
        $this->generateEmptyGitRepository = $generateEmptyGitRepository;
    }

    public function getGenerateEmptyGitRepository(): bool
    {
        return $this->generateEmptyGitRepository;
    }

    public function getGenerateEditorConfig(): bool
    {
        return $this->generateEditorConfig;
    }

    public function setGenerateEditorConfig(bool $generateEditorConfig): void
    {
        $this->generateEditorConfig = $generateEditorConfig;
    }

    public function getSourceLanguage(): string
    {
        return $this->sourceLanguage;
    }

    public function setSourceLanguage(string $sourceLanguage): void
    {
        $this->sourceLanguage = $sourceLanguage;
    }

    public function getComposerInfo(): array
    {
        // TODO: consider moving this into the CodeTemplates
        $authors = [];
        foreach ($this->persons as $person) {
            $author = [
                'name' => $person->getName()
            ];
            if ($person->getRole() !== '') {
                $author['role'] = $person->getRole();
            }
            $authors[] = $author;
        }

        $extensionKey = $this->extensionKey;
        $composerExtensionKey = strtolower(str_replace('_', '-', $extensionKey));
        $info = [
            'name' => strtolower(str_replace('_', '', GeneralUtility::camelCaseToLowerCaseUnderscored($this->vendorName))) . '/' . $composerExtensionKey,
            'type' => 'typo3-cms-extension',
            'description' => $this->description,
            'authors' => $authors,
            'license' => 'GPL-2.0-or-later',
            'require' => [
                'typo3/cms-core' => '^12.4'
            ],
            'autoload' => [
                'psr-4' => [
                    $this->getNamespaceName() . '\\' => 'Classes'
                ]
            ],
            'replace' => [
                'typo3-ter/' . $composerExtensionKey => 'self.version'
            ],
            'config' => [
                'vendor-dir' => '.Build/vendor',
                'bin-dir' => '.Build/bin',
            ],
            'scripts' => [
                'post-autoload-dump' => [
                    'TYPO3\\TestingFramework\\Composer\\ExtensionTestEnvironment::prepare',
                ]
            ],
            'extra' => [
                'typo3/cms' => [
                    'web-dir' => '.Build/public',
                    'extension-key' => $extensionKey,
                ]
            ]
        ];
        return $info;
    }
}
