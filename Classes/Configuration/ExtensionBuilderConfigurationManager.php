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

namespace EBT\ExtensionBuilder\Configuration;

use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Utility\SpycYAMLParser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RectorPrefix202306\Tracy\Debugger;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Load settings from yaml file and from TYPO3_CONF_VARS extConf
 */
class ExtensionBuilderConfigurationManager implements SingletonInterface
{
    /**
     * @var string
     */
    const SETTINGS_DIR = 'Configuration/ExtensionBuilder/';
    /**
     * @var string
     */
    const EXTENSION_BUILDER_SETTINGS_FILE = 'ExtensionBuilder.json';
    /**
     * @var string
     */
    const DEFAULT_TEMPLATE_ROOTPATH = 'EXT:extension_builder/Resources/Private/CodeTemplates/Extbase/';
    private array $inputData = [];

    protected ConfigurationManagerInterface $configurationManager;

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Parse the request data from the input stream.
     *
     * @return void
     */
    public function parseRequest(): void
    {
        $jsonString = file_get_contents('php://input');
        $this->inputData = json_decode($jsonString, true);
    }

    /**
     * @return mixed
     */
    public function getParamsFromRequest()
    {
        return $this->inputData['params'];
    }

    /**
     * Reads the configuration from this->inputData and returns it as array.
     *
     * @return array
     * @throws \Exception
     */
    public function getConfigurationFromModeler(): array
    {
        if (empty($this->inputData)) {
            throw new \Exception('No inputData!');
        }
        $extensionConfigurationJson = json_decode($this->inputData['params']['working'], true);
        $extensionConfigurationJson = $this->reArrangeRelations($extensionConfigurationJson);
        $extensionConfigurationJson['modules'] = $this->checkForAbsoluteClassNames($extensionConfigurationJson['modules']);
        return $extensionConfigurationJson;
    }

    public function getSubActionFromRequest(): string
    {
        return $this->inputData['method'];
    }

    /**
     * Set settings from various sources:
     *
     * - Settings configured in module.extension_builder typoscript
     * - Module settings configured in the extension manager
     *
     * @param array|null $typoscript
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function getSettings(?array $typoscript = null): array
    {
        if ($typoscript === null) {
            $typoscript = $this->configurationManager->getConfiguration($this->configurationManager::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        }
        $settings = $typoscript['module.']['extension_builder.']['settings.'] ?? [];
        $settings['extConf'] = $this->getExtensionBuilderSettings();
        if (empty($settings['publicResourcesPath'])) {
            $settings['publicResourcesPath'] = ExtensionManagementUtility::extPath('extension_builder') . 'Resources/Public/';
            $settings['codeTemplateRootPaths'][] = ExtensionManagementUtility::extPath('extension_builder') . 'Resources/Private/CodeTemplates/Extbase/';
            $settings['codeTemplatePartialPaths'][] = ExtensionManagementUtility::extPath('extension_builder') . 'Resources/Private/CodeTemplates/Extbase/Partials/';
        }
        return $settings;
    }

    /**
     * Get the extension_builder configuration (ext_template_conf).
     *
     * @return array
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function getExtensionBuilderSettings(): array
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('extension_builder');
    }

    public function getExtensionSettings(string $extensionKey, string $extensionStoragePath): array
    {
        $settingsFile = $this->getSettingsFile($extensionKey, $extensionStoragePath);
        if (!file_exists($settingsFile)) {
            return [];
        }
        return SpycYAMLParser::YAMLLoadString(file_get_contents($settingsFile));
    }

    /**
     * Reads the stored configuration (i.e. the extension model etc.).
     *
     * @param string $extensionKey
     * @param string|null $storagePath
     * @return array|null
     */
    public function getExtensionBuilderConfiguration(string $extensionKey, ?string $storagePath = null): ?array
    {
        $extensionConfigurationJson = self::getExtensionBuilderJson($extensionKey, $storagePath);
        if ($extensionConfigurationJson) {
            $extensionConfigurationJson = $this->fixExtensionBuilderJSON($extensionConfigurationJson);
            $extensionConfigurationJson['properties']['originalExtensionKey'] = $extensionKey;
            $extensionConfigurationJson['properties']['originalVendorName'] = $extensionConfigurationJson['properties']['vendorName'];
            return $extensionConfigurationJson;
        }
        return null;
    }

    public static function getExtensionBuilderJson(string $extensionKey, ?string $storagePath = null): ?array
    {
        $storagePath = $storagePath ?? Environment::getPublicPath() . '/typo3conf/ext/';
        $jsonFile = $storagePath . $extensionKey . '/' . self::EXTENSION_BUILDER_SETTINGS_FILE;
        if (file_exists($jsonFile)) {
            return json_decode(file_get_contents($jsonFile), true);
        }

        return null;
    }

    /**
     * @param $className string
     * @return string
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function getPersistenceTable(string $className): string
    {
        return GeneralUtility::makeInstance(DataMapper::class)->getDataMap($className)->getTableName();
    }

    /**
     * Get the file name and path of the settings file.
     *
     * @param string $extensionKey
     * @param string $storagePath
     * @return string path
     */
    public function getSettingsFile(string $extensionKey, string $storagePath): string
    {
        return $storagePath . $extensionKey . '/' . self::SETTINGS_DIR . 'settings.yaml';
    }

    /**
     * @param Extension $extension
     * @param array $codeTemplateRootPaths
     *
     * @throws \Exception
     */
    public function createInitialSettingsFile(Extension $extension, array $codeTemplateRootPaths): void
    {
        GeneralUtility::mkdir_deep($extension->getExtensionDir() . self::SETTINGS_DIR);
        $substitutedExtensionPath = self::substituteExtensionPath($codeTemplateRootPaths[0]);
        $settings = file_get_contents($substitutedExtensionPath . self::SETTINGS_DIR . 'settings.yamlt');
        $settings = str_replace('{extension.extensionKey}', $extension->getExtensionKey(), $settings);
        $settings = str_replace(
            '{f:format.date(format:\'Y-m-d\\TH:i:s\\Z\',date:\'now\')}',
            date('Y-m-d\TH:i:s\Z'),
            $settings
        );
        GeneralUtility::writeFile(
            $extension->getExtensionDir() . self::SETTINGS_DIR . 'settings.yaml',
            $settings
        );
    }

    /**
     * Replace the EXT:extkey prefix with the appropriate path.
     *
     * @param string $encodedTemplateRootPath
     * @return string
     */
    public static function substituteExtensionPath(string $encodedTemplateRootPath): string
    {
        if (str_starts_with($encodedTemplateRootPath, 'EXT:')) {
            [$extKey, $script] = explode('/', substr($encodedTemplateRootPath, 4), 2);
            if ($extKey && ExtensionManagementUtility::isLoaded($extKey)) {
                return ExtensionManagementUtility::extPath($extKey) . $script;
            }
            return '';
        }

        if (PathUtility::isAbsolutePath($encodedTemplateRootPath)) {
            return $encodedTemplateRootPath;
        }

        return Environment::getPublicPath() . '/' . $encodedTemplateRootPath;
    }

    /**
     * Performs various fixes/workarounds for wireit limitations.
     *
     * @param array $extensionConfigurationJson
     * @return array the modified configuration
     */
    public function fixExtensionBuilderJSON(array $extensionConfigurationJson): array
    {
        $extensionConfigurationJson['modules'] = $this->resetOutboundedPositions($extensionConfigurationJson['modules']);
        $extensionConfigurationJson['modules'] = $this->mapAdvancedMode($extensionConfigurationJson['modules']);
        return $this->reArrangeRelations($extensionConfigurationJson);
    }

    /**
     * Copy values from simple mode fieldset to advanced fieldset.
     *
     * Enables compatibility with JSON from older versions of the extension builder.
     *
     * @param array $jsonConfig
     * @param bool $prepareForModeler
     *
     * @return array modified json
     */
    protected function mapAdvancedMode(array $jsonConfig, bool $prepareForModeler = true): array
    {
        $fieldsToMap = [
            'relationType',
            'renderType',
            'propertyIsExcludeField',
            'propertyIsExcludeField',
            'lazyLoading',
            'relationDescription',
            'foreignRelationClass'
        ];
        foreach ($jsonConfig as &$module) {
            for ($i = 0, $relations = count($module['value']['relationGroup']['relations']); $i < $relations; $i++) {
                if ($prepareForModeler) {
                    if (empty($module['value']['relationGroup']['relations'][$i]['advancedSettings'])) {
                        $module['value']['relationGroup']['relations'][$i]['advancedSettings'] = [];
                        foreach ($fieldsToMap as $fieldToMap) {
                            $module['value']['relationGroup']['relations'][$i]['advancedSettings'][$fieldToMap] =
                                $module['value']['relationGroup']['relations'][$i][$fieldToMap];
                        }

                        $module['value']['relationGroup']['relations'][$i]['advancedSettings']['propertyIsExcludeField'] =
                            $module['value']['relationGroup']['relations'][$i]['propertyIsExcludeField'];
                        $module['value']['relationGroup']['relations'][$i]['advancedSettings']['lazyLoading'] =
                            $module['value']['relationGroup']['relations'][$i]['lazyLoading'];
                        $module['value']['relationGroup']['relations'][$i]['advancedSettings']['relationDescription'] =
                            $module['value']['relationGroup']['relations'][$i]['relationDescription'];
                        $module['value']['relationGroup']['relations'][$i]['advancedSettings']['foreignRelationClass'] =
                            $module['value']['relationGroup']['relations'][$i]['foreignRelationClass'];
                    }
                } elseif (isset($module['value']['relationGroup']['relations'][$i]['advancedSettings'])) {
                    foreach ($fieldsToMap as $fieldToMap) {
                        $module['value']['relationGroup']['relations'][$i][$fieldToMap] =
                            $module['value']['relationGroup']['relations'][$i]['advancedSettings'][$fieldToMap];
                    }
                    unset($module['value']['relationGroup']['relations'][$i]['advancedSettings']);
                }
            }
        }
        return $jsonConfig;
    }

    /**
     * Prefixes class names with a backslash to ensure that always fully qualified
     * class names are used.
     *
     * @param $moduleConfig
     * @return mixed
     */
    protected function checkForAbsoluteClassNames($moduleConfig)
    {
        foreach ($moduleConfig as &$module) {
            if (!empty($module['value']['objectsettings']['parentClass'])
                && strpos($module['value']['objectsettings']['parentClass'], '\\') !== 0
            ) {
                // namespaced classes always need a full qualified class name
                $module['value']['objectsettings']['parentClass'] = '\\' . $module['value']['objectsettings']['parentClass'];
            }
        }
        return $moduleConfig;
    }

    /**
     * Check if the confirm was send with input data.
     *
     * @param string $identifier
     * @return bool
     */
    public function isConfirmed(string $identifier): bool
    {
        return isset($this->inputData['params'][$identifier]) && $this->inputData['params'][$identifier] == 1;
    }

    /**
     * Just a temporary workaround until the new UI is available.
     *
     * @param array $jsonConfig
     * @return array
     */
    protected function resetOutboundedPositions(array $jsonConfig): array
    {
        foreach ($jsonConfig as &$module) {
            if ($module['config']['position'][0] < 0) {
                $module['config']['position'][0] = 10;
            }
            if ($module['config']['position'][1] < 0) {
                $module['config']['position'][1] = 10;
            }
        }
        return $jsonConfig;
    }

    /**
     * This is a workaround for the bad design in WireIt. All wire terminals are
     * only identified by a simple index, that does not reflect deleting of models
     * and relations.
     *
     * @param array $jsonConfig
     * @return array
     */
    protected function reArrangeRelations(array $jsonConfig): array
    {
        // TODO check this code. This one removes the terminal array key inside the src array, this mustn't happen
        // foreach ($jsonConfig['wires'] as &$wire) {
        //     // format: relation_1
        //     $parts = explode('_', $wire['src']['terminal']);
        //     $supposedRelationIndex = (int)$parts[1];
//
        //     // Source
        //     $uid = $wire['src']['uid'];
        //     $wire['src'] = $this->findModuleIndexByRelationUid(
        //         $wire['src']['uid'],
        //         $jsonConfig['modules'],
        //         $wire['src']['moduleId'],
        //         $supposedRelationIndex
        //     );
        //     $wire['src']['uid'] = $uid;
//
        //     // Target
        //     $uid = $wire['tgt']['uid'];
        //     $wire['tgt'] = $this->findModuleIndexByRelationUid(
        //         $wire['tgt']['uid'],
        //         $jsonConfig['modules'],
        //         $wire['tgt']['moduleId']
        //     );
        //     $wire['tgt']['uid'] = $uid;
        // }
        return $jsonConfig;
    }

    /**
     * @param string $uid
     * @param array $modules
     * @param int $supposedModuleIndex
     * @param int|null $supposedRelationIndex
     * @return array
     */
    protected function findModuleIndexByRelationUid(
        string $uid,
        array $modules,
        int $supposedModuleIndex,
        ?int $supposedRelationIndex = null
    ): array {
        $result = [
            'moduleId' => $supposedModuleIndex
        ];
        if ($supposedRelationIndex === null) {
            $result['terminal'] = 'SOURCES';
            if ($modules[$supposedModuleIndex]['value']['objectsettings']['uid'] === $uid) {
                // everything as expected
                return $result;
            }

            $moduleCounter = 0;
            foreach ($modules as $module) {
                if ($module['value']['objectsettings']['uid'] === $uid) {
                    $result['moduleId'] = $moduleCounter;
                    return $result;
                }
            }
            return $result;
        }

        if ($modules[$supposedModuleIndex]['value']['relationGroup']['relations'][$supposedRelationIndex]['uid'] === $uid) {
            $result['terminal'] = 'relationWire_' . $supposedRelationIndex;
            return $result;
        }

        $moduleCounter = 0;
        foreach ($modules as $module) {
            $relationCounter = 0;
            foreach ($module['value']['relationGroup']['relations'] as $relation) {
                if ($relation['uid'] === $uid) {
                    $result['moduleId'] = $moduleCounter;
                    $result['terminal'] = 'relationWire_' . $relationCounter;
                    return $result;
                }
                $relationCounter++;
            }
            $moduleCounter++;
        }
        return $result;
    }

    public function getParentClassForValueObject(Extension $extension): string
    {
        $settings = $this->getExtensionSettings($extension->getExtensionKey(), $extension->getStoragePath());
        return $settings['classBuilder']['Model']['AbstractValueObject']['parentClass'] ??
            '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractValueObject';
    }

    public function getParentClassForEntityObject(Extension $extension): string
    {
        $settings = $this->getExtensionSettings($extension->getExtensionKey(), $extension->getStoragePath());
        return $settings['classBuilder']['Model']['AbstractEntity']['parentClass'] ??
            '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractEntity';
    }
}
