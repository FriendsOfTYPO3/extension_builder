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
 * Creates a request an dispatches it to the controller which was specified
 * by TS Setup, Flexform and returns the content to the v4 framework.
 *
 * This class is the main entry point for extbase extensions in the frontend.
 *
 * @package ExtbaseKickstarter
 * @version $ID:$
 */
class Tx_ExtbaseKickstarter_Service_ExtensionSchemaBuilder implements t3lib_singleton {
	
	/**
	 * 
	 * @param array $jsonArray
	 * @return $extension
	 */
	public function build(array $jsonArray) {
		$extension = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Domain_Model_Extension');
		$globalProperties = $jsonArray['properties'];
		if (!is_array($globalProperties)){
			throw new Exception('Extension properties not submitted!');
		}

		// name
		$extension->setName(trim($globalProperties['name']));
		// description
		$extension->setDescription($globalProperties['description']);
		// extensionKey
		$extension->setExtensionKey(trim($globalProperties['extensionKey']));

		if(!empty($globalProperties['originalExtensionKey'])){
			// original extensionKey
			$extension->setOriginalExtensionKey($globalProperties['originalExtensionKey']);
			t3lib_div::devlog('Extension setOriginalExtensionKey:'.$extension->getOriginalExtensionKey(),'extbase',0,$globalProperties);
		}

		if(!empty($globalProperties['originalExtensionKey']) && $extension->getOriginalExtensionKey() != $extension->getExtensionKey()){
			$settings = Tx_ExtbaseKickstarter_Utility_ConfigurationManager::getExtensionSettings($extension->getOriginalExtensionKey());
			// if an extension was renamed, a new extension dir is created and we
			// have to copy the old settings file to the new extension dir
			copy(Tx_ExtbaseKickstarter_Utility_ConfigurationManager::getSettingsFile($extension->getOriginalExtensionKey()),Tx_ExtbaseKickstarter_Utility_ConfigurationManager::getSettingsFile($extension->getExtensionKey()));
		}
		else {
			$settings = Tx_ExtbaseKickstarter_Utility_ConfigurationManager::getExtensionSettings($extension->getExtensionKey());	
		}
		
		if(!empty($settings)){
			$extension->setSettings($settings);
			t3lib_div::devlog('Extension settings:'.$extension->getExtensionKey(),'extbase',0,$extension->getSettings());
		}
		
			// version
		$extension->setVersion($globalProperties['version']);
		
		foreach($globalProperties['persons'] as $personValues) {
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

			// state
		$state = 0;
		switch ($globalProperties['state']) {
			case 'alpha':
				$state = Tx_ExtbaseKickstarter_Domain_Model_Extension::STATE_ALPHA;
				break;
			case 'beta':
				$state = Tx_ExtbaseKickstarter_Domain_Model_Extension::STATE_BETA;
				break;
			case 'stable':
				$state = Tx_ExtbaseKickstarter_Domain_Model_Extension::STATE_STABLE;
				break;
			case 'experimental':
				$state = Tx_ExtbaseKickstarter_Domain_Model_Extension::STATE_EXPERIMENTAL;
				break;
			case 'test':
				$state = Tx_ExtbaseKickstarter_Domain_Model_Extension::STATE_TEST;
				break;
		}
		$extension->setState($state);


		// classes
		if (is_array($jsonArray['modules'])) {
			foreach ($jsonArray['modules'] as $singleModule) {
				$domainObject = Tx_ExtbaseKickstarter_Service_ObjectSchemaBuilder::build($singleModule['value']);
				$extension->addDomainObject($domainObject);
			}
		}

		// relations
		if (is_array($jsonArray['wires'])) {
			foreach ($jsonArray['wires'] as $wire) {
				
				if($wire['tgt']['terminal'] !== 'SOURCES'){
					if($wire['src']['terminal'] == 'SOURCES'){
						// this happens if a relation wire was drawn from child to parent
						// swap the two arrays
						$tgtModuleId = $wire['src']['moduleId'];
						$wire['src'] =  $wire['tgt'];
						$wire['tgt'] = array('moduleId'=>$tgtModuleId,'terminal' => 'SOURCES');
					}
					else {
						 throw new Exception('A wire has always to connect a relation with a model, not with another relation');
					}
				}
				$relationJsonConfiguration = $jsonArray['modules'][$wire['src']['moduleId']]['value']['relationGroup']['relations'][substr($wire['src']['terminal'], 13)];
				
				if (!is_array($relationJsonConfiguration)){
					t3lib_div::devlog('jsonArray:','extbase_kickstarter',3,$jsonArray);
					throw new Exception('Error. Relation JSON config was not found');
				}

				$foreignClassName = $jsonArray['modules'][$wire['tgt']['moduleId']]['value']['name'];
				$localClassName = $jsonArray['modules'][$wire['src']['moduleId']]['value']['name'];
				
				$relation = Tx_ExtbaseKickstarter_Service_ObjectSchemaBuilder::buildRelation($relationJsonConfiguration);
				
				$relation->setForeignClass($extension->getDomainObjectByName($foreignClassName));

				$extension->getDomainObjectByName($localClassName)->addProperty($relation);
			}
		}

		return $extension;
	}
	
	/**
	 * 
	 * @param array $personValues
	 * @return Tx_ExtbaseKickstarter_Domain_Model_Person
	 */
	protected function buildPerson($personValues){
		$person=t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Domain_Model_Person');
		$person->setName($personValues['name']);
		$person->setRole($personValues['role']);
		$person->setEmail($personValues['email']);
		$person->setCompany($personValues['company']);
		return $person;
	}
	
	/**
	 * 
	 * @param array $pluginValues
	 * @return Tx_ExtbaseKickstarter_Domain_Model_Plugin
	 */
	protected function buildPlugin($pluginValues){
		$plugin = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Domain_Model_Plugin');
		$plugin->setName($pluginValues['name']);
		$plugin->setType($pluginValues['type']);
		$plugin->setKey($pluginValues['key']);
		return $plugin;
	}
	
/**
	 * 
	 * @param array $backendModuleValues
	 * @return Tx_ExtbaseKickstarter_Domain_Model_BackendModule
	 */
	protected function buildBackendModule($backendModuleValues){
		$backendModule = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Domain_Model_BackendModule');
		$backendModule->setName($backendModuleValues['name']);
		$backendModule->setMainModule($backendModuleValues['mainModule']);
		$backendModule->setTabLabel($backendModuleValues['tabLabel']);
		$backendModule->setKey($backendModuleValues['key']);
		$backendModule->setDescription($backendModuleValues['description']);
		return $backendModule;
	}
}

?>