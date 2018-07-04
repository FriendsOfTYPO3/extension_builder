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
use EBT\ExtensionBuilder\Domain\Model\BackendModule;
use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Domain\Model\Person;
use EBT\ExtensionBuilder\Domain\Model\Plugin;
use EBT\ExtensionBuilder\Service\ObjectSchemaBuilder;
use EBT\ExtensionBuilder\Utility\Tools;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Builds an extension object based on the buildConfiguration
 */
class ExtensionSchemaBuilder implements SingletonInterface
{
    /**
     * @var \EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager
     */
    protected $configurationManager = null;

    /**
     * @param \EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ExtensionBuilderConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @var \EBT\ExtensionBuilder\Service\ObjectSchemaBuilder
     */
    protected $objectSchemaBuilder;

    /**
     * @param \EBT\ExtensionBuilder\Service\ObjectSchemaBuilder $objectSchemaBuilder
     * @return void
     */
    public function injectObjectSchemaBuilder(ObjectSchemaBuilder $objectSchemaBuilder)
    {
        $this->objectSchemaBuilder = $objectSchemaBuilder;
    }

    /**
     * @param array $extensionBuildConfiguration
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     * @throws \EBT\ExtensionBuilder\Domain\Exception\ExtensionException
     * @throws \Exception
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function build(array $extensionBuildConfiguration)
    {
        /** @var $extension \EBT\ExtensionBuilder\Domain\Model\Extension */
        $extension = GeneralUtility::makeInstance(Extension::class);
        $globalProperties = $extensionBuildConfiguration['properties'];
        if (!is_array($globalProperties)) {
            throw new \Exception('Extension properties not submitted!');
        }

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
                    $classSettings = $this->configurationManager->getExtbaseClassConfiguration($domainObject->getParentClass());
                    if (isset($classSettings['tableName'])) {
                        $tableName = $classSettings['tableName'];
                    } else {
                        // we use the default table name
                        $tableName = Tools::parseTableNameFromClassName($domainObject->getParentClass());
                    }
                    if (!isset($GLOBALS['TCA'][$tableName])) {
                        throw new \Exception('Table definitions for table ' . $tableName . ' could not be loaded. You can only map to tables with existing TCA or extend classes of installed extensions!');
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
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     * @throws \Exception
     */
    protected function setExtensionRelations($extensionBuildConfiguration, &$extension)
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
                    throw new \Exception('A wire has always to connect a relation with a model, not with another relation');
                }
            }
            $srcModuleId = $wire['src']['moduleId'];
            $relationId = substr($wire['src']['terminal'], 13); // strip "relationWire_"
            $relationJsonConfiguration = $extensionBuildConfiguration['modules'][$srcModuleId]['value']['relationGroup']['relations'][$relationId];

            if (!is_array($relationJsonConfiguration)) {
                $errorMessage = 'Missing relation config in domain object: ' . $extensionBuildConfiguration['modules'][$srcModuleId]['value']['name'];
                throw new \Exception($errorMessage);
            }

            $foreignModelName = $extensionBuildConfiguration['modules'][$wire['tgt']['moduleId']]['value']['name'];
            $localModelName = $extensionBuildConfiguration['modules'][$wire['src']['moduleId']]['value']['name'];

            if (!isset($existingRelations[$localModelName])) {
                $existingRelations[$localModelName] = [];
            }
            $domainObject = $extension->getDomainObjectByName($localModelName);
            $relation = $domainObject->getPropertyByName($relationJsonConfiguration['relationName']);
            if (!$relation) {
                throw new \Exception('Relation not found: ' . $localModelName . '->' . $relationJsonConfiguration['relationName']);
            }
            // get unique foreign key names for multiple relations to the same foreign class
            if (in_array($foreignModelName, $existingRelations[$localModelName])) {
                if (is_a($relation, '\EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation')) {
                    $relation->setForeignKeyName(strtolower($localModelName) . count($existingRelations[$localModelName]));
                }
                if (is_a($relation, '\EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AnyToManyRelation')) {
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

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     * @param array $propertyConfiguration
     * @return void
     */
    protected function setExtensionProperties(&$extension, $propertyConfiguration)
    {
        // name
        $extension->setName(trim($propertyConfiguration['name']));
        // description
        $extension->setDescription($propertyConfiguration['description']);
        // extensionKey
        $extension->setExtensionKey(trim($propertyConfiguration['extensionKey']));
        // vendorName
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

        if (!empty($propertyConfiguration['emConf']['skipGenerateDocumentationTemplate'])) {
            $extension->setGenerateDocumentationTemplate(false);
        }

        // various extension properties
        $extension->setVersion($propertyConfiguration['emConf']['version']);

        if (!empty($propertyConfiguration['emConf']['dependsOn'])) {
            $dependencies = [];
            $lines = GeneralUtility::trimExplode(LF, $propertyConfiguration['emConf']['dependsOn']);
            foreach ($lines as $line) {
                if (strpos($line, '=>')) {
                    list($extensionKey, $version) = GeneralUtility::trimExplode('=>', $line);
                    $dependencies[$extensionKey] = $version;
                }
            }
            $extension->setDependencies($dependencies);
        }

        if (!empty($propertyConfiguration['emConf']['targetVersion'])) {
            $extension->setTargetVersion(floatval($propertyConfiguration['emConf']['targetVersion']));
        }

        if (!empty($propertyConfiguration['emConf']['custom_category'])) {
            $category = $propertyConfiguration['emConf']['custom_category'];
        } else {
            $category = $propertyConfiguration['emConf']['category'];
        }

        $extension->setCategory($category);

        // state
        $state = 0;
        switch ($propertyConfiguration['emConf']['state']) {
            case 'alpha':
                $state = Extension::STATE_ALPHA;
                break;
            case 'beta':
                $state = Extension::STATE_BETA;
                break;
            case 'stable':
                $state = Extension::STATE_STABLE;
                break;
            case 'experimental':
                $state = Extension::STATE_EXPERIMENTAL;
                break;
            case 'test':
                $state = Extension::STATE_TEST;
                break;
        }
        $extension->setState($state);

        if (!empty($propertyConfiguration['originalExtensionKey'])) {
            // handle renaming of extensions
            // original extensionKey
            $extension->setOriginalExtensionKey($propertyConfiguration['originalExtensionKey']);
        }

        if (!empty($propertyConfiguration['originalExtensionKey']) && $extension->getOriginalExtensionKey() != $extension->getExtensionKey()) {
            $settings = $this->configurationManager->getExtensionSettings($extension->getOriginalExtensionKey());
            // if an extension was renamed, a new extension dir is created and we
            // have to copy the old settings file to the new extension dir
            copy($this->configurationManager->getSettingsFile($extension->getOriginalExtensionKey()), $this->configurationManager->getSettingsFile($extension->getExtensionKey()));
        } else {
            $settings = $this->configurationManager->getExtensionSettings($extension->getExtensionKey());
        }

        if (!empty($settings)) {
            $extension->setSettings($settings);
        }
    }

    /**
     * @param array $personValues
     * @return \EBT\ExtensionBuilder\Domain\Model\Person
     */
    protected function buildPerson($personValues)
    {
        $person = GeneralUtility::makeInstance(Person::class);
        $person->setName($personValues['name']);
        $person->setRole($personValues['role']);
        $person->setEmail($personValues['email']);
        $person->setCompany($personValues['company']);
        return $person;
    }

    /**
     * @param array $pluginValues
     * @return \EBT\ExtensionBuilder\Domain\Model\Plugin
     */
    protected function buildPlugin($pluginValues)
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
                list($controllerName, $actionNames) = GeneralUtility::trimExplode('=>', $line);
                if (!empty($actionNames)) {
                    $controllerActionCombinations[$controllerName] = GeneralUtility::trimExplode(',', $actionNames);
                }
            }
            $plugin->setControllerActionCombinations($controllerActionCombinations);
        }
        if (!empty($pluginValues['actions']['noncacheableActions'])) {
            $noncacheableControllerActions = [];
            $lines = GeneralUtility::trimExplode(LF, $pluginValues['actions']['noncacheableActions'], true);
            foreach ($lines as $line) {
                list($controllerName, $actionNames) = GeneralUtility::trimExplode('=>', $line);
                if (!empty($actionNames)) {
                    $noncacheableControllerActions[$controllerName] = GeneralUtility::trimExplode(',', $actionNames);
                }
            }
            $plugin->setNoncacheableControllerActions($noncacheableControllerActions);
        }
        if (!empty($pluginValues['actions']['switchableActions'])) {
            $switchableControllerActions = [];
            $lines = GeneralUtility::trimExplode(LF, $pluginValues['actions']['switchableActions'], true);
            $switchableAction = [];
            foreach ($lines as $line) {
                if (strpos($line, '->') === false) {
                    if (isset($switchableAction['label'])) {
                        // start a new array
                        $switchableAction = [];
                    }
                    $switchableAction['label'] = trim($line);
                } else {
                    $switchableAction['actions'] = GeneralUtility::trimExplode(';', $line, true);
                    $switchableControllerActions[] = $switchableAction;
                }
            }
            $plugin->setSwitchableControllerActions($switchableControllerActions);
        }
        return $plugin;
    }

    /**
     * @param array $backendModuleValues
     * @return \EBT\ExtensionBuilder\Domain\Model\BackendModule
     */
    protected function buildBackendModule($backendModuleValues)
    {
        $backendModule = GeneralUtility::makeInstance(BackendModule::class);
        $backendModule->setName($backendModuleValues['name']);
        $backendModule->setMainModule($backendModuleValues['mainModule']);
        $backendModule->setTabLabel($backendModuleValues['tabLabel']);
        $backendModule->setKey($backendModuleValues['key']);
        $backendModule->setDescription($backendModuleValues['description']);
        if (!empty($backendModuleValues['actions']['controllerActionCombinations'])) {
            $controllerActionCombinations = [];
            $lines = GeneralUtility::trimExplode(LF, $backendModuleValues['actions']['controllerActionCombinations'], true);
            foreach ($lines as $line) {
                list($controllerName, $actionNames) = GeneralUtility::trimExplode('=>', $line);
                $controllerActionCombinations[$controllerName] = GeneralUtility::trimExplode(',', $actionNames);
            }
            $backendModule->setControllerActionCombinations($controllerActionCombinations);
        }
        return $backendModule;
    }
}
