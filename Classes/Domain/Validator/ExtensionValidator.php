<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Rens Admiraal
 *  (c) 2011 Nico de Haen
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Schema for a whole extension
 *
 * @version $ID:$
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class Tx_ExtensionBuilder_Domain_Validator_ExtensionValidator extends TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator {

	/**
	 * Error Codes:
	 * 0 - 99: Errors concerning the Extension configuration
	 * 100 - 199: Errors concerning the Domain Objects directly
	 * 200 - 299: Errors concerning the Properties
	 */
	const	ERROR_EXTKEY_LENGTH = 0,
	ERROR_EXTKEY_ILLEGAL_CHARACTERS = 1,
	ERROR_EXTKEY_ILLEGAL_PREFIX = 2,
	ERROR_EXTKEY_ILLEGAL_FIRST_CHARACTER = 3,
	ERROR_DOMAINOBJECT_ILLEGAL_CHARACTER = 100,
	ERROR_DOMAINOBJECT_NO_NAME = 101,
	ERROR_DOMAINOBJECT_LOWER_FIRST_CHARACTER = 102,
	ERROR_DOMAINOBJECT_DUPLICATE = 103,
	ERROR_PROPERTY_NO_NAME = 200,
	ERROR_PROPERTY_DUPLICATE = 201,
	ERROR_PROPERTY_ILLEGAL_CHARACTER = 202,
	ERROR_PROPERTY_UPPER_FIRST_CHARACTER = 203,
	ERROR_PROPERTY_RESERVED_WORD = 204,
	ERROR_PROPERTY_RESERVED_SQL_WORD = 205,
	ERROR_PLUGIN_DUPLICATE_KEY = 300,
	ERROR_PLUGIN_INVALID_KEY = 301,
	ERROR_BACKENDMODULE_DUPLICATE_KEY = 400,
	ERROR_BACKENDMODULE_INVALID_KEY = 401,
	ERROR_ACTIONNAME_DUPLICATE = 501,
	ERROR_ACTIONNAME_ILLEGAL_CHARACTER = 502,
	ERROR_MISCONFIGURATION = 503,
	ERROR_ACTION_MISCONFIGURATION = 504,
	EXTENSION_DIR_EXISTS = 500,
	ERROR_MAPPING_NO_TCA = 600,
	ERROR_MAPPING_NO_PARENTCLASS = 601,
	ERROR_MAPPING_NO_TABLE = 602,
	ERROR_MAPPING_NO_FOREIGNCLASS = 603,
	ERROR_MAPPING_WIRE_AND_FOREIGNCLASS = 604,
	ERROR_MAPPING_WRONG_TYPEFIELD_CONFIGURATION = 605;

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param Tx_ExtensionBuilder_Configuration_ConfigurationManager $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_ExtensionBuilder_Configuration_ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
	}


	/**
	 * TODO: Check this list if it's still up to date
	 * Reserved words by MySQL
	 * @var array
	 */
	static protected $reservedMYSQLWords = array(
		'ACCESSIBLE',
		'ADD',
		'ALL',
		'ALTER',
		'ANALYZE',
		'AND',
		'AS',
		'ASC',
		'ASENSITIVE',
		'BEFORE',
		'BETWEEN',
		'BIGINT',
		'BINARY',
		'BLOB',
		'BOTH',
		'BY',
		'CALL',
		'CASCADE',
		'CASE',
		'CHANGE',
		'CHAR',
		'CHARACTER',
		'CHECK',
		'COLLATE',
		'COLUMN',
		'CONDITION',
		'CONSTRAINT',
		'CONTINUE',
		'CONVERT',
		'CREATE',
		'CROSS',
		'CURRENT_DATE',
		'CURRENT_TIME',
		'CURRENT_TIMESTAMP',
		'CURRENT_USER',
		'CURSOR',
		'DATABASE',
		'DATABASES',
		'DAY_HOUR',
		'DAY_MICROSECOND',
		'DAY_MINUTE',
		'DAY_SECOND',
		'DEC',
		'DECIMAL',
		'DECLARE',
		'DEFAULT',
		'DELAYED',
		'DELETE',
		'DESC',
		'DESCRIBE',
		'DETERMINISTIC',
		'DISTINCT',
		'DISTINCTROW',
		'DIV',
		'DOUBLE',
		'DROP',
		'DUAL',
		'EACH',
		'ELSE',
		'ELSEIF',
		'ENCLOSED',
		'ESCAPED',
		'EXISTS',
		'EXIT',
		'EXPLAIN',
		'FALSE',
		'FETCH',
		'FLOAT',
		'FLOAT4',
		'FLOAT8',
		'FOR',
		'FORCE',
		'FOREIGN',
		'FROM',
		'FULLTEXT',
		'GENERAL',
		'GOTO',
		'GRANT',
		'GROUP',
		'HAVING',
		'HIGH_PRIORITY',
		'HOUR_MICROSECOND',
		'HOUR_MINUTE',
		'HOUR_SECOND',
		'IF',
		'IGNORE',
		'IGNORE_SERVER_IDS',
		'IN',
		'INDEX',
		'INFILE',
		'INNER',
		'INOUT',
		'INSENSITIVE',
		'INSERT',
		'INT',
		'INT1',
		'INT2',
		'INT3',
		'INT4',
		'INT8',
		'INTEGER',
		'INTERVAL',
		'INTO',
		'IS',
		'ITERATE',
		'JOIN',
		'KEY',
		'KEYS',
		'KILL',
		'LABEL',
		'LEADING',
		'LEAVE',
		'LEFT',
		'LIKE',
		'LIMIT',
		'LINEAR',
		'LINES',
		'LOAD',
		'LOCALTIME',
		'LOCALTIMESTAMP',
		'LOCK',
		'LONG',
		'LONGBLOB',
		'LONGTEXT',
		'LOOP',
		'LOW_PRIORITY',
		'MASTER_HEARTBEAT_PERIOD',
		'MASTER_SSL_VERIFY_SERVER_CERT',
		'MATCH',
		'MAXVALUE',
		'MEDIUMBLOB',
		'MEDIUMINT',
		'MEDIUMTEXT',
		'MIDDLEINT',
		'MINUTE_MICROSECOND',
		'MINUTE_SECOND',
		'MOD',
		'MODIFIES',
		'NATURAL',
		'NOT',
		'NO_WRITE_TO_BINLOG',
		'NULL',
		'NUMERIC',
		'ON',
		'OPTIMIZE',
		'OPTION',
		'OPTIONALLY',
		'OR',
		'ORDER',
		'OUT',
		'OUTER',
		'OUTFILE',
		'PRECISION',
		'PRIMARY',
		'PROCEDURE',
		'PURGE',
		'RANGE',
		'READ',
		'READS',
		'READ_WRITE',
		'READ_ONLY',
		'REAL',
		'REFERENCES',
		'REGEXP',
		'RELEASE',
		'RENAME',
		'REPEAT',
		'REPLACE',
		'REQUIRE',
		'RESIGNAL',
		'RESTRICT',
		'RETURN',
		'REVOKE',
		'RIGHT',
		'RLIKE',
		'SCHEMA',
		'SCHEMAS',
		'SECOND_MICROSECOND',
		'SELECT',
		'SENSITIVE',
		'SEPARATOR',
		'SET',
		'SHOW',
		'SIGNAL',
		'SLOW',
		'SMALLINT',
		'SPATIAL',
		'SPECIFIC',
		'SQL',
		'SQLEXCEPTION',
		'SQLSTATE',
		'SQLWARNING',
		'SQL_BIG_RESULT',
		'SQL_CALC_FOUND_ROWS',
		'SQL_SMALL_RESULT',
		'SSL',
		'STARTING',
		'STRAIGHT_JOIN',
		'TABLE',
		'TERMINATED',
		'THEN',
		'TINYBLOB',
		'TINYINT',
		'TINYTEXT',
		'TO',
		'TRAILING',
		'TRIGGER',
		'TRUE',
		'UNDO',
		'UNION',
		'UNIQUE',
		'UNLOCK',
		'UNSIGNED',
		'UPDATE',
		'USAGE',
		'USE',
		'USING',
		'UTC_DATE',
		'UTC_TIME',
		'UTC_TIMESTAMP',
		'VALUES',
		'VARBINARY',
		'VARCHAR',
		'VARCHARACTER',
		'VARYING',
		'WHEN',
		'WHERE',
		'WHILE',
		'WITH',
		'WRITE',
		'XOR',
		'YEAR_MONTH',
		'ZEROFILL'
	);

	/**
	 *
	 * column names used by TYPO3
	 * @var array
	 */
	static protected $reservedTYPO3ColumnNames = array(
		'uid',
		'pid',
		'endtime',
		'starttime',
		'sorting',
		'fe_group',
		'hidden',
		'deleted',
		'cruser_id',
		'crdate',
		'tstamp',
		'sys_language',
		't3ver_oid',
		't3ver_id',
		't3ver_wsid',
		't3ver_label',
		't3ver_state',
		't3ver_stage',
		't3ver_count',
		't3ver_tstamp',
		't3ver_move_id',
		't3_origuid',
		'sys_language_uid',
		'l18n_parent',
		'l18n_diffsource'
	);

	/**
	 * keeping warnings (which will result in a confirmation)
	 * @var array
	 */
	protected $validationResult = array('errors' => array(), 'warnings' => array());

	/**
	 * Validate the given extension
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 * @return boolean
	 */
	public function isValid($extension) {

		$this->validationResult = array('errors' => array(), 'warnings' => array());

		//$this->validateExtensionKey($extension->getExtensionKey());

		$this->checkExistingExtensions($extension);

		$this->validatePlugins($extension);

		$this->validateBackendModules($extension);

		$this->validateDomainObjects($extension);

		return $this->validationResult;
	}


	/**
	 * @param Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 * @return void
	 */
	protected function checkExistingExtensions($extension) {
		if (is_dir($extension->getExtensionDir())) {
			$settingsFile = $extension->getExtensionDir() .
							Tx_ExtensionBuilder_Configuration_ConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE;
			if (!file_exists($settingsFile) || $extension->isRenamed()) {
				$this->validationResult['warnings'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
					'Extension directory exists',
					self::EXTENSION_DIR_EXISTS);
			}
		}
	}

	/**
	 * @param Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 * @return void
	 */
	private function validatePlugins($extension) {
		if (count($extension->getPlugins()) < 1) {
			return;
		}
		$pluginKeys = array();
		foreach ($extension->getPlugins() as $plugin) {
			if (self::validatePluginKey($plugin->getKey()) === 0) {
				$this->validationResult['errors'][] = new Exception('Invalid plugin key in plugin ' . $plugin->getName() . ': "' . $plugin->getKey() . '". Only alphanumeric character without spaces are allowed', self::ERROR_PLUGIN_INVALID_KEY);
			}
			if (in_array($plugin->getKey(), $pluginKeys)) {
				$this->validationResult['errors'][] = new Exception('Duplicate plugin key: "' . $plugin->getKey() . '". Plugin keys must be unique.', self::ERROR_PLUGIN_DUPLICATE_KEY);
			}
			$pluginKeys[] = $plugin->getKey();

			$this->validatePluginConfiguration($plugin, $extension);
		}
	}

	/**
	 * @param Tx_ExtensionBuilder_Domain_Model_Plugin $plugin
	 * @param Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 * @return void
	 */
	private function validatePluginConfiguration($plugin, $extension) {
		$controllerActionCombinationConfiguration = $plugin->getControllerActionCombinations();
		if (is_array($controllerActionCombinationConfiguration)) {
			$firstControllerAction = TRUE;
			foreach ($controllerActionCombinationConfiguration as $controllerName => $actionNames) {
				$this->validateActionConfiguration($controllerName, $actionNames, 'plugin ' . $plugin->getName(), $extension, $firstControllerAction);
				$firstControllerAction = FALSE;
			}
		}
		$noncachableActionConfiguration = $plugin->getNoncacheableControllerActions();
		if (is_array($noncachableActionConfiguration)) {
			foreach ($noncachableActionConfiguration as $controllerName => $actionNames) {
				$this->validateActionConfiguration($controllerName, $actionNames, 'plugin ' . $plugin->getName(), $extension);
			}
		}
		$switchableActionConfiguration = $plugin->getSwitchableControllerActions();
		// \TYPO3\CMS\Core\Utility\GeneralUtility::devlog('validate switchableActionConfiguration', 'extension_builder', 0, $switchableActionConfiguration);
		if (is_array($switchableActionConfiguration)) {
			foreach ($switchableActionConfiguration as $switchableAction) {
				$configuredActions = array();
				foreach ($switchableAction['actions'] as $actions) {
					// Format should be: Controller->action
					list($controllerName, $actionName) = explode('->', $actions);
					$configuredActions[] = $actionName;
					\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Controller' . $controllerName, 'extension_builder', 0, array($actionName));
					$this->validateActionConfiguration($controllerName, array($actionName), 'plugin ' . $plugin->getName(), $extension);
				}
				$this->validateDependentActions($configuredActions, 'plugin ' . $plugin->getName());
			}
		}
	}

	/**
	 * @param Tx_ExtensionBuilder_Domain_Model_BackendModule $backendModule
	 * @param Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 * @return void
	 */
	private function validateBackendModuleConfiguration($backendModule, $extension) {
		$controllerActionCombinationConfiguration = $backendModule->getControllerActionCombinations();
		if (is_array($controllerActionCombinationConfiguration)) {
			$firstControllerAction = TRUE;
			foreach ($controllerActionCombinationConfiguration as $controllerName => $actionNames) {
				$this->validateActionConfiguration($controllerName, $actionNames, 'module ' . $backendModule->getName(), $extension, $firstControllerAction);
				$firstControllerAction = FALSE;
			}
		}
	}

	private function validateDependentActions($actionNames, $name) {
		if ((in_array('new', $actionNames) && !in_array('create', $actionNames)) ||
			(in_array('create', $actionNames) && !in_array('new', $actionNames))
		) {
			$this->validationResult['warnings'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
				'Potential misconfiguration in ' . $name . ':<br />Actions new and create usually depend on each other',
				self::ERROR_MISCONFIGURATION);
		}
		if ((in_array('edit', $actionNames) && !in_array('update', $actionNames)) ||
			(in_array('update', $actionNames) && !in_array('edit', $actionNames))
		) {
			$this->validationResult['warnings'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
				'Potential misconfiguration in ' . $name . ':<br />Actions edit and update usually depend on each other',
				self::ERROR_MISCONFIGURATION);
		}
	}

	/**
	 * @param string $controllerName
	 * @param array $actionNames
	 * @param string $label related plugin or module
	 * @param Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 * @param boolean $firstControllerAction
	 * @return void
	 */
	private function validateActionConfiguration($controllerName, $actionNames, $label, $extension, $firstControllerAction = FALSE) {
		if ($firstControllerAction) {
			// the first Controller action config is the default Controller action
			// we show a warning if that's an action that requires a domain object as parameter
			$defaultAction = reset($actionNames);
			//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Invalid action configuration:'.$defaultAction, 'extension_builder', 1, array($controllerName, $actionNames));
			if (in_array($defaultAction, array('show', 'edit'))) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Invalid action configurations', 'extension_builder', 1, array($controllerName, $actionNames));
				$this->validationResult['warnings'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
					'Potential misconfiguration in ' . $label . ':<br />Default action ' . $controllerName . '->' . $defaultAction . '  can not be called without a domain object parameter',
					self::ERROR_MISCONFIGURATION);
			}
		}

		$relatedDomainObject = $extension->getDomainObjectByName($controllerName);
		if (!$relatedDomainObject) {
			$this->validationResult['warnings'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
				'Potential misconfiguration in ' . $label . ':<br />Controller ' . $controllerName . ' has no related Domain Object',
				self::ERROR_MISCONFIGURATION);
		} else {
			$existingActions = $relatedDomainObject->getActions();
			$existingActionNames = array();
			foreach ($existingActions as $existingAction) {
				$existingActionNames[] = $existingAction->getName();
			}

			foreach ($actionNames as $actionName) {
				if (!in_array($actionName, $existingActionNames)) {
					$this->validationResult['warnings'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
						'Potential misconfiguration in ' . $label . ':<br />Controller ' . $controllerName . ' has no action named ' . $actionName,
						self::ERROR_MISCONFIGURATION);
				}
			}
		}
	}

	/**
	 * @param array $configuration
	 * @return void
	 */
	public function validateConfigurationFormat($configuration) {
		foreach ($configuration['properties']['plugins'] as $pluginConfiguration) {
			$pluginName = $pluginConfiguration['name'];
			if (!empty($pluginConfiguration['actions'])) {
				$configTypes = array('controllerActionCombinations', 'noncacheableActions');
				foreach ($configTypes as $configType) {
					if (!empty($pluginConfiguration['actions'][$configType])) {
						$isValid = $this->validateActionConfigFormat($pluginConfiguration['actions'][$configType], $configType);
						if (!$isValid) {
							\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('validateActionConfigFormat failed', 'extension_builder', 2, array($pluginConfiguration['actions'][$configType]));
							$this->validationResult['warnings'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
								'Wrong format in configuration for ' . $configType . ' in plugin ' . $pluginName,
								self::ERROR_MISCONFIGURATION);
						}
					}
				}
				if (!empty($pluginConfiguration['actions']['switchableActions'])) {
					$isValid = TRUE;
					$lines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode("\n", $pluginConfiguration['actions']['switchableActions'], TRUE);
					$firstLine = TRUE;
					foreach ($lines as $line) {
						if ($firstLine) {
							// label for flexform select
							if (!preg_match("/^[a-zA-Z0-9_-\s]*$/", $line)) {
								$isValid = FALSE;
								\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Label in switchable Actions contained disallowed character:' . $line, 'extension_builder', 2);
							}
							$firstLine = FALSE;
						} else {
							$parts = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(';', $line, TRUE);
							\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('switchable Actions line even:' . $line, 'extension_builder', 0, $parts);
							if (count($parts) < 1) {
								$isValid = FALSE;
								\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Wrong count for explode(";") switchable Actions line:' . $line, 'extension_builder', 2, $parts);
							}
							foreach ($parts as $part) {
								if (!empty($part) && count(\TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('->', $part, TRUE)) != 2) {
									$isValid = FALSE;
									\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Wrong count for explode("->") switchable Actions line:' . $part, 'extension_builder', 2);
								}
							}
							$firstLine = TRUE;
						}
					}
					if (!$isValid) {
						$this->validationResult['warnings'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
							'Wrong format in configuration for switchable ControllerActions in plugin ' . $pluginName,
							self::ERROR_MISCONFIGURATION);
					}
				}
			}
		}
		foreach ($configuration['properties']['backendModules'] as $moduleConfiguration) {
			$moduleName = $moduleConfiguration['name'];
			if (!empty($moduleConfiguration['actions'])) {
				$configTypes = array('controllerActionCombinations');
				foreach ($configTypes as $configType) {
					if (!empty($moduleConfiguration['actions'][$configType])) {
						$isValid = $this->validateActionConfigFormat($moduleConfiguration['actions'][$configType], $configType);
						if (!$isValid) {
							\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('validateActionConfigFormat failed', 'extension_builder', 2, array($moduleConfiguration['actions'][$configType]));
							$this->validationResult['warnings'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
								'Wrong format in configuration for ' . $configType . ' in module ' . $moduleName,
								self::ERROR_MISCONFIGURATION);
						}
					}
				}
			}
		}

		return $this->validationResult;
	}

	/**
	 * @param array $configuration
	 * @return bool
	 */
	protected function validateActionConfigFormat($configuration) {
		$isValid = TRUE;
		$lines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode("\n", $configuration, TRUE);
		foreach ($lines as $line) {
			$test = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('=>', $line, TRUE);
			if (count($test) != 2) {
				$isValid = FALSE;
				\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Wrong count for explode("=>") switchable Actions line:' . $line, 'extension_builder', 2);
			} else if (!preg_match("/^[a-zA-Z0-9_,\s]*$/", $test[1])) {
				$isValid = FALSE;
				\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Regex failed:' . $test[1], 'extension_builder', 2);
			}
		}
		return $isValid;
	}

	/**
	 * @param Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 * @return void
	 */
	private function validateBackendModules($extension) {
		if (count($extension->getBackendModules()) < 1) {
			return;
		}
		$backendModuleKeys = array();
		foreach ($extension->getBackendModules() as $backendModule) {
			if (self::validateModuleKey($backendModule->getKey()) === 0) {
				$this->validationResult['errors'][] = new Exception('Invalid key in backend module ' . $backendModule->getName() . '. Only alphanumeric character without spaces are allowed', self::ERROR_BACKENDMODULE_INVALID_KEY);
			}
			if (in_array($backendModule->getKey(), $backendModuleKeys)) {
				$this->validationResult['errors'][] = new Exception('Duplicate backend module key: "' . $backendModule->getKey() . '". Backend module keys must be unique.', self::ERROR_BACKENDMODULE_DUPLICATE_KEY);
			}
			$backendModuleKeys[] = $backendModule->getKey();
			$this->validateBackendModuleConfiguration($backendModule, $extension);
		}
	}

	/**
	 * @author Sebastian Michaelsen <sebastian.gebhard@gmail.com>
	 * @param	Tx_ExtensionBuilder_Domain_Model_Extension
	 * @return	 void
	 * @throws Tx_ExtensionBuilder_Domain_Exception_ExtensionException
	 */
	private function validateDomainObjects($extension) {

		$actionCounter = 0;
		foreach ($extension->getDomainObjects() as $domainObject) {
			$actionCounter .= count($domainObject->getActions());
			// Check if domainObject name is given
			if (!$domainObject->getName()) {
				$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException('A Domain Object has no name', self::ERROR_DOMAINOBJECT_NO_NAME);
			}

			/**
			 * Character test
			 * Allowed characters are: a-z (lowercase), A-Z (uppercase) and 0-9
			 */
			if (!preg_match("/^[a-zA-Z0-9]*$/", $domainObject->getName())) {
				$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException('Illegal domain object name "' . $domainObject->getName() . '". Please use UpperCamelCase, no spaces or underscores.', self::ERROR_DOMAINOBJECT_ILLEGAL_CHARACTER);
			}

			$objectName = $domainObject->getName();
			$firstChar = $objectName{0};
			if (strtolower($firstChar) == $firstChar) {
				$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException('Illegal first character of domain object name "' . $domainObject->getName() . '". Please use UpperCamelCase.', self::ERROR_DOMAINOBJECT_LOWER_FIRST_CHARACTER);
			}
			$this->validateProperties($domainObject);
			$this->validateDomainObjectActions($domainObject);
			$this->validateMapping($domainObject);
		}
		if($actionCounter < 1) {
			if (count($extension->getBackendModules()) > 0) {
				$this->validationResult['warnings'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
					"Potential misconfiguration: No actions configured, this will result in a missing default action in your backend module",
					self::ERROR_ACTION_MISCONFIGURATION
				);
			}
			if (count($extension->getPlugins()) > 0) {
				$this->validationResult['warnings'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
					"Potential misconfiguration: No actions configured, this will result in a missing default action in your plugin",
					self::ERROR_ACTION_MISCONFIGURATION
				);
			}
		}
	}

	/**
	 * cover all cases:
	 * 1. extend TYPO3 class like fe_users (no mapping table needed)
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 */
	private function validateMapping(Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject) {
		$parentClass = $domainObject->getParentClass();
		$tableName = $domainObject->getMapToTable();
		$extensionPrefix = 'Tx_' . \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($domainObject->getExtension()->getExtensionKey()) . '_Domain_Model_';
		if (!empty($parentClass)) {
			$classConfiguration = $this->configurationManager->getExtbaseClassConfiguration($parentClass);
			\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('class settings ' . $parentClass, 'extension_builder', 0, $classConfiguration);

			if (!isset($classConfiguration['tableName'])) {
				if (!$tableName) {
					$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
						'Mapping configuration error in domain object ' . $domainObject->getName() . ': The mapping table could not be detected from Extbase Configuration. Please enter a table name',
						self::ERROR_MAPPING_NO_TABLE
					);
				}
			} else {
				// get the table name from the parent class configuration
				$tableName = $classConfiguration['tableName'];
			}

			if (!class_exists($parentClass, TRUE)) {
				$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
					'Mapping configuration error in domain object ' . $domainObject->getName() . ': the parent class ' . $parentClass . 'seems not to exist ',
					self::ERROR_MAPPING_NO_PARENTCLASS
				);
			}
		}
		if ($tableName) {
			if (strpos($extensionPrefix, $tableName) !== FALSE) {
				// the domainObject extends a class of the same extension
				if (!$parentClass) {
					$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
						'Mapping configuration error in domain object ' . $domainObject->getName() . ': you have to define a parent class if you map to a table of another domain object of the same extension ',
						self::ERROR_MAPPING_NO_PARENTCLASS
					);
				}
			}
			if (!isset($GLOBALS['TCA'][$tableName])) {
				$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
					'There is no entry for table "' . $tableName . '" of ' . $domainObject->getName() . ' in TCA. For technical reasons you can only extend tables with TCA configuration.',
					self::ERROR_MAPPING_NO_TCA
				);
			}
		}
		if (isset($GLOBALS['TCA'][$tableName]['ctrl']['type'])) {
			$dataTypeRes = $GLOBALS['TYPO3_DB']->sql_query('DESCRIBE ' . $tableName);
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dataTypeRes)) {
				if($row['Field'] == $GLOBALS['TCA'][$tableName]['ctrl']['type']) {
					if(strpos($row['Type'],'int') !== FALSE) {
						$this->validationResult['warnings'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
							'The configured type field for table "' . $tableName . '" is of type ' .$row['Type'] . '<br />This means the type field can not be used for defining the record type. <br />You have to configure the mappings yourself if you want to map to this<br /> table or extend the correlated class',
							self::ERROR_MAPPING_WRONG_TYPEFIELD_CONFIGURATION
						);
					}
				}
			}
		}
	}

	/**$actions = $domainObject->getActions();
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 * @return void
	 */
	private function validateDomainObjectActions(Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject) {
		$actionNames = array();
		$actions = $domainObject->getActions();
		foreach ($actions as $action) {
			if (in_array($action->getName(), $actionNames)) {
				$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
					'Duplicate action name "' . $action->getName() . '" of ' . $domainObject->getName() . '. Action names have to be unique for each model',
					self::ERROR_ACTIONNAME_DUPLICATE
				);
			}
			/**
			 * Character test
			 * Allowed characters are: a-z (lowercase), A-Z (uppercase) and 0-9
			 */
			if (!preg_match("/^[a-zA-Z0-9]*$/", $action->getName())) {
				$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
					'Illegal action name "' . $action->getName() . '" of ' . $domainObject->getName() . '. Please use lowerCamelCase, no spaces or underscores.',
					self::ERROR_ACTIONNAME_ILLEGAL_CHARACTER
				);
			}
			$actionNames[] = $action->getName();
		}
		$this->validateDependentActions($actionNames, 'Domain object ' . $domainObject->getName());

		$firstAction = reset($actionNames);
		if ($firstAction == 'show' || $firstAction == 'edit' || $firstAction == 'delete') {
			$this->validationResult['warnings'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
				'Potential misconfiguration in Domain object ' . $domainObject->getName() . ':<br />First action could not be default action since "' . $firstAction . '" action needs a parameter',
				self::ERROR_MISCONFIGURATION
			);
		}
	}


	/**
	 * @author Sebastian Michaelsen <sebastian.gebhard@gmail.com>
	 * @param	Tx_ExtensionBuilder_Domain_Model_DomainObject
	 * @return	 void
	 * @throws Tx_ExtensionBuilder_Domain_Exception_ExtensionException
	 */
	private function validateProperties($domainObject) {
		$propertyNames = array();
		foreach ($domainObject->getProperties() as $property) {
			// Check if property name is given
			if (!$property->getName()) {
				$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException('A property of ' . $domainObject->getName() . ' has no name', self::ERROR_PROPERTY_NO_NAME);
			}
			$propertyName = $property->getName();
			/**
			 * Character test
			 * Allowed characters are: a-z (lowercase), A-Z (uppercase) and 0-9
			 */
			if (!preg_match("/^[a-zA-Z0-9]*$/", $propertyName)) {
				$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
					'Illegal property name "' . $propertyName . '" of ' . $domainObject->getName() . '. Please use lowerCamelCase, no spaces or underscores.',
					self::ERROR_PROPERTY_ILLEGAL_CHARACTER
				);
			}


			$firstChar = $propertyName{0};
			if (strtoupper($firstChar) == $firstChar) {
				$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
					'Illegal first character of property name "' . $property->getName() . '" of domain object "' . $domainObject->getName() . '". Please use lowerCamelCase.',
					self::ERROR_PROPERTY_UPPER_FIRST_CHARACTER
				);
			}

			if (self::isReservedTYPO3Word($propertyName)) {
				$this->validationResult['warnings'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
					'The name of property "' . $propertyName . '" in Model "' . $domainObject->getName() . '" will result in a TYPO3 specific column name.<br /> This might result in unexpected behaviour. If you didn\'t choose that name by purpose<br /> it is recommended to use another name',
					self::ERROR_PROPERTY_RESERVED_WORD
				);
			}

			if (self::isReservedMYSQLWord($propertyName)) {
				$this->validationResult['warnings'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
					'Property "' . $propertyName . '" in Model "' . $domainObject->getName() . '".',
					self::ERROR_PROPERTY_RESERVED_SQL_WORD
				);
			}

			// Check for duplicate property names
			if (in_array($propertyName, $propertyNames)) {
				$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException('Property "' . $property->getName() . '" of ' . $domainObject->getName() . ' exists twice.', self::ERROR_PROPERTY_DUPLICATE);
			}
			$propertyNames[] = $propertyName;

			if( is_subclass_of($property, Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_AbstractRelation)) {
				if(!$property->getForeignModel() && $property->getForeignClassName()){
					if(!class_exists($property->getForeignClassName())) {
						$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
							'Related class not loadable: "' . $property->getForeignClassName() . '" configured in relation "' .$property->getName() . '".',
							self::ERROR_MAPPING_NO_FOREIGNCLASS
						);
					}
				}
				if($property->getForeignModel() && ($property->getForeignModel()->getFullQualifiedClassName() != $property->getForeignClassName())){
					$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException(
						'Relation "' .$property->getName() . '" in model "' . $domainObject->getName() . '" has a external class relation and a wire to '.$property->getForeignModel()->getName() ,
						self::ERROR_MAPPING_WIRE_AND_FOREIGNCLASS
					);
				}
			}

		}
	}

	/**
	 * validates a plugin key
	 * @param string $key
	 * @return boolean TRUE if valid
	 */
	private static function validatePluginKey($key) {
		return preg_match('/^[a-zA-Z0-9_-]*$/', $key);
	}

	/**
	 * validates a backend module key
	 * @param string $key
	 * @return boolean TRUE if valid
	 */
	private static function validateModuleKey($key) {
		return preg_match('/^[a-zA-Z0-9_-]*$/', $key);
	}

	/**
	 * @author Rens Admiraal
	 * @param string $key
	 * @return void
	 * @throws Tx_ExtensionBuilder_Domain_Exception_ExtensionException
	 */
	private function validateExtensionKey($key) {
		/**
		 * Character test
		 * Allowed characters are: a-z (lowercase), 0-9 and '_' (underscore)
		 */
		if (!preg_match("/^[a-z0-9_]*$/", $key)) {
			$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException('Illegal characters in extension key', self::ERROR_EXTKEY_ILLEGAL_CHARACTERS);
		}

		/**
		 * Start character
		 * Extension keys cannot start or end with 0-9 and '_' (underscore)
		 */
		if (preg_match("/^[0-9_]/", $key)) {
			$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException('Illegal first character of extension key', self::ERROR_EXTKEY_ILLEGAL_FIRST_CHARACTER);
		}

		/**
		 * Extension key length
		 * An extension key must have minimum 3, maximum 30 characters (not counting underscores)
		 */
		$keyLengthTest = str_replace('_', '', $key);
		if (strlen($keyLengthTest) < 3 || strlen($keyLengthTest) > 30) {
			$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException('Invalid extension key length', self::ERROR_EXTKEY_LENGTH);
		}

		/**
		 * Reserved prefixes
		 * The key must not being with one of the following prefixes: tx,u,user_,pages,tt_,sys_,ts_language_,csh_
		 */
		if (preg_match("/^(tx_|u_|user_|pages_|tt_|sys_|ts_language_|csh_)/", $key)) {
			$this->validationResult['errors'][] = new Tx_ExtensionBuilder_Domain_Exception_ExtensionException('Illegal extension key prefix', self::ERROR_EXTKEY_ILLEGAL_PREFIX);
		}
	}

	/**
	 *
	 * @param string $word
	 *
	 * @return boolean
	 */
	static public function isReservedTYPO3Word($word) {
		if (in_array(\TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($word), self::$reservedTYPO3ColumnNames)) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	/**
	 *
	 * @param string $word
	 *
	 * @return boolean
	 */
	static public function isReservedMYSQLWord($word) {
		if (in_array(strtoupper($word), self::$reservedMYSQLWords)) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	/**
	 *
	 * @param string $word
	 *
	 * @return boolean
	 */
	static public function isReservedWord($word) {
		if (self::isReservedMYSQLWord($word) || self::isReservedTYPO3Word($word)) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
}

?>
