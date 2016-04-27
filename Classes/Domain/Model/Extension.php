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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Schema for a whole extension
 */
class Extension
{
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
    protected $settings = array();
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
    const STATE_ALPHA = 0;
    const STATE_BETA = 1;
    const STATE_STABLE = 2;
    const STATE_EXPERIMENTAL = 3;
    const STATE_TEST = 4;
    /**
     * Is an upload folder required for this extension
     *
     * @var bool
     */
    protected $needsUploadFolder = false;
    /**
     *
     * an array keeping all md5 hashes of all files in the extension to detect modifications
     *
     * @var string[]
     */
    protected $md5Hashes = array();
    /**
     * all domain objects
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject[]
     */
    protected $domainObjects = array();
    /**
     * the Persons working on the Extension
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\Person[]
     */
    protected $persons = array();
    /**
     * plugins
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\Plugin[]
     */
    private $plugins;
    /**
     * backend modules
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\BackendModule[]
     */
    private $backendModules;
    /**
     * was the extension renamed?
     * @var bool
     */
    private $renamed = false;
    /**
     * @var array
     */
    private $dependencies = array();
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
     *
     * @return string
     */
    public function getExtensionKey()
    {
        return $this->extensionKey;
    }

    /**
     * @return string
     */
    public function getExtensionName()
    {
        return GeneralUtility::underscoredToUpperCamelCase($this->extensionKey);
    }

    /**
     *
     * @param string $extensionKey
     */
    public function setExtensionKey($extensionKey)
    {
        $this->extensionKey = $extensionKey;
    }

    /**
     *
     * @return string
     */
    public function getOriginalExtensionKey()
    {
        return $this->originalExtensionKey;
    }

    /**
     *
     * @param string $extensionKey
     */
    public function setOriginalExtensionKey($extensionKey)
    {
        $this->originalExtensionKey = $extensionKey;
    }

    /**
     *
     * @param array $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getExtensionDir()
    {
        if (empty($this->extensionDir)) {
            if (empty($this->extensionKey)) {
                throw new \Exception('ExtensionDir can only be created if an extensionKey is defined first');
            }
            $this->extensionDir = PATH_typo3conf . 'ext/' . $this->extensionKey . '/';
        }
        return $this->extensionDir;
    }

    /**
     *
     * @param string $extensionDir
     */
    public function setExtensionDir($extensionDir)
    {
        $this->extensionDir = $extensionDir;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $vendorName
     */
    public function setVendorName($vendorName)
    {
        $this->vendorName = $vendorName;
    }

    /**
     * @return string
     */
    public function getVendorName()
    {
        return $this->vendorName;
    }

    /**
     *
     * @return string
     */
    public function getOriginalVendorName()
    {
        return $this->originalVendorName;
    }

    /**
     *
     * @param string $vendorName
     */
    public function setOriginalVendorName($vendorName)
    {
        $this->originalVendorName = $vendorName;
    }

    /**
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     *
     * @param int $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject[]
     */
    public function getDomainObjects()
    {
        return $this->domainObjects;
    }

    /**
     * An array of domain objects for which a controller should be built.
     * Retruns ttrue if there are any actions defined for these domain objects
     *
     * @return array
     */
    public function getDomainObjectsForWhichAControllerShouldBeBuilt()
    {
        $domainObjects = array();
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
    public function getDomainObjectsThatNeedMappingStatements()
    {
        $domainObjectsThatNeedMappingStatements = array();
        foreach ($this->domainObjects as $domainObject) {
            if ($domainObject->getNeedsMappingStatement()) {
                $domainObjectsThatNeedMappingStatements[] = $domainObject;
            }
        }
        if (!empty($domainObjectsThatNeedMappingStatements)) {
            return $domainObjectsThatNeedMappingStatements;
        } else {
            return null;
        }
    }

    /**
     * return tables that need a type field to enable
     * single table inheritance or mapping to an existing table
     */
    public function getTablesForTypeFieldDefinitions()
    {
        $tableNames = array();
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
    public function getClassHierarchy()
    {
        $classHierarchy = array();
        foreach ($this->domainObjects as $domainObject) {
            if ($domainObject->isSubclass()) {
                $parentClass = $domainObject->getParentClass();
                if (strpos($parentClass, '\\') === 0) {
                    $parentClass = substr($parentClass, 1);
                }
                if (!is_array($classHierarchy[$parentClass])) {
                    $classHierarchy[$parentClass] = array();
                }
                $classHierarchy[$parentClass][] = $domainObject;
            }
        }
        if (!empty($classHierarchy)) {
            return $classHierarchy;
        } else {
            return null;
        }
    }

    /**
     * needed to get the right order for models
     * extending other models parents have to be ordered before their children
     */
    public function getDomainObjectsInHierarchicalOrder()
    {
        $domainObjects = $this->getDomainObjects();
        $sortByParent = function (DomainObject $domainObject1, DomainObject $domainObject2) {
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
    protected function isParentOf($domainObject1, $domainObject2, $classHierarchy)
    {
        if ($domainObject2->getParentClass() === $domainObject1->getFullQualifiedClassName()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add a domain object to the extension. Creates the reverse link as well.
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     */
    public function addDomainObject(\EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject)
    {
        $domainObject->setExtension($this);
        foreach (array_keys($this->domainObjects) as $existingDomainObjectName) {
            if (strtolower($domainObject->getName()) == strtolower($existingDomainObjectName)) {
                throw new \EBT\ExtensionBuilder\Domain\Exception\ExtensionException('Duplicate domain object name "' . $domainObject->getName() . '".', \EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator::ERROR_DOMAINOBJECT_DUPLICATE);
            }
        }
        if ($domainObject->getNeedsUploadFolder()) {
            $this->needsUploadFolder = true;
        }
        $this->domainObjects[$domainObject->getName()] = $domainObject;
    }

    /**
     *
     * @param string $domainObjectName
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject
     */
    public function getDomainObjectByName($domainObjectName)
    {
        if (isset($this->domainObjects[$domainObjectName])) {
            return $this->domainObjects[$domainObjectName];
        }
        return null;
    }

    /**
     * returns the extension key a prefix tx_  and without underscore
     */
    public function getShortExtensionKey()
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
    public function getPersons()
    {
        return $this->persons;
    }

    /**
     * Sets the Persons
     *
     * @param array <\EBT\ExtensionBuilder\Domain\Model\Person> $persons
     * @return void
     */
    public function setPersons($persons)
    {
        $this->persons = $persons;
    }

    /**
     * Adds a Person to the end of the current Set of Persons.
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\Person $person
     * @return void
     */
    public function addPerson($person)
    {
        $this->persons[] = $person;
    }

    /**
     * Setter for plugin
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\EBT\ExtensionBuilder\Domain\Model\Plugin> $plugins
     * @return void
     */
    public function setPlugins(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $plugins)
    {
        $this->plugins = $plugins;
    }

    /**
     * Getter for $plugin
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\EBT\ExtensionBuilder\Domain\Model\Plugin>
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     *
     * @return bool
     */
    public function hasPlugins()
    {
        if (count($this->plugins) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add $plugin
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\Plugin
     * @return void
     */
    public function addPlugin(\EBT\ExtensionBuilder\Domain\Model\Plugin $plugin)
    {
        $this->plugins[] = $plugin;
    }

    /**
     * Setter for backendModule
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\EBT\ExtensionBuilder\Domain\Model\BackendModule> $backendModules
     * @return void
     */
    public function setBackendModules(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $backendModules)
    {
        $this->backendModules = $backendModules;
    }

    /**
     * Getter for $backendModule
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\EBT\ExtensionBuilder\Domain\Model\Plugin>
     */
    public function getBackendModules()
    {
        return $this->backendModules;
    }

    /**
     * Add $backendModule
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\BackendModule
     * @return void
     */
    public function addBackendModule(\EBT\ExtensionBuilder\Domain\Model\BackendModule $backendModule)
    {
        $this->backendModules[] = $backendModule;
    }

    /**
     *
     * @return bool
     */
    public function hasBackendModules()
    {
        if (count($this->backendModules) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getReadableState()
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

    public function getCssClassName()
    {
        return 'tx-' . str_replace('_', '-', $this->getExtensionKey());
    }

    public function isModified($filePath)
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
     * @return void
     */
    public function setMD5Hashes($md5Hashes)
    {
        $this->md5Hashes = $md5Hashes;
    }

    /**
     * getter for md5 hashes
     * @return array $md5Hashes
     */
    public function getMD5Hashes()
    {
        return $this->md5Hashes;
    }

    /**
     * calculates all md5 hashes
     *
     * @param string $filePath
     */
    public function setMD5Hash($filePath)
    {
        $this->md5Hashes[$filePath] = md5_file($filePath);
    }

    /**
     * Get the previous extension directory
     * if the extension was renamed it is different from $this->extensionDir
     *
     * @return string
     */
    public function getPreviousExtensionDirectory()
    {
        if ($this->isRenamed()) {
            $originalExtensionKey = $this->getOriginalExtensionKey();
            $this->previousExtensionDirectory = PATH_typo3conf . 'ext/' . $originalExtensionKey . '/';
            $this->previousExtensionKey = $originalExtensionKey;
            return $this->previousExtensionDirectory;
        } else {
            return $this->extensionDir;
        }
    }

    /**
     *
     * @return bool
     */
    public function isRenamed()
    {
        $originalExtensionKey = $this->getOriginalExtensionKey();
        if (!empty($originalExtensionKey) && $originalExtensionKey != $this->getExtensionKey()) {
            $this->renamed = true;
        }
        return $this->renamed;
    }

    /**
     *
     * @return bool
     */
    public function vendorNameChanged()
    {
        $originalVendorName = $this->getOriginalVendorName();
        if (!empty($originalVendorName) && $originalVendorName != $this->getVendorName()) {
            $this->renamed = true;
        }
        return $this->renamed;
    }

    /**
     * Getter for $needsUploadFolder
     *
     * @return bool $needsUploadFolder
     */
    public function getNeedsUploadFolder()
    {
        if ($this->needsUploadFolder) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     *
     * @return string $uploadFolder
     */
    public function getUploadFolder()
    {
        return 'uploads/' . $this->getShortExtensionKey();
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @param bool $supportVersioning
     */
    public function setSupportVersioning($supportVersioning)
    {
        $this->supportVersioning = $supportVersioning;
    }

    /**
     * @return bool
     */
    public function getSupportVersioning()
    {
        return $this->supportVersioning;
    }

    /**
     * @param array $dependencies
     */
    public function setDependencies($dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @param float $targetVersion
     */
    public function setTargetVersion($targetVersion)
    {
        $this->targetVersion = $targetVersion;
    }

    /**
     * @return float
     */
    public function getTargetVersion()
    {
        return $this->targetVersion;
    }

    /**
     * @return string
     */
    public function getNamespaceName()
    {
        return $this->getVendorName() . '\\' . $this->getExtensionName();
    }

    /**
     * @param bool $supportLocalization
     */
    public function setSupportLocalization($supportLocalization)
    {
        $this->supportLocalization = $supportLocalization;
    }

    /**
     * @return bool
     */
    public function getSupportLocalization()
    {
        return $this->supportLocalization;
    }

    /**
     * @param bool $generateDocumentationTemplate
     */
    public function setGenerateDocumentationTemplate($generateDocumentationTemplate)
    {
        $this->generateDocumentationTemplate = $generateDocumentationTemplate;
    }

    /**
     * @return bool
     */
    public function getGenerateDocumentationTemplate()
    {
        return $this->generateDocumentationTemplate;
    }

    /**
     * @return string
     */
    public function getSourceLanguage()
    {
        return $this->sourceLanguage;
    }

    /**
     * @param string $sourceLanguage
     */
    public function setSourceLanguage($sourceLanguage)
    {
        $this->sourceLanguage = $sourceLanguage;
    }
}
