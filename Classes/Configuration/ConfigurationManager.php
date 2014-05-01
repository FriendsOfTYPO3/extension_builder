<?php
namespace EBT\ExtensionBuilder\Configuration;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nico de Haen
 *  All rights reserved
 *
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
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Http\AjaxRequestHandler;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Load settings from yaml file and from TYPO3_CONF_VARS extConf
 */
class ConfigurationManager extends \TYPO3\CMS\Extbase\Configuration\ConfigurationManager {

	/**
	 * @var string
	 */
	const SETTINGS_DIR = 'Configuration/ExtensionBuilder/';


	/**
	 * @var string
	 */
	const EXTENSION_BUILDER_SETTINGS_FILE = 'ExtensionBuilder.json';

	/**
	 * @var array
	 */
	private $inputData = array();

	/**
	 * Wrapper for file_get_contents('php://input')
	 *
	 * @return void
	 */
	public function parseRequest() {
		$jsonString = file_get_contents('php://input');
		$this->inputData = json_decode($jsonString, TRUE);
	}

	/**
	 * @return mixed
	 */
	public function getParamsFromRequest() {
		$params = $this->inputData['params'];
		return $params;
	}

	/**
	 * Reads the configuration from this->inputData and returns it as array.
	 *
	 * @throws \Exception
	 * @return array
	 */
	public function getConfigurationFromModeler() {
		if (empty($this->inputData)) {
			throw new \Exception('No inputData!');
		}
		$extensionConfigurationJson = json_decode($this->inputData['params']['working'], TRUE);
		$extensionConfigurationJson = $this->reArrangeRelations($extensionConfigurationJson);
		$extensionConfigurationJson['modules'] = $this->checkForAbsoluteClassNames($extensionConfigurationJson['modules']);
		return $extensionConfigurationJson;
	}

	/**
	 * @return mixed
	 */
	public function getSubActionFromRequest() {
		$subAction = $this->inputData['method'];
		return $subAction;
	}

	/**
	 * Set settings from various sources:
	 *
	 * - Settings configured in module.extension_builder typoscript
	 * - Module settings configured in the extension manager
	 *
	 * @param array $typoscript (optional)
	 */
	public function getSettings($typoscript = NULL) {
		if ($typoscript == NULL) {
			$typoscript = $this->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		}
		$settings = $typoscript['module.']['extension_builder.']['settings.'];
		if (empty($settings['codeTemplateRootPath'])) {
			$settings['codeTemplateRootPath'] = 'EXT:extension_builder/Resources/Private/CodeTemplates/Extbase/';
		}
		$settings['codeTemplateRootPath'] = self::substituteExtensionPath($settings['codeTemplateRootPath']);
		$settings['extConf'] = $this->getExtensionBuilderSettings();
		return $settings;
	}

	/**
	 * Get the extension_builder configuration (ext_template_conf).
	 *
	 * @return array
	 */
	public function getExtensionBuilderSettings() {
		$settings = array();
		if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extension_builder'])) {
			$settings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extension_builder']);
		}
		return $settings;
	}

	/**
	 * @param string $extensionKey
	 * @return array settings
	 */
	public function getExtensionSettings($extensionKey) {
		$settings = array();
		$settingsFile = $this->getSettingsFile($extensionKey);
		if (file_exists($settingsFile)) {
			$yamlParser = new \EBT\ExtensionBuilder\Utility\SpycYAMLParser();
			$settings = $yamlParser->YAMLLoadString(file_get_contents($settingsFile));
		} else {
			GeneralUtility::devlog('No settings found: ' . $settingsFile, 'extension_builder', 2);
		}

		return $settings;
	}

	/**
	 * Reads the stored configuration (i.e. the extension model etc.).
	 *
	 * @param string $extensionKey
	 * @return array|NULL
	 */
	public function getExtensionBuilderConfiguration($extensionKey) {
		$result = NULL;

		$jsonFile = PATH_typo3conf . 'ext/' . $extensionKey . '/' . self::EXTENSION_BUILDER_SETTINGS_FILE;

		if (file_exists($jsonFile)) {
			$extensionConfigurationJson = json_decode(file_get_contents($jsonFile), TRUE);
			$extensionConfigurationJson = $this->fixExtensionBuilderJSON($extensionConfigurationJson);
			$extensionConfigurationJson['properties']['originalExtensionKey'] = $extensionKey;
			if (floatval($extensionConfigurationJson['log']['extension_builder_version']) >= 2.5) {
				$result = $extensionConfigurationJson;
			}
		}

		return $result;
	}

	/**
	 * This is mainly copied from DataMapFactory.
	 *
	 * @param string $className
	 * @return array with configuration values
	 */
	public function getExtbaseClassConfiguration($className) {
		$classConfiguration = array();
		if (strpos($className, '\\') === 0) {
			$className = substr($className, 1);
		}
		$frameworkConfiguration = $this->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		$classSettings = $frameworkConfiguration['persistence']['classes'][$className];
		if ($classSettings !== NULL) {
			if (isset($classSettings['subclasses']) && is_array($classSettings['subclasses'])) {
				$classConfiguration['subclasses'] = $classSettings['subclasses'];
			}
			if (isset($classSettings['mapping']['recordType']) && strlen($classSettings['mapping']['recordType']) > 0) {
				$classConfiguration['recordType'] = $classSettings['mapping']['recordType'];
			}
			if (isset($classSettings['mapping']['tableName']) && strlen($classSettings['mapping']['tableName']) > 0) {
				$classConfiguration['tableName'] = $classSettings['mapping']['tableName'];
			}
			$classHierachy = array_merge(array($className), class_parents($className));
			$columnMapping = array();
			foreach ($classHierachy as $currentClassName) {
				if (in_array(
					$currentClassName,
					array(
						'TYPO3\\CMS\\Extbase\\DomainObject\\AbstractEntity',
						'TYPO3\\CMS\\Extbase\\DomainObject\\AbstractValueObject'
					)
				)) {
					break;
				}
				$currentClassSettings = $frameworkConfiguration['persistence']['classes'][$currentClassName];
				if ($currentClassSettings !== NULL) {
					if (isset($currentClassSettings['mapping']['columns']) && is_array($currentClassSettings['mapping']['columns'])) {
						GeneralUtility::array_merge_recursive_overrule(
							$columnMapping,
							$currentClassSettings['mapping']['columns'],
							0,
								// FALSE means: do not include empty values form 2nd array
							FALSE
						);
					}
				}
			}
		}
		return $classConfiguration;
	}

	/**
	 * Get the file name and path of the settings file.
	 *
	 * @param string $extensionKey
	 * @return string path
	 */
	public function getSettingsFile($extensionKey) {
		$extensionDir = PATH_typo3conf . 'ext/' . $extensionKey . '/';
		return $extensionDir . self::SETTINGS_DIR . 'settings.yaml';
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
	 * @param string $codeTemplateRootPath
	 * @return void
	 */
	public function createInitialSettingsFile($extension, $codeTemplateRootPath) {
		GeneralUtility::mkdir_deep($extension->getExtensionDir(), self::SETTINGS_DIR);
		$settings = file_get_contents($codeTemplateRootPath . 'Configuration/ExtensionBuilder/settings.yamlt');
		$settings = str_replace('{extension.extensionKey}', $extension->getExtensionKey(), $settings);
		$settings = str_replace('{f:format.date(format:\'Y-m-d\\TH:i:s\\Z\',date:\'now\')}', date('Y-m-d\TH:i:s\Z'), $settings);
		GeneralUtility::writeFile(
			$extension->getExtensionDir() . self::SETTINGS_DIR . 'settings.yaml', $settings
		);
	}

	/**
	 * Replace the EXT:extkey prefix with the appropriate path.
	 *
	 * @param string $encodedTemplateRootPath
	 * @return string
	 */
	static public function substituteExtensionPath($encodedTemplateRootPath) {
		$result = '';

		if (GeneralUtility::isFirstPartOfStr($encodedTemplateRootPath, 'EXT:')) {
			list($extKey, $script) = explode('/', substr($encodedTemplateRootPath, 4), 2);
			if ($extKey && ExtensionManagementUtility::isLoaded($extKey)) {
				$result = ExtensionManagementUtility::extPath($extKey) . $script;
			}
		} elseif (GeneralUtility::isAbsPath($encodedTemplateRootPath)) {
			$result = $encodedTemplateRootPath;
		} else {
			$result = PATH_site . $encodedTemplateRootPath;
		}

		return $result;
	}

	/**
	 * Performs various fixes/workarounds for wireit limitations.
	 *
	 * @param array $extensionConfigurationJson
	 * @return array the modified configuration
	 */
	public function fixExtensionBuilderJSON($extensionConfigurationJson) {
		$extensionConfigurationJson['modules'] = $this->resetOutboundedPositions($extensionConfigurationJson['modules']);
		$extensionConfigurationJson['modules'] = $this->mapAdvancedMode($extensionConfigurationJson['modules']);
		$extensionConfigurationJson = $this->reArrangeRelations($extensionConfigurationJson);
		return $extensionConfigurationJson;
	}


	/**
	 * Copy values from simple mode fieldset to advanced fieldset.
	 *
	 * Enables compatibility with JSON from older versions of the extension builder.
	 *
	 * @param array $jsonConfig
	 * @param boolean $prepareForModeler
	 *
	 * @return array modified json
	 */
	protected function mapAdvancedMode($jsonConfig, $prepareForModeler = TRUE) {
		$fieldsToMap = array(
			'relationType',
			'propertyIsExcludeField',
			'propertyIsExcludeField',
			'lazyLoading',
			'relationDescription',
			'foreignRelationClass'
		);
		foreach ($jsonConfig as &$module) {
			for ($i = 0; $i < count($module['value']['relationGroup']['relations']); $i++) {
				if ($prepareForModeler) {
					if (empty($module['value']['relationGroup']['relations'][$i]['advancedSettings'])) {
						$module['value']['relationGroup']['relations'][$i]['advancedSettings'] = array();
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
	protected function checkForAbsoluteClassNames($moduleConfig) {
		foreach ($moduleConfig as &$module) {
			if (!empty($module['value']['objectsettings']['parentClass'])
					&& strpos($module['value']['objectsettings']['parentClass'], '\\') !== 0) {
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
	 * @return boolean
	 */
	public function isConfirmed($identifier) {
		if (isset($this->inputData['params'][$identifier]) &&
				$this->inputData['params'][$identifier] == 1
		) {
			return TRUE;
		}
		return FALSE;
	}


	/**
	 * Just a temporary workaround until the new UI is available.
	 *
	 * @param array $jsonConfig
	 * @return array
	 */
	protected function resetOutboundedPositions($jsonConfig) {
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
	protected function reArrangeRelations($jsonConfig) {
		foreach ($jsonConfig['wires'] as &$wire) {
			// format: relation_1
			$parts = explode('_', $wire['src']['terminal']);
			$supposedRelationIndex = $parts[1];
			$uid = $wire['src']['uid'];
			$wire['src'] = self::findModuleIndexByRelationUid(
				$wire['src']['uid'],
				$jsonConfig['modules'],
				$wire['src']['moduleId'],
				$supposedRelationIndex
			);
			$wire['src']['uid'] = $uid;

			$uid = $wire['tgt']['uid'];
			$wire['tgt'] = self::findModuleIndexByRelationUid(
				$wire['tgt']['uid'],
				$jsonConfig['modules'],
				$wire['tgt']['moduleId']
			);
			$wire['tgt']['uid'] = $uid;
		}
		return $jsonConfig;
	}

	/**
	 * @param int $uid
	 * @param array $modules
	 * @param int $supposedModuleIndex
	 * @param int $supposedRelationIndex
	 * @return array
	 */
	protected function findModuleIndexByRelationUid($uid, $modules, $supposedModuleIndex, $supposedRelationIndex = NULL) {
		$result = array(
			'moduleId' => $supposedModuleIndex
		);
		if ($supposedRelationIndex == NULL) {
			$result['terminal'] = 'SOURCES';
			if ($modules[$supposedModuleIndex]['value']['objectsettings']['uid'] == $uid) {
				// everything as expected
				return $result;
			} else {
				$moduleCounter = 0;
				foreach ($modules as $module) {
					if ($module['value']['objectsettings']['uid'] == $uid) {
						$result['moduleId'] = $moduleCounter;
						return $result;
					}
				}
			}
		} elseif ($modules[$supposedModuleIndex]['value']['relationGroup']['relations'][$supposedRelationIndex]['uid'] == $uid) {
			$result['terminal'] = 'relationWire_' . $supposedRelationIndex;
				// everything as expected
			return $result;
		} else {
			$moduleCounter = 0;
			foreach ($modules as $module) {
				$relationCounter = 0;
				foreach ($module['value']['relationGroup']['relations'] as $relation) {
					if ($relation['uid'] == $uid) {
						$result['moduleId'] = $moduleCounter;
						$result['terminal'] = 'relationWire_' . $relationCounter;
						return $result;
					}
					$relationCounter++;
				}
				$moduleCounter++;
			}
		}
		return $result;
	}

	public function getParentClassForValueObject($extensionKey) {
		$settings = self::getExtensionSettings($extensionKey);
		if (isset($settings['classBuilder']['Model']['AbstractValueObject']['parentClass'])) {
			$parentClass = $settings['classBuilder']['Model']['AbstractValueObject']['parentClass'];
		} else {
			$parentClass = '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractValueObject';
		}
		return $parentClass;
	}

	public function getParentClassForEntityObject($extensionKey) {
		$settings = self::getExtensionSettings($extensionKey);
		if (isset($settings['classBuilder']['Model']['AbstractEntity']['parentClass'])) {
			$parentClass = $settings['classBuilder']['Model']['AbstractEntity']['parentClass'];
		} else {
			$parentClass = '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractEntity';
		}
		return $parentClass;
	}

	/**
	 * Ajax callback that reads the smd file and modiefies the target URL to include
	 * the module token.
	 *
	 * @param array $parameters (unused)
	 * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxRequestHandler
	 * @return void
	 */
	public function getWiringEditorSmd(array $parameters, AjaxRequestHandler $ajaxRequestHandler) {
		$smdJsonString = file_get_contents(
			ExtensionManagementUtility::extPath('extension_builder') . 'Resources/Public/jsDomainModeling/phpBackend/WiringEditor.smd'
		);
		$smdJson = json_decode($smdJsonString);
		$parameters = array(
			'tx_extensionbuilder_tools_extensionbuilderextensionbuilder' => array(
				'controller' => 'BuilderModule',
				'action' => 'dispatchRpc',
			)
		);
		$smdJson->target = BackendUtility::getModuleUrl('tools_ExtensionBuilderExtensionbuilder', $parameters);
		$smdJsonString = json_encode($smdJson);
		$ajaxRequestHandler->setContent(array($smdJsonString));
	}
}