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
 *
 * Builds an extension object based on the buildConfiguration
 * @package ExtensionBuilder
 *
 */
class Tx_ExtensionBuilder_Service_ExtensionSchemaBuilder implements t3lib_singleton {


	/**
	 * @param Tx_ExtensionBuilder_Configuration_ConfigurationManager $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_ExtensionBuilder_Configuration_ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 *
	 * @param array $extensionBuildConfiguration
	 * @return Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 */
	public function build(array $extensionBuildConfiguration) {
		$extension = t3lib_div::makeInstance('Tx_ExtensionBuilder_Domain_Model_Extension');
		$globalProperties = $extensionBuildConfiguration['properties'];
		if (!is_array($globalProperties)) {
			t3lib_div::devlog('Error: Extension properties not submitted! ' . $extension->getOriginalExtensionKey(), 'builder', 3, $globalProperties);
			throw new Exception('Extension properties not submitted!');
		}

		$this->setExtensionProperties($extension,$globalProperties);

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
				$domainObject = Tx_ExtensionBuilder_Service_ObjectSchemaBuilder::build($singleModule['value']);
				$extension->addDomainObject($domainObject);
			}
		}

		// relations
		if (is_array($extensionBuildConfiguration['wires'])) {
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
					t3lib_div::devlog('Error in JSON relation configuration!', 'extension_builder', 3, $extensionBuildConfiguration);
					$errorMessage = 'Missing relation config in domain object: ' . $extensionBuildConfiguration['modules'][$srcModuleId]['value']['name'];
					throw new Exception($errorMessage);
				}

				$foreignClassName = $extensionBuildConfiguration['modules'][$wire['tgt']['moduleId']]['value']['name'];
				$localClassName = $extensionBuildConfiguration['modules'][$wire['src']['moduleId']]['value']['name'];

				$relation = Tx_ExtensionBuilder_Service_ObjectSchemaBuilder::buildRelation($relationJsonConfiguration);

				$relation->setForeignClass($extension->getDomainObjectByName($foreignClassName));

				$extension->getDomainObjectByName($localClassName)->addProperty($relation);
			}
		}

		return $extension;
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


		// various extension properties
		$extension->setVersion($propertyConfiguration['emConf']['version']);

		if (!empty($propertyConfiguration['emConf']['custom_category'])) {
			$category = $propertyConfiguration['emConf']['custom_category'];
		} else  {
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
			t3lib_div::devlog('Extension setOriginalExtensionKey:' . $extension->getOriginalExtensionKey(), 'extbase', 0, $propertyConfiguration);
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
			t3lib_div::devlog('Extension settings:' . $extension->getExtensionKey(), 'extbase', 0, $extension->getSettings());
		}

	}

	/**
	 *
	 * @param array $personValues
	 * @return Tx_ExtensionBuilder_Domain_Model_Person
	 */
	protected function buildPerson($personValues) {
		$person = t3lib_div::makeInstance('Tx_ExtensionBuilder_Domain_Model_Person');
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
		$plugin = t3lib_div::makeInstance('Tx_ExtensionBuilder_Domain_Model_Plugin');
		$plugin->setName($pluginValues['name']);
		$plugin->setType($pluginValues['type']);
		$plugin->setKey($pluginValues['key']);
		if(!empty($pluginValues['cacheableActions'])){
			$cacheableControllerActions = array();
			$lines = explode("\n",$pluginValues['cacheableActions']);
			foreach($lines as $line){
				list($controller,$actions) = explode('=>',str_replace(' ','',$line));
				$cacheableControllerActions[] = array('controller'=>$controller,'actions'=>$actions);
			}
			$plugin->setCacheableControllerActions($cacheableControllerActions);
		}
		if(!empty($pluginValues['noncacheableActions'])){
			$noncacheableControllerActions = array();
			$lines = explode("\n",$pluginValues['noncacheableActions']);
			foreach($lines as $line){
				list($controller,$actions) = explode('=>',str_replace(' ','',$line));
				$noncacheableControllerActions[] = array('controller'=>$controller,'actions'=>$actions);
			}
			$plugin->setNoncacheableControllerActions($noncacheableControllerActions);
		}
		return $plugin;
	}


	/**
	 *
	 * @param array $backendModuleValues
	 * @return Tx_ExtensionBuilder_Domain_Model_BackendModule
	 */
	protected function buildBackendModule($backendModuleValues) {
		$backendModule = t3lib_div::makeInstance('Tx_ExtensionBuilder_Domain_Model_BackendModule');
		$backendModule->setName($backendModuleValues['name']);
		$backendModule->setMainModule($backendModuleValues['mainModule']);
		$backendModule->setTabLabel($backendModuleValues['tabLabel']);
		$backendModule->setKey($backendModuleValues['key']);
		$backendModule->setDescription($backendModuleValues['description']);
		return $backendModule;
	}
}

?>