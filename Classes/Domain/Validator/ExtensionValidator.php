<?php
namespace EBT\ExtensionBuilder\Domain\Validator;

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
use EBT\ExtensionBuilder\Domain\Exception\ExtensionException;
use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation;
use EBT\ExtensionBuilder\Service\ValidationService;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Schema for a whole extension
 *
 */
class ExtensionValidator extends AbstractValidator
{
    /*
     * Error Codes:
     * 0 - 99: Errors concerning the Extension configuration
     * 100 - 199: Errors concerning the Domain Objects directly
     * 200 - 299: Errors concerning the Properties
     */
    /**
     * @var int
     */
    const ERROR_EXTKEY_LENGTH = 0;
    /**
     * @var int
     */
    const ERROR_EXTKEY_ILLEGAL_CHARACTERS = 1;
    /**
     * @var int
     */
    const ERROR_EXTKEY_ILLEGAL_PREFIX = 2;
    /**
     * @var int
     */
    const ERROR_EXTKEY_ILLEGAL_FIRST_CHARACTER = 3;
    /**
     * @var int
     */
    const ERROR_DOMAINOBJECT_ILLEGAL_CHARACTER = 100;
    /**
     * @var int
     */
    const ERROR_DOMAINOBJECT_NO_NAME = 101;
    /**
     * @var int
     */
    const ERROR_DOMAINOBJECT_LOWER_FIRST_CHARACTER = 102;
    /**
     * @var int
     */
    const ERROR_DOMAINOBJECT_DUPLICATE = 103;
    /**
     * @var int
     */
    const ERROR_PROPERTY_NO_NAME = 200;
    /**
     * @var int
     */
    const ERROR_PROPERTY_DUPLICATE = 201;
    /**
     * @var int
     */
    const ERROR_PROPERTY_ILLEGAL_CHARACTER = 202;
    /**
     * @var int
     */
    const ERROR_PROPERTY_UPPER_FIRST_CHARACTER = 203;
    /**
     * @var int
     */
    const ERROR_PROPERTY_RESERVED_WORD = 204;
    /**
     * @var int
     */
    const ERROR_PROPERTY_RESERVED_SQL_WORD = 205;
    /**
     * @var int
     */
    const ERROR_PLUGIN_DUPLICATE_KEY = 300;
    /**
     * @var int
     */
    const ERROR_PLUGIN_INVALID_KEY = 301;
    /**
     * @var int
     */
    const ERROR_BACKENDMODULE_DUPLICATE_KEY = 400;
    /**
     * @var int
     */
    const ERROR_BACKENDMODULE_INVALID_KEY = 401;
    /**
     * @var int
     */
    const ERROR_ACTIONNAME_DUPLICATE = 501;
    /**
     * @var int
     */
    const ERROR_ACTIONNAME_ILLEGAL_CHARACTER = 502;
    /**
     * @var int
     */
    const ERROR_MISCONFIGURATION = 503;
    /**
     * @var int
     */
    const ERROR_ACTION_MISCONFIGURATION = 504;
    /**
     * @var int
     */
    const EXTENSION_DIR_EXISTS = 500;
    /**
     * @var int
     */
    const ERROR_MAPPING_NO_TCA = 600;
    /**
     * @var int
     */
    const ERROR_MAPPING_NO_PARENTCLASS = 601;
    /**
     * @var int
     */
    const ERROR_MAPPING_NO_TABLE = 602;
    /**
     * @var int
     */
    const ERROR_MAPPING_NO_FOREIGNCLASS = 603;
    /**
     * @var int
     */
    const ERROR_MAPPING_WIRE_AND_FOREIGNCLASS = 604;
    /**
     * @var int
     */
    const ERROR_MAPPING_WRONG_TYPEFIELD_CONFIGURATION = 605;
    /**
     * @var int
     */
    const ERROR_MAPPING_TO_INCOMPATIBLE_TABLE = 606;
    /**
     * @var ExtensionBuilderConfigurationManager
     */
    protected $configurationManager = null;
    /**
     * can be set in settings.yaml
     *
     * @var array
     */
    protected $warningsToIgnore = [];

    /**
     * @param ExtensionBuilderConfigurationManager $configurationManager
     * @return void
     */
    public function injectExtensionBuilderConfigurationManager(ExtensionBuilderConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * keeping warnings (which will result in a confirmation)
     *
     * @var array[]
     */
    protected $validationResult = ['errors' => [], 'warnings' => []];

    /**
     * Validate the given extension
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     *
     * @return array[]
     * @throws \EBT\ExtensionBuilder\Domain\Exception\ExtensionException
     * @throws \Exception
     */
    public function isValid($extension)
    {
        $extensionSettings = $extension->getSettings();
        if (isset($extensionSettings['ignoreWarnings'])) {
            $this->warningsToIgnore = $extensionSettings['ignoreWarnings'];
        }

        $this->validationResult = ['errors' => [], 'warnings' => []];

        $this->validateExtensionKey($extension->getExtensionKey());
        $this->checkExistingExtensions($extension);
        $this->validatePlugins($extension);
        $this->validateBackendModules($extension);
        $this->validateDomainObjects($extension);

        if (!empty($this->warningsToIgnore)) {
            $warningsToKeep = [];
            foreach ($this->validationResult['warnings'] as $warning) {
                /* @var ExtensionException $warning */
                if (!in_array($warning->getCode(), $this->warningsToIgnore)) {
                    $warningsToKeep[] = $warning;
                }
            }
            $this->validationResult['warnings'] = $warningsToKeep;
        }

        return $this->validationResult;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     *
     * @return void
     * @throws \Exception
     */
    protected function checkExistingExtensions($extension)
    {
        if (is_dir($extension->getExtensionDir())) {
            $settingsFile = $extension->getExtensionDir() .
                ExtensionBuilderConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE;
            if (!file_exists($settingsFile) || $extension->isRenamed()) {
                $this->validationResult['warnings'][] = new ExtensionException(
                    'Extension directory exists',
                    self::EXTENSION_DIR_EXISTS
                );
            }
        }
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     * @return void
     */
    private function validatePlugins($extension)
    {
        if (count($extension->getPlugins()) < 1) {
            return;
        }
        $pluginKeys = [];
        /** @var $plugin \EBT\ExtensionBuilder\Domain\Model\Plugin */
        foreach ($extension->getPlugins() as $plugin) {
            if (self::validatePluginKey($plugin->getKey()) === 0) {
                $this->validationResult['errors'][] = new \Exception(
                    'Invalid plugin key in plugin ' . $plugin->getName() . ': "' . $plugin->getKey() . '".' . LF .
                    'Only alphanumeric character without spaces are allowed',
                    self::ERROR_PLUGIN_INVALID_KEY
                );
            }
            if (in_array($plugin->getKey(), $pluginKeys)) {
                $this->validationResult['errors'][] = new \Exception(
                    'Duplicate plugin key: "' . $plugin->getKey() . '". Plugin keys must be unique.',
                    self::ERROR_PLUGIN_DUPLICATE_KEY
                );
            }
            $pluginKeys[] = $plugin->getKey();

            $this->validatePluginConfiguration($plugin, $extension);
        }
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\Plugin $plugin
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     * @return void
     */
    private function validatePluginConfiguration($plugin, $extension)
    {
        $controllerActionCombinationConfiguration = $plugin->getControllerActionCombinations();
        if (is_array($controllerActionCombinationConfiguration)) {
            $firstControllerAction = true;
            foreach ($controllerActionCombinationConfiguration as $controllerName => $actionNames) {
                $this->validateActionConfiguration($controllerName, $actionNames, 'plugin ' . $plugin->getName(), $extension, $firstControllerAction);
                $firstControllerAction = false;
            }
        }
        $noncachableActionConfiguration = $plugin->getNoncacheableControllerActions();
        if (is_array($noncachableActionConfiguration)) {
            foreach ($noncachableActionConfiguration as $controllerName => $actionNames) {
                $this->validateActionConfiguration($controllerName, $actionNames, 'plugin ' . $plugin->getName(), $extension);
            }
        }
        $switchableActionConfiguration = $plugin->getSwitchableControllerActions();
        if (is_array($switchableActionConfiguration)) {
            foreach ($switchableActionConfiguration as $switchableAction) {
                $configuredActions = [];
                foreach ($switchableAction['actions'] as $actions) {
                    // Format should be: Controller->action
                    list($controllerName, $actionName) = explode('->', $actions);
                    $configuredActions[] = $actionName;
                    $this->validateActionConfiguration($controllerName, [$actionName], 'plugin ' . $plugin->getName(), $extension);
                }
                $this->validateDependentActions($configuredActions, 'plugin ' . $plugin->getName());
            }
        }
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\BackendModule $backendModule
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     * @return void
     */
    private function validateBackendModuleConfiguration($backendModule, $extension)
    {
        $controllerActionCombinationConfiguration = $backendModule->getControllerActionCombinations();
        if (is_array($controllerActionCombinationConfiguration)) {
            $firstControllerAction = true;
            foreach ($controllerActionCombinationConfiguration as $controllerName => $actionNames) {
                $this->validateActionConfiguration($controllerName, $actionNames, 'module ' . $backendModule->getName(), $extension, $firstControllerAction);
                $firstControllerAction = false;
            }
        }
    }

    private function validateDependentActions($actionNames, $name)
    {
        if ((in_array('new', $actionNames) && !in_array('create', $actionNames)) ||
            (in_array('create', $actionNames) && !in_array('new', $actionNames))
        ) {
            $this->validationResult['warnings'][] = new ExtensionException(
                'Potential misconfiguration in ' . $name . ':' . LF . 'Actions new and create usually depend on each other',
                self::ERROR_ACTION_MISCONFIGURATION
            );
        }
        if ((in_array('edit', $actionNames) && !in_array('update', $actionNames)) ||
            (in_array('update', $actionNames) && !in_array('edit', $actionNames))
        ) {
            $this->validationResult['warnings'][] = new ExtensionException(
                'Potential misconfiguration in ' . $name . ':' . LF . 'Actions edit and update usually depend on each other',
                self::ERROR_ACTION_MISCONFIGURATION
            );
        }
    }

    /**
     * @param string $controllerName
     * @param array $actionNames
     * @param string $label related plugin or module
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     * @param bool $firstControllerAction
     * @return void
     */
    private function validateActionConfiguration($controllerName, $actionNames, $label, $extension, $firstControllerAction = false)
    {
        if ($firstControllerAction) {
            // the first Controller action config is the default Controller action
            // we show a warning if that's an action that requires a domain object as parameter
            $defaultAction = reset($actionNames);
            if (in_array($defaultAction, ['show', 'edit'])) {
                $this->validationResult['warnings'][] = new ExtensionException(
                    'Potential misconfiguration in ' . $label . ':' . LF .
                    'Default action ' . $controllerName . '->' . $defaultAction . '  can not be called without a domain object parameter',
                    self::ERROR_ACTION_MISCONFIGURATION
                );
            }
        }

        $relatedDomainObject = $extension->getDomainObjectByName($controllerName);
        if (!$relatedDomainObject) {
            $this->validationResult['warnings'][] = new ExtensionException(
                'Potential misconfiguration in ' . $label . ':' . LF . 'Controller ' . $controllerName . ' has no related Domain Object',
                self::ERROR_ACTION_MISCONFIGURATION
            );
        } else {
            $existingActions = $relatedDomainObject->getActions();
            $existingActionNames = [];
            foreach ($existingActions as $existingAction) {
                $existingActionNames[] = $existingAction->getName();
            }

            foreach ($actionNames as $actionName) {
                if (!in_array($actionName, $existingActionNames)) {
                    $this->validationResult['warnings'][] = new ExtensionException(
                        'Potential misconfiguration in ' . $label . ':' . LF . 'Controller ' . $controllerName . ' has no action named ' . $actionName,
                        self::ERROR_ACTION_MISCONFIGURATION
                    );
                }
            }
        }
    }

    /**
     * @param array $configuration
     * @return array
     */
    public function validateConfigurationFormat($configuration)
    {
        foreach ($configuration['properties']['plugins'] as $pluginConfiguration) {
            $pluginName = $pluginConfiguration['name'];
            if (!empty($pluginConfiguration['actions'])) {
                $configTypes = ['controllerActionCombinations', 'noncacheableActions'];
                foreach ($configTypes as $configType) {
                    if (!empty($pluginConfiguration['actions'][$configType])) {
                        $isValid = $this->validateActionConfigFormat($pluginConfiguration['actions'][$configType]);
                        if (!$isValid) {
                            $this->validationResult['warnings'][] = new ExtensionException(
                                'Wrong format in configuration for ' . $configType . ' in plugin ' . $pluginName,
                                self::ERROR_MISCONFIGURATION
                            );
                        }
                    }
                }
                if (!empty($pluginConfiguration['actions']['switchableActions'])) {
                    $isValid = true;
                    $lines = GeneralUtility::trimExplode(LF, $pluginConfiguration['actions']['switchableActions'], true);
                    $firstLine = true;
                    foreach ($lines as $line) {
                        if ($firstLine) {
                            // label for flexform select
                            if (!preg_match('/^[a-zA-Z0-9_\-\s]*$/', $line)) {
                                $isValid = false;
                            }
                            $firstLine = false;
                        } else {
                            $parts = GeneralUtility::trimExplode(';', $line, true);
                            if (count($parts) < 1) {
                                $isValid = false;
                            }
                            foreach ($parts as $part) {
                                if (!empty($part) && count(GeneralUtility::trimExplode('->', $part, true)) != 2) {
                                    $isValid = false;
                                }
                            }
                            $firstLine = true;
                        }
                    }
                    if (!$isValid) {
                        $this->validationResult['warnings'][] = new ExtensionException(
                            'Wrong format in configuration for switchable ControllerActions in plugin ' . $pluginName,
                            self::ERROR_MISCONFIGURATION
                        );
                    }
                }
            }
        }
        foreach ($configuration['properties']['backendModules'] as $moduleConfiguration) {
            $moduleName = $moduleConfiguration['name'];
            if (!empty($moduleConfiguration['actions'])) {
                $configTypes = ['controllerActionCombinations'];
                foreach ($configTypes as $configType) {
                    if (!empty($moduleConfiguration['actions'][$configType])) {
                        $isValid = $this->validateActionConfigFormat($moduleConfiguration['actions'][$configType]);
                        if (!$isValid) {
                            $this->validationResult['warnings'][] = new ExtensionException(
                                'Wrong format in configuration for ' . $configType . ' in module ' . $moduleName,
                                self::ERROR_MISCONFIGURATION
                            );
                        }
                    }
                }
            }
        }
        foreach ($configuration['modules'] as $domainObjectConfiguration) {
            $propertyNames = [];
            if (isset($domainObjectConfiguration['value']['propertyGroup']['properties'])) {
                foreach ($domainObjectConfiguration['value']['propertyGroup']['properties'] as $property) {
                    if (in_array($property['propertyName'], $propertyNames)) {
                        $this->validationResult['errors'][] = new ExtensionException(
                            'Property "' . $property['propertyName'] . '" of Model "' . $domainObjectConfiguration['value']['name'] . '" exists twice.',
                            self::ERROR_PROPERTY_DUPLICATE
                        );
                    }
                    $propertyNames[] = $property['propertyName'];
                }
            }
            // check relation names, since these will result in class properties too
            if (isset($domainObjectConfiguration['value']['relationGroup']['relations'])) {
                foreach ($domainObjectConfiguration['value']['relationGroup']['relations'] as $property) {
                    if (in_array($property['relationName'], $propertyNames)) {
                        $this->validationResult['errors'][] = new ExtensionException(
                            'Property "' . $property['relationName'] . '" of Model "' . $domainObjectConfiguration['value']['name'] . '" exists twice.',
                            self::ERROR_PROPERTY_DUPLICATE
                        );
                    }
                    $propertyNames[] = $property['relationName'];
                }
            }
        }
        return $this->validationResult;
    }

    /**
     * @param string $configuration
     * @return bool
     */
    protected function validateActionConfigFormat($configuration)
    {
        $isValid = true;
        $lines = GeneralUtility::trimExplode(LF, $configuration, true);
        foreach ($lines as $line) {
            $test = GeneralUtility::trimExplode('=>', $line, true);
            if (count($test) != 2) {
                $isValid = false;
            } elseif (!preg_match('/^[a-zA-Z0-9_,\s]*$/', $test[1])) {
                $isValid = false;
            }
        }
        return $isValid;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     * @return void
     */
    private function validateBackendModules($extension)
    {
        if (count($extension->getBackendModules()) < 1) {
            return;
        }
        $backendModuleKeys = [];
        /** @var $backendModule \EBT\ExtensionBuilder\Domain\Model\BackendModule */
        foreach ($extension->getBackendModules() as $backendModule) {
            if (self::validateModuleKey($backendModule->getKey()) === 0) {
                $this->validationResult['errors'][] = new \Exception(
                    'Invalid key in backend module "' . $backendModule->getName() . LF . '". Only alphanumeric character without spaces are allowed',
                    self::ERROR_BACKENDMODULE_INVALID_KEY
                );
            }
            if (in_array($backendModule->getKey(), $backendModuleKeys)) {
                $this->validationResult['errors'][] = new \Exception(
                    'Duplicate backend module key: "' . $backendModule->getKey() . LF . '". Backend module keys must be unique.',
                    self::ERROR_BACKENDMODULE_DUPLICATE_KEY
                );
            }
            $backendModuleKeys[] = $backendModule->getKey();
            $this->validateBackendModuleConfiguration($backendModule, $extension);
        }
    }

    /**
     * @author Sebastian Michaelsen <sebastian.gebhard@gmail.com>
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     * @return void
     */
    private function validateDomainObjects($extension)
    {
        $actionCounter = 0;
        foreach ($extension->getDomainObjects() as $domainObject) {
            $actionCounter .= count($domainObject->getActions());
            // Check if domainObject name is given
            if (!$domainObject->getName()) {
                $this->validationResult['errors'][] = new ExtensionException(
                    'A Domain Object has no name',
                    self::ERROR_DOMAINOBJECT_NO_NAME
                );
            }

            /**
             * Character test
             * Allowed characters are: a-z (lowercase), A-Z (uppercase) and 0-9
             */
            if (!preg_match('/^[a-zA-Z0-9]*$/', $domainObject->getName())) {
                $this->validationResult['errors'][] = new ExtensionException(
                    'Illegal domain object name "' . $domainObject->getName() . '". Please use UpperCamelCase, no spaces or underscores.',
                    self::ERROR_DOMAINOBJECT_ILLEGAL_CHARACTER
                );
            }

            $objectName = $domainObject->getName();
            $firstChar = $objectName{0};
            if (strtolower($firstChar) == $firstChar) {
                $this->validationResult['errors'][] = new ExtensionException(
                    'Illegal first character of domain object name "' . $domainObject->getName() . '". Please use UpperCamelCase.',
                    self::ERROR_DOMAINOBJECT_LOWER_FIRST_CHARACTER
                );
            }
            if (ValidationService::isReservedExtbaseWord($objectName)) {
                $this->validationResult['errors'][] = new ExtensionException(
                    'Domain object name "' . $domainObject->getName() . '" may not be used in extbase.',
                    self::ERROR_PROPERTY_RESERVED_WORD
                );
            }

            $this->validateProperties($domainObject);
            $this->validateDomainObjectActions($domainObject);
            $this->validateMapping($domainObject);
        }
        if ($actionCounter < 1) {
            if (count($extension->getBackendModules()) > 0) {
                $this->validationResult['warnings'][] = new ExtensionException(
                    'Potential misconfiguration: No actions configured!' . LF . 'This will result in a missing default action in your backend module',
                    self::ERROR_ACTION_MISCONFIGURATION
                );
            }
            if (count($extension->getPlugins()) > 0) {
                $this->validationResult['warnings'][] = new ExtensionException(
                    'Potential misconfiguration: No actions configured!' . LF . 'This will result in a missing default action in your plugin',
                    self::ERROR_ACTION_MISCONFIGURATION
                );
            }
        }
    }

    /**
     * cover all cases:
     * 1. extend TYPO3 class like fe_users (no mapping table needed)
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     *
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    private function validateMapping(DomainObject $domainObject)
    {
        $parentClass = $domainObject->getParentClass();
        $tableName = $domainObject->getMapToTable();
        $extensionPrefix = 'Tx_' . $domainObject->getExtension()->getExtensionName() . '_Domain_Model_';
        if (!empty($parentClass)) {
            $classConfiguration = $this->configurationManager->getExtbaseClassConfiguration($parentClass);
            if (!isset($classConfiguration['tableName'])) {
                if (!$tableName) {
                    $this->validationResult['errors'][] = new ExtensionException(
                        'Mapping configuration error in domain object ' . $domainObject->getName() . ': ' . LF .
                        'The mapping table could not be detected from Extbase Configuration. Please enter a table name',
                        self::ERROR_MAPPING_NO_TABLE
                    );
                }
            } else {
                // get the table name from the parent class configuration
                $tableName = $classConfiguration['tableName'];
            }

            if (!class_exists($parentClass, true)) {
                $this->validationResult['errors'][] = new ExtensionException(
                    'Mapping configuration error in domain object ' . $domainObject->getName() . ': the parent class ' . LF .
                    $parentClass . 'seems not to exist ',
                    self::ERROR_MAPPING_NO_PARENTCLASS
                );
            }
        }
        if ($tableName) {
            if (in_array($tableName, ['tt_content', 'pages']) || preg_match('/^(pages_|be_|sys_|static_|cf_)/', $tableName)) {
                $this->validationResult['warnings'][] = new ExtensionException(
                    'The configuration for table "' . $tableName . '" is not compatible' . LF .
                    ' with extbase. You have to configure it yourself if you want to map' . LF .
                    ' to this table',
                    self::ERROR_MAPPING_TO_INCOMPATIBLE_TABLE
                );
            }
            if (strpos($extensionPrefix, $tableName) !== false) {
                // the domainObject extends a class of the same extension
                if (!$parentClass) {
                    $this->validationResult['errors'][] = new ExtensionException(
                        'Mapping configuration error in domain object ' . $domainObject->getName() . ': you have to define' . LF .
                        'a parent class if you map to a table of another domain object of the same extension ',
                        self::ERROR_MAPPING_NO_PARENTCLASS
                    );
                }
            }
            if (!isset($GLOBALS['TCA'][$tableName])) {
                $this->validationResult['errors'][] = new ExtensionException(
                    'There is no entry for table "' . $tableName . '" of ' . $domainObject->getName() . ' in TCA. ' . LF .
                    'For technical reasons you can only extend tables with TCA configuration.',
                    self::ERROR_MAPPING_NO_TCA
                );
            }
        }
        if (isset($GLOBALS['TCA'][$tableName]['ctrl']['type'])) {
            $columns = $this->getDatabaseConnection($tableName)->getSchemaManager()->listTableColumns($tableName);
            foreach ($columns as $column) {
                if ($column->getName()  === $GLOBALS['TCA'][$tableName]['ctrl']['type']) {
                    if ((String) $column->getType() === 'Integer') {
                        $this->validationResult['warnings'][] = new ExtensionException(
                            'This means the type field can not be used for defining the record type. ' . LF .
                            'You have to configure the mappings yourself if you want to map to this' . LF .
                            'table or extend the correlated class',
                            self::ERROR_MAPPING_WRONG_TYPEFIELD_CONFIGURATION
                        );
                    }
                }
            }
        }
    }

    /**
     * $actions = $domainObject->getActions();
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     * @return void
     */
    private function validateDomainObjectActions(DomainObject $domainObject)
    {
        $actionNames = [];
        $actions = $domainObject->getActions();
        foreach ($actions as $action) {
            if (in_array($action->getName(), $actionNames)) {
                $this->validationResult['errors'][] = new ExtensionException(
                    'Duplicate action name "' . $action->getName() . '" of ' . $domainObject->getName() . LF .
                    '; action names have to be unique for each model',
                    self::ERROR_ACTIONNAME_DUPLICATE
                );
            }
            /**
             * Character test
             * Allowed characters are: a-z (lowercase), A-Z (uppercase) and 0-9
             */
            if (!preg_match('/^[a-zA-Z0-9]*$/', $action->getName())) {
                $this->validationResult['errors'][] = new ExtensionException(
                    'Illegal action name "' . $action->getName() . '" of ' . $domainObject->getName() . '.' . LF .
                    'Please use lowerCamelCase, no spaces or underscores.',
                    self::ERROR_ACTIONNAME_ILLEGAL_CHARACTER
                );
            }
            $actionNames[] = $action->getName();
        }
        $this->validateDependentActions($actionNames, 'Domain object ' . $domainObject->getName());

        $firstAction = reset($actionNames);
        if ($firstAction == 'show' || $firstAction == 'edit' || $firstAction == 'delete') {
            $this->validationResult['warnings'][] = new ExtensionException(
                'Potential misconfiguration in Domain object ' . $domainObject->getName() . ':' . LF .
                'First action could not be default action since "' . $firstAction . '" action needs a parameter',
                self::ERROR_ACTION_MISCONFIGURATION
            );
        }
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     * @return void
     */
    private function validateProperties($domainObject)
    {
        $propertyNames = [];
        foreach ($domainObject->getProperties() as $property) {
            // Check if property name is given
            if (!$property->getName()) {
                $this->validationResult['errors'][] = new ExtensionException(
                    'A property of ' . $domainObject->getName() . ' has no name',
                    self::ERROR_PROPERTY_NO_NAME
                );
            }
            $propertyName = $property->getName();
            /**
             * Character test
             * Allowed characters are: a-z (lowercase), A-Z (uppercase) and 0-9
             */
            if (!preg_match('/^[a-zA-Z0-9]*$/', $propertyName)) {
                $this->validationResult['errors'][] = new ExtensionException(
                    'Illegal property name "' . $propertyName . '" of ' . $domainObject->getName() . '.' . LF .
                    'Please use lowerCamelCase, no spaces or underscores.',
                    self::ERROR_PROPERTY_ILLEGAL_CHARACTER
                );
            }

            $firstChar = $propertyName{0};
            if (strtoupper($firstChar) == $firstChar) {
                $this->validationResult['errors'][] = new ExtensionException(
                    'Illegal first character of property name "' . $property->getName() . '" of domain object "' .
                    $domainObject->getName() . '".' . LF .
                    'Please use lowerCamelCase.',
                    self::ERROR_PROPERTY_UPPER_FIRST_CHARACTER
                );
            }

            if (ValidationService::isReservedTYPO3Word($propertyName)) {
                $this->validationResult['warnings'][] = new ExtensionException(
                    'The name of property "' . $propertyName . '" in Model "' . $domainObject->getName() .
                    '" will result in a TYPO3 specific column name.' . LF .
                    ' This might result in unexpected behaviour. If you didn\'t choose that name by purpose' . LF .
                    ' it is recommended to use another name',
                    self::ERROR_PROPERTY_RESERVED_WORD
                );
            }

            if (ValidationService::isReservedMYSQLWord($propertyName)) {
                $this->validationResult['warnings'][] = new ExtensionException(
                    'Property "' . $propertyName . '" in Model "' . $domainObject->getName() . '".',
                    self::ERROR_PROPERTY_RESERVED_SQL_WORD
                );
            }

            // Check for duplicate property names
            if (in_array($propertyName, $propertyNames)) {
                $this->validationResult['errors'][] = new ExtensionException(
                    'Property "' . $property->getName() . '" of ' . $domainObject->getName() . ' exists twice.',
                    self::ERROR_PROPERTY_DUPLICATE
                );
            }
            $propertyNames[] = $propertyName;

            if ($property instanceof AbstractRelation) {
                if (!$property->getForeignModel() && $property->getForeignClassName()) {
                    if (!class_exists($property->getForeignClassName())) {
                        $this->validationResult['errors'][] = new ExtensionException(
                            'Related class not loadable: "' . $property->getForeignClassName() . '" configured in relation "' . $property->getName() . '".',
                            self::ERROR_MAPPING_NO_FOREIGNCLASS
                        );
                    }
                }
                if ($property->getForeignModel() && ($property->getForeignModel()->getFullQualifiedClassName() != $property->getForeignClassName())) {
                    $this->validationResult['errors'][] = new ExtensionException(
                        'Relation "' . $property->getName() . '" in model "' . $domainObject->getName() .
                        '" has a external class relation and a wire to ' . $property->getForeignModel()->getName(),
                        self::ERROR_MAPPING_WIRE_AND_FOREIGNCLASS
                    );
                }
            }
        }
    }

    /**
     * validates a plugin key
     * @param string $key
     * @return bool true if valid
     */
    private static function validatePluginKey($key)
    {
        return preg_match('/^[a-zA-Z0-9_\-]*$/', $key);
    }

    /**
     * validates a backend module key
     * @param string $key
     * @return bool true if valid
     */
    private static function validateModuleKey($key)
    {
        return preg_match('/^[a-zA-Z0-9_\-]*$/', $key);
    }

    /**
     * @author Rens Admiraal
     * @param string $key
     * @return void
     */
    private function validateExtensionKey($key)
    {
        /**
         * Character test
         * Allowed characters are: a-z (lowercase), 0-9 and '_' (underscore)
         */
        if (!preg_match('/^[a-z0-9_]*$/', $key)) {
            $this->validationResult['errors'][] = new ExtensionException(
                'Illegal characters in extension key',
                self::ERROR_EXTKEY_ILLEGAL_CHARACTERS
            );
        }

        /**
         * Start character
         * Extension keys cannot start or end with 0-9 and '_' (underscore)
         */
        if (preg_match('/^[0-9_]/', $key)) {
            $this->validationResult['errors'][] = new ExtensionException(
                'Illegal first character of extension key',
                self::ERROR_EXTKEY_ILLEGAL_FIRST_CHARACTER
            );
        }

        /**
         * Extension key length
         * An extension key must have minimum 3, maximum 30 characters (not counting underscores)
         */
        $keyLengthTest = str_replace('_', '', $key);
        if (strlen($keyLengthTest) < 3 || strlen($keyLengthTest) > 30) {
            $this->validationResult['errors'][] = new ExtensionException(
                'Invalid extension key length',
                self::ERROR_EXTKEY_LENGTH
            );
        }

        /**
         * Reserved prefixes
         * The key must not being with one of the following prefixes: tx,pages,tt_,sys_,ts_language_,csh_
         */
        if (preg_match('/^(tx|pages_|tt_|sys_|ts_language_|csh_)/', $key)) {
            $this->validationResult['errors'][] = new ExtensionException(
                'Illegal extension key prefix',
                self::ERROR_EXTKEY_ILLEGAL_PREFIX
            );
        }
    }

    /**
     *
     * @param string $word
     *
     * @return bool
     */
    public static function isReservedWord($word)
    {
        return ValidationService::isReservedMYSQLWord($word) || ValidationService::isReservedTYPO3Word($word);
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection($tableName)
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tableName);
    }
}
