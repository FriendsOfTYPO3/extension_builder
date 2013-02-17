<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Ingmar Schlecht
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
 * Builds an extension object based on the buildConfiguration
 */
class Tx_ExtensionBuilder_Service_ExtensionSchemaBuilder implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var Tx_ExtensionBuilder_Configuration_ConfigurationManager
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
	 * @var Tx_ExtensionBuilder_Service_ObjectSchemaBuilder
	 */
	protected $objectSchemaBuilder;

	/**
	 * @param Tx_ExtensionBuilder_Service_ObjectSchemaBuilder $objectSchemaBuilder
	 * @return void
	 */
	public function injectObjectSchemaBuilder(Tx_ExtensionBuilder_Service_ObjectSchemaBuilder $objectSchemaBuilder) {
		$this->objectSchemaBuilder = $objectSchemaBuilder;
	}

	/**
	 *
	 * @param array $extensionBuildConfiguration
	 * @return Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 */
	public function build(array $extensionBuildConfiguration) {
		$extension = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_ExtensionBuilder_Domain_Model_Extension');
		$globalProperties = $extensionBuildConfiguration['properties'];
		if (!is_array($globalProperties)) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Error: Extension properties not submitted! ' . $extension->getOriginalExtensionKey(), 'builder', 3, $globalProperties);
			throw new Exception('Extension properties not submitted!');
		}

		$this->setExtensionProperties($extension, $globalProperties);

		foreach ($globalProperties['persons'] as $personValues) {
			$person = $this->buildPerson($personValues);
			$extension->addPerson($person);
		}
		foreach ($globalProperties['plugins'] as $pluginValues) {
			$plugin = $this->buildPlugin($pluginValues);
			$extension->addPlugin($plugin);
		}

		foreach ($globalProperties['backendModules'] as $backendModuleValues) {
			$backendModule = $this->buildBackendModule($backendModuleValues);
			$extension->addBackendModule($backendModule);
		}

		// classes
		if (is_array($extensionBuildConfiguration['modules'])) {
			foreach ($extensionBuildConfiguration['modules'] as $singleModule) {
				$domainObject = $this->objectSchemaBuilder->build($singleModule['value']);
				if ($domainObject->isSubClass() && !$domainObject->isMappedToExistingTable()) {
						// we try to get the table from Extbase configuration
					$classSettings = $this->configurationManager->getExtbaseClassConfiguration($domainObject->getParentClass());
						//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('!isMappedToExistingTable:' . strtolower($domainObject->getParentClass()), 'extension_builder', 0, $classSettings);
					if (isset($classSettings['tableName'])) {
						$tableName = $classSettings['tableName'];
					} else {
						// we use the default table name
						$tableName = Tx_ExtensionBuilder_Utility_Tools::parseTableNameFromClassName($domainObject->getParentClass());
					}
					if (!isset($GLOBALS['TCA'][$tableName])) {
						throw new Exception('Table definitions for table ' . $tableName . ' could not be loaded. You can only map to tables with existing TCA or extend classes of installed extensions!');
					}
					$domainObject->setMapToTable($tableName);
				}
				$extension->addDomainObject($domainObject);
			}
			$classHierarchy = $extension->getClassHierarchy();
			foreach ($extension->getDomainObjects() as $domainObject) {
				if (isset($classHierarchy[$domainObject->getQualifiedClassName()])) {
					foreach ($classHierarchy[$domainObject->getQualifiedClassName()] as $directChild) {
						$domainObject->addChildObject($directChild);
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
	 * @param Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 * @throws Exception
	 */
	protected function setExtensionRelations($extensionBuildConfiguration, &$extension) {
		$existingRelations = array();
		foreach ($extensionBuildConfiguration['wires'] as $wire) {
			if ($wire['tgt']['terminal'] !== 'SOURCES') {
				if ($wire['src']['terminal'] == 'SOURCES') {
					// this happens if a relation wire was drawn from child to parent
					// swap the two arrays
					$tgtModuleId = $wire['src']['moduleId'];
					$wire['src'] = $wire['tgt'];
					$wire['tgt'] = array('moduleId' => $tgtModuleId, 'terminal' => 'SOURCES');
				}
				else {
					throw new Exception('A wire has always to connect a relation with a model, not with another relation');
				}
			}
			$srcModuleId = $wire['src']['moduleId'];
			$relationId = substr($wire['src']['terminal'], 13); // strip "relationWire_"
			$relationJsonConfiguration = $extensionBuildConfiguration['modules'][$srcModuleId]['value']['relationGroup']['relations'][$relationId];

			if (!is_array($relationJsonConfiguration)) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Error in JSON relation configuration!', 'extension_builder', 3, $extensionBuildConfiguration);
				$errorMessage = 'Missing relation config in domain object: ' . $extensionBuildConfiguration['modules'][$srcModuleId]['value']['name'];
				throw new Exception($errorMessage);
			}

			$foreignModelName = $extensionBuildConfiguration['modules'][$wire['tgt']['moduleId']]['value']['name'];
			$localModelName = $extensionBuildConfiguration['modules'][$wire['src']['moduleId']]['value']['name'];

			if (!isset($existingRelations[$localModelName])) {
				$existingRelations[$localModelName] = array();
			}
			$domainObject = $extension->getDomainObjectByName($localModelName);
			$relation = $domainObject->getPropertyByName($relationJsonConfiguration['relationName']);
			if (!$relation) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Relation not found: ' . $localModelName . '->' . $relationJsonConfiguration['relationName'],'extension_builder',2,$relationJsonConfiguration);
				throw new Exception('Relation not found: ' . $localModelName . '->' . $relationJsonConfiguration['relationName']);
			}
			// get unique foreign key names for multiple relations to the same foreign class
			if (in_array($foreignModelName, $existingRelations[$localModelName])) {
				if (is_a($relation, 'Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_ZeroToManyRelation')) {
					$relation->setForeignKeyName(strtolower($localModelName) . count($existingRelations[$localModelName]));
				}
				if(is_a($relation, 'Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_AnyToManyRelation')) {
					$relation->setUseExtendedRelationTableName(TRUE);
				}
			}
			$existingRelations[$localModelName][] = $foreignModelName;

			$relation->setForeignModel($extension->getDomainObjectByName($foreignModelName));
		}

	}


	/**
	 * @param Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 * @param array $propertyConfiguration
	 * @return void
	 */
	protected function setExtensionProperties(&$extension, $propertyConfiguration) {
		// name
		$extension->setName(trim($propertyConfiguration['name']));
		// description
		$extension->setDescription($propertyConfiguration['description']);
		// extensionKey
		$extension->setExtensionKey(trim($propertyConfiguration['extensionKey']));

		// vendorName
		$extension->setVendorName(trim($propertyConfiguration['vendorName']));

		if($propertyConfiguration['emConf']['disableVersioning']) {
			$extension->setSupportVersioning(FALSE);
		}

		// various extension properties
		$extension->setVersion($propertyConfiguration['emConf']['version']);

		if(!empty($propertyConfiguration['emConf']['dependsOn'])) {
			$dependencies = array();
			$lines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode("\n",$propertyConfiguration['emConf']['dependsOn']);
			foreach($lines as $line) {
				if(strpos($line, '=>')) {
					list($extensionKey,$version) = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('=>',$line);
					$dependencies[$extensionKey] = $version;
				}
			}
			$extension->setDependencies($dependencies);
		}

		if(!empty($propertyConfiguration['emConf']['targetVersion'])) {
			$extension->setTargetVersion(floatval($propertyConfiguration['emConf']['targetVersion']));
		}

		if (!empty($propertyConfiguration['emConf']['custom_category'])) {
			$category = $propertyConfiguration['emConf']['custom_category'];
		} else {
			$category = $propertyConfiguration['emConf']['category'];
		}

		$extension->setCategory($category);

		$extension->setShy($propertyConfiguration['emConf']['shy']);

		$extension->setPriority($propertyConfiguration['emConf']['priority']);

		// state
		$state = 0;
		switch ($propertyConfiguration['emConf']['state']) {
			case 'alpha':
				$state = Tx_ExtensionBuilder_Domain_Model_Extension::STATE_ALPHA;
				break;
			case 'beta':
				$state = Tx_ExtensionBuilder_Domain_Model_Extension::STATE_BETA;
				break;
			case 'stable':
				$state = Tx_ExtensionBuilder_Domain_Model_Extension::STATE_STABLE;
				break;
			case 'experimental':
				$state = Tx_ExtensionBuilder_Domain_Model_Extension::STATE_EXPERIMENTAL;
				break;
			case 'test':
				$state = Tx_ExtensionBuilder_Domain_Model_Extension::STATE_TEST;
				break;
		}
		$extension->setState($state);

		if (!empty($propertyConfiguration['originalExtensionKey'])) {
			// handle renaming of extensions
			// original extensionKey
			$extension->setOriginalExtensionKey($propertyConfiguration['originalExtensionKey']);
			\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Extension setOriginalExtensionKey:' . $extension->getOriginalExtensionKey(), 'extbase', 0, $propertyConfiguration);
		}

		if (!empty($propertyConfiguration['originalExtensionKey']) && $extension->getOriginalExtensionKey() != $extension->getExtensionKey()) {
			$settings = $this->configurationManager->getExtensionSettings($extension->getOriginalExtensionKey());
			// if an extension was renamed, a new extension dir is created and we
			// have to copy the old settings file to the new extension dir
			copy($this->configurationManager->getSettingsFile($extension->getOriginalExtensionKey()), $this->configurationManager->getSettingsFile($extension->getExtensionKey()));
		}
		else {
			$settings = $this->configurationManager->getExtensionSettings($extension->getExtensionKey());
		}

		if (!empty($settings)) {
			$extension->setSettings($settings);
			\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Extension settings:' . $extension->getExtensionKey(), 'extbase', 0, $extension->getSettings());
		}

	}

	/**
	 *
	 * @param array $personValues
	 * @return Tx_ExtensionBuilder_Domain_Model_Person
	 */
	protected function buildPerson($personValues) {
		$person = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_ExtensionBuilder_Domain_Model_Person');
		$person->setName($personValues['name']);
		$person->setRole($personValues['role']);
		$person->setEmail($personValues['email']);
		$person->setCompany($personValues['company']);
		return $person;
	}

	/**
	 *
	 * @param array $pluginValues
	 * @return Tx_ExtensionBuilder_Domain_Model_Plugin
	 */
	protected function buildPlugin($pluginValues) {
		$plugin = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_ExtensionBuilder_Domain_Model_Plugin');
		$plugin->setName($pluginValues['name']);
		$plugin->setType($pluginValues['type']);
		$plugin->setKey($pluginValues['key']);
		if (!empty($pluginValues['actions']['controllerActionCombinations'])) {
			$controllerActionCombinations = array();
			$lines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode("\n", $pluginValues['actions']['controllerActionCombinations'], TRUE);
			foreach ($lines as $line) {
				list($controllerName, $actionNames) = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('=>', $line);
				$controllerActionCombinations[$controllerName] = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $actionNames);
			}
			$plugin->setControllerActionCombinations($controllerActionCombinations);
		}
		if (!empty($pluginValues['actions']['noncacheableActions'])) {
			$noncacheableControllerActions = array();
			$lines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode("\n", $pluginValues['actions']['noncacheableActions'], TRUE);
			foreach ($lines as $line) {
				list($controllerName, $actionNames) = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('=>', $line);
				$noncacheableControllerActions[$controllerName] = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $actionNames);
			}
			$plugin->setNoncacheableControllerActions($noncacheableControllerActions);
		}
		if (!empty($pluginValues['actions']['switchableActions'])) {
			$switchableControllerActions = array();
			$lines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode("\n", $pluginValues['actions']['switchableActions'], TRUE);
			$switchableAction = array();
			foreach ($lines as $line) {
				if (strpos($line, '->') === FALSE) {
					if (isset($switchableAction['label'])) {
						// start a new array
						$switchableAction = array();
					}
					$switchableAction['label'] = trim($line);
				} else {
					$switchableAction['actions'] = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(';', $line, TRUE);
					$switchableControllerActions[] = $switchableAction;
				}
			}
			$plugin->setSwitchableControllerActions($switchableControllerActions);
		}
		return $plugin;
	}


	/**
	 *
	 * @param array $backendModuleValues
	 * @return Tx_ExtensionBuilder_Domain_Model_BackendModule
	 */
	protected function buildBackendModule($backendModuleValues) {
		$backendModule = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_ExtensionBuilder_Domain_Model_BackendModule');
		$backendModule->setName($backendModuleValues['name']);
		$backendModule->setMainModule($backendModuleValues['mainModule']);
		$backendModule->setTabLabel($backendModuleValues['tabLabel']);
		$backendModule->setKey($backendModuleValues['key']);
		$backendModule->setDescription($backendModuleValues['description']);
		if (!empty($backendModuleValues['actions']['controllerActionCombinations'])) {
			$controllerActionCombinations = array();
			$lines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode("\n", $backendModuleValues['actions']['controllerActionCombinations'], TRUE);
			foreach ($lines as $line) {
				list($controllerName, $actionNames) = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('=>', $line);
				$controllerActionCombinations[$controllerName] = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $actionNames);
			}
			$backendModule->setControllerActionCombinations($controllerActionCombinations);
		}
		return $backendModule;
	}
}

?>