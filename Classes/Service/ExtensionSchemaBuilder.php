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
use EBT\ExtensionBuilder\Domain\Exception\ExtensionException;
use EBT\ExtensionBuilder\Domain\Model\BackendModule;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AnyToManyRelation;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation;
use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Domain\Model\Person;
use EBT\ExtensionBuilder\Domain\Model\Plugin;
use EBT\ExtensionBuilder\Utility\Tools;
use Exception;
use RuntimeException;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Builds an extension object based on the buildConfiguration
 */
class ExtensionSchemaBuilder implements SingletonInterface
{
    /**
     * @var ExtensionBuilderConfigurationManager
     */
    protected $configurationManager;

    /**
     * @var ObjectSchemaBuilder
     */
    protected $objectSchemaBuilder;

    public function injectConfigurationManager(ExtensionBuilderConfigurationManager $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
    }

    public function injectObjectSchemaBuilder(ObjectSchemaBuilder $objectSchemaBuilder): void
    {
        $this->objectSchemaBuilder = $objectSchemaBuilder;
    }

    /**
     * @param array $extensionBuildConfiguration
     *
     * @return Extension $extension
     * @throws ExtensionException
     * @throws Exception
     */
    public function build(array $extensionBuildConfiguration): Extension
    {
        /** @var $extension Extension */
        $extension = GeneralUtility::makeInstance(Extension::class);
        $globalProperties = $extensionBuildConfiguration['properties'];
        if (!is_array($globalProperties)) {
            throw new Exception('Extension properties not submitted!');
        }

        $extension->setStoragePath($extensionBuildConfiguration['storagePath'] ?? null);

        $this->setExtensionProperties($extension, $globalProperties);

        if (is_array($globalProperties['persons'])) {
            foreach ($globalProperties['persons'] as $personValues) {
                $person = $this->buildPerson($personValues);
                $extension->addPerson($person);
            }
        }

        if (is_array($globalProperties['plugins'])) {
            foreach ($globalProperties['plugins'] as $pluginValues) {
                $plugin = $this->buildPlugin($pluginValues);
                $extension->addPlugin($plugin);
            }
        }

        if (is_array($globalProperties['backendModules'])) {
            foreach ($globalProperties['backendModules'] as $backendModuleValues) {
                $backendModule = $this->buildBackendModule($backendModuleValues);
                $extension->addBackendModule($backendModule);
            }
        }

        // classes
        if (is_array($extensionBuildConfiguration['modules'])) {
            foreach ($extensionBuildConfiguration['modules'] as $singleModule) {
                $domainObject = $this->objectSchemaBuilder->build($singleModule['value']);
                if ($domainObject->isSubClass() && !$domainObject->isMappedToExistingTable()) {
                    // we try to get the table from Extbase configuration
                    $table = $this->configurationManager->getPersistenceTable($domainObject->getParentClass());
                    if ($table) {
                        $tableName = $table;
                    } else {
                        // we use the default table name
                        $tableName = Tools::parseTableNameFromClassName($domainObject->getParentClass());
                    }
                    if (!isset($GLOBALS['TCA'][$tableName])) {
                        throw new Exception('Table definitions for table ' . $tableName . ' could not be loaded. You can only map to tables with existing TCA or extend classes of installed extensions!');
                    }
                    $domainObject->setMapToTable($tableName);
                }
                $extension->addDomainObject($domainObject);
            }
            // add child objects - needed to generate correct TCA for inheritance
            foreach ($extension->getDomainObjects() as $domainObject1) {
                foreach ($extension->getDomainObjects() as $domainObject2) {
                    if ($domainObject2->getParentClass() === $domainObject1->getFullQualifiedClassName()) {
                        $domainObject1->addChildObject($domainObject2);
                    }
                }
            }
        }

        // relations
        if (is_array($extensionBuildConfiguration['wires'])) {
            $this->setExtensionRelations($extensionBuildConfiguration, $extension);
        }

        return $extension;
    }

    /**
     * @param array $extensionBuildConfiguration
     * @param Extension $extension
     * @throws Exception
     */
    protected function setExtensionRelations(array $extensionBuildConfiguration, Extension $extension): void
    {
        $existingRelations = [];
        foreach ($extensionBuildConfiguration['wires'] as $wire) {
            if ($wire['tgt']['terminal'] !== 'SOURCES') {
                if ($wire['src']['terminal'] == 'SOURCES') {
                    // this happens if a relation wire was drawn from child to parent
                    // swap the two arrays
                    $tgtModuleId = $wire['src']['moduleId'];
                    $wire['src'] = $wire['tgt'];
                    $wire['tgt'] = ['moduleId' => $tgtModuleId, 'terminal' => 'SOURCES'];
                } else {
                    throw new Exception('A wire has always to connect a relation with a model, not with another relation');
                }
            }
            $srcModuleId = $wire['src']['moduleId'];
            $relationId = substr($wire['src']['terminal'], 13); // strip "relationWire_"
            $relationJsonConfiguration = $extensionBuildConfiguration['modules'][$srcModuleId]['value']['relationGroup']['relations'][$relationId];

            if (!is_array($relationJsonConfiguration)) {
                $errorMessage = 'Missing relation config in domain object: ' . $extensionBuildConfiguration['modules'][$srcModuleId]['value']['name'];
                throw new Exception($errorMessage);
            }

            $foreignModelName = $extensionBuildConfiguration['modules'][$wire['tgt']['moduleId']]['value']['name'];
            $localModelName = $extensionBuildConfiguration['modules'][$wire['src']['moduleId']]['value']['name'];

            if (!isset($existingRelations[$localModelName])) {
                $existingRelations[$localModelName] = [];
            }
            $domainObject = $extension->getDomainObjectByName($localModelName);
            $relation = $domainObject->getPropertyByName($relationJsonConfiguration['relationName']);
            if (!$relation) {
                throw new Exception('Relation not found: ' . $localModelName . '->' . $relationJsonConfiguration['relationName']);
            }
            // get unique foreign key names for multiple relations to the same foreign class
            if (in_array($foreignModelName, $existingRelations[$localModelName])) {
                if (is_a($relation, ZeroToManyRelation::class)) {
                    $relation->setForeignKeyName(strtolower($localModelName) . count($existingRelations[$localModelName]));
                }
                if (is_a($relation, AnyToManyRelation::class)) {
                    $relation->setUseExtendedRelationTableName(true);
                }
            }
            $existingRelations[$localModelName][] = $foreignModelName;

            if (!empty($relationJsonConfiguration['renderType'])) {
                $relation->setRenderType($relationJsonConfiguration['renderType']);
            }

            $relation->setForeignModel($extension->getDomainObjectByName($foreignModelName));
        }
    }

    protected function setExtensionProperties(Extension $extension, array $propertyConfiguration): void
    {
        $extension->setName(trim($propertyConfiguration['name']));
        $extension->setDescription($propertyConfiguration['description']);
        $extension->setExtensionKey(trim($propertyConfiguration['extensionKey']));
        $extension->setVendorName(trim($propertyConfiguration['vendorName']));

        if (!empty($propertyConfiguration['emConf']['sourceLanguage'])) {
            $extension->setSourceLanguage($propertyConfiguration['emConf']['sourceLanguage']);
        }

        if ($propertyConfiguration['emConf']['disableVersioning']) {
            $extension->setSupportVersioning(false);
        }

        if ($propertyConfiguration['emConf']['disableLocalization']) {
            $extension->setSupportLocalization(false);
        }

        if (empty($propertyConfiguration['emConf']['generateDocumentationTemplate'])) {
            $extension->setGenerateDocumentationTemplate(false);
        }

        // various extension properties
        $extension->setVersion($propertyConfiguration['emConf']['version']);

        if (!empty($propertyConfiguration['emConf']['dependsOn'])) {
            $dependencies = [];
            $lines = GeneralUtility::trimExplode(LF, $propertyConfiguration['emConf']['dependsOn']);
            foreach ($lines as $line) {
                if (strpos($line, '=>')) {
                    [$extensionKey, $version] = GeneralUtility::trimExplode('=>', $line);
                    $dependencies[$extensionKey] = $version;
                }
            }
            $extension->setDependencies($dependencies);
        }

        if (!empty($propertyConfiguration['emConf']['targetVersion'])) {
            $extension->setTargetVersion((float)$propertyConfiguration['emConf']['targetVersion']);
        }

        if (!empty($propertyConfiguration['emConf']['custom_category'])) {
            $category = $propertyConfiguration['emConf']['custom_category'];
        } else {
            $category = $propertyConfiguration['emConf']['category'];
        }

        $extension->setCategory($category);
        $extension->setState($this->getStateByName($propertyConfiguration['emConf']['state']));

        if (!empty($propertyConfiguration['originalExtensionKey'])) {
            // handle renaming of extensions
            // original extensionKey
            $extension->setOriginalExtensionKey($propertyConfiguration['originalExtensionKey']);
        }

        if (!empty($propertyConfiguration['originalExtensionKey'])
            && $extension->getOriginalExtensionKey() != $extension->getExtensionKey()
        ) {
            $settings = $this->configurationManager->getExtensionSettings($extension->getOriginalExtensionKey(), $extension->getStoragePath());
            // if an extension was renamed, a new extension dir is created and we
            // have to copy the old settings file to the new extension dir
            $source = $this->configurationManager->getSettingsFile($extension->getOriginalExtensionKey(), $extension->getStoragePath());
            $target = $this->configurationManager->getSettingsFile($extension->getExtensionKey(), $extension->getStoragePath());
            $pathInfo = pathinfo($target);
            if (!is_dir($pathInfo['dirname'])) {
                if (!mkdir($concurrentDirectory = $pathInfo['dirname'], 0775, true) && !is_dir($concurrentDirectory)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                }
            }
            copy($source, $target);
        } else {
            $settings = $this->configurationManager->getExtensionSettings($extension->getExtensionKey(), $extension->getStoragePath());
        }

        if (!empty($settings)) {
            $extension->setSettings($settings);
        }
    }

    protected function getStateByName(string $stateKey): int
    {
        switch ($stateKey) {
            case 'alpha':
                return Extension::STATE_ALPHA;
            case 'beta':
                return Extension::STATE_BETA;
            case 'stable':
                return Extension::STATE_STABLE;
            case 'experimental':
                return Extension::STATE_EXPERIMENTAL;
            case 'test':
                return Extension::STATE_TEST;
        }
        return Extension::STATE_ALPHA;
    }

    protected function buildPerson(array $personValues): Person
    {
        $person = GeneralUtility::makeInstance(Person::class);
        $person->setName($personValues['name']);
        $person->setRole($personValues['role']);
        $person->setEmail($personValues['email']);
        $person->setCompany($personValues['company']);
        return $person;
    }

    protected function buildPlugin(array $pluginValues): Plugin
    {
        $plugin = GeneralUtility::makeInstance(Plugin::class);
        $plugin->setName($pluginValues['name']);
        $plugin->setDescription($pluginValues['description']);
        $plugin->setType($pluginValues['type']);
        $plugin->setKey($pluginValues['key']);
        if (!empty($pluginValues['actions']['controllerActionCombinations'])) {
            $controllerActionCombinations = [];
            $lines = GeneralUtility::trimExplode(LF, $pluginValues['actions']['controllerActionCombinations'], true);
            foreach ($lines as $line) {
                [$controllerName, $actionNames] = GeneralUtility::trimExplode('=>', $line);
                if (!empty($actionNames)) {
                    $controllerActionCombinations[$controllerName] = GeneralUtility::trimExplode(',', $actionNames);
                }
            }
            $plugin->setControllerActionCombinations($controllerActionCombinations);
        }
        if (!empty($pluginValues['actions']['noncacheableActions'])) {
            $nonCacheableControllerActions = [];
            $lines = GeneralUtility::trimExplode(LF, $pluginValues['actions']['noncacheableActions'], true);
            foreach ($lines as $line) {
                [$controllerName, $actionNames] = GeneralUtility::trimExplode('=>', $line);
                if (!empty($actionNames)) {
                    $nonCacheableControllerActions[$controllerName] = GeneralUtility::trimExplode(',', $actionNames);
                }
            }
            $plugin->setNonCacheableControllerActions($nonCacheableControllerActions);
        }
        return $plugin;
    }

    protected function buildBackendModule(array $backendModuleValues): BackendModule
    {
        $backendModule = GeneralUtility::makeInstance(BackendModule::class);
        $backendModule->setName($backendModuleValues['name']);
        $backendModule->setMainModule($backendModuleValues['mainModule']);
        $backendModule->setTabLabel($backendModuleValues['tabLabel']);
        $backendModule->setKey($backendModuleValues['key']);
        $backendModule->setDescription($backendModuleValues['description']);
        if (!empty($backendModuleValues['actions']['controllerActionCombinations'])) {
            $controllerActionCombinations = [];
            $lines = GeneralUtility::trimExplode(
                LF,
                $backendModuleValues['actions']['controllerActionCombinations'],
                true
            );
            foreach ($lines as $line) {
                [$controllerName, $actionNames] = GeneralUtility::trimExplode('=>', $line);
                $controllerActionCombinations[$controllerName] = GeneralUtility::trimExplode(',', $actionNames);
            }
            $backendModule->setControllerActionCombinations($controllerActionCombinations);
        }
        return $backendModule;
    }
}
