<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Ingmar Schlecht
 *  (c) 2010 Nico de Haen
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
 * Builder for domain objects
 */
class Tx_ExtensionBuilder_Service_ObjectSchemaBuilder implements \TYPO3\CMS\Core\SingletonInterface {

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
	 *
	 * @param array $jsonDomainObject
	 * @return Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 */
	public function build(array $jsonDomainObject) {
		//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Building domain object '.$jsonDomainObject['name'],'extension_builder',0,$jsonDomainObject);
		$domainObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_ExtensionBuilder_Domain_Model_DomainObject');
		$domainObject->setUniqueIdentifier($jsonDomainObject['objectsettings']['uid']);

		$domainObject->setName($jsonDomainObject['name']);
		$domainObject->setDescription($jsonDomainObject['objectsettings']['description']);
		if ($jsonDomainObject['objectsettings']['type'] === 'Entity') {
			$domainObject->setEntity(TRUE);
		} else {
			$domainObject->setEntity(FALSE);
		}
		$domainObject->setAggregateRoot($jsonDomainObject['objectsettings']['aggregateRoot']);
		$domainObject->setSorting($jsonDomainObject['objectsettings']['sorting']);

		// extended settings

		if (!empty($jsonDomainObject['objectsettings']['mapToTable'])) {
			$domainObject->setMapToTable($jsonDomainObject['objectsettings']['mapToTable']);
		}
		if (!empty($jsonDomainObject['objectsettings']['parentClass'])) {
			$domainObject->setParentClass($jsonDomainObject['objectsettings']['parentClass']);
		}

		// properties

		foreach ($jsonDomainObject['propertyGroup']['properties'] as $jsonProperty) {
			$propertyType = $jsonProperty['propertyType'];
			$propertyClassName = 'Tx_ExtensionBuilder_Domain_Model_DomainObject_' . $propertyType . 'Property';
			if (!class_exists($propertyClassName)) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Property of type ' . $propertyType . ' not found', 'extension_builder', 2, $jsonProperty);
				throw new Exception('Property of type ' . $propertyType . ' not found');
			}
			$property = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($propertyClassName);
			$property->setUniqueIdentifier($jsonProperty['uid']);
			$property->setName($jsonProperty['propertyName']);
			$property->setDescription($jsonProperty['propertyDescription']);

			if (isset($jsonProperty['propertyIsRequired'])) {
				$property->setRequired($jsonProperty['propertyIsRequired']);
			}
			if (isset($jsonProperty['propertyIsExcludeField'])) {
				$property->setExcludeField($jsonProperty['propertyIsExcludeField']);
			}
			//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Adding property ' . $jsonProperty['propertyName'] . ' to domain object '.$jsonDomainObject['name'],'extension_builder',0,$jsonDomainObject);
			$domainObject->addProperty($property);
		}

		$relatedForeignTables = array();
		foreach ($jsonDomainObject['relationGroup']['relations'] as $jsonRelation) {
			$relation = self::buildRelation($jsonRelation);
			if (!empty($jsonRelation['foreignRelationClass'])) {
				// relations without wires
				if(strpos($jsonRelation['foreignRelationClass'], '\\') > 0) {
					// add trailing slash if not set
					$jsonRelation['foreignRelationClass'] = '\\' . $jsonRelation['foreignRelationClass'];
				}
				$relation->setForeignClassName($jsonRelation['foreignRelationClass']);
				$relation->setRelatedToExternalModel(TRUE);
				$extbaseClassConfiguration = $this->configurationManager->getExtbaseClassConfiguration($jsonRelation['foreignRelationClass']);
				if (isset($extbaseClassConfiguration['tableName'])) {
					$foreignDatabaseTableName = $extbaseClassConfiguration['tableName'];
				} else {
					$foreignDatabaseTableName = Tx_ExtensionBuilder_Utility_Tools::parseTableNameFromClassName($jsonRelation['foreignRelationClass']);
				}
				$relation->setForeignDatabaseTableName($foreignDatabaseTableName);
				if (is_a($relation, 'Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_ZeroToManyRelation')) {
					$foreignKeyName = strtolower($domainObject->getName());
					if (isset($relatedForeignTables[$foreignDatabaseTableName])) {
						$foreignKeyName .= $relatedForeignTables[$foreignDatabaseTableName];
						$relatedForeignTables[$foreignDatabaseTableName] += 1;
					} else {
						$relatedForeignTables[$foreignDatabaseTableName] = 1;
					}
					$relation->setForeignKeyName($foreignKeyName);
				}
			}
			\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Adding relation ' . $jsonRelation['relationName'] . ' to domain object '.$jsonDomainObject['name'],'extension_builder',0,$jsonRelation);
			$domainObject->addProperty($relation);
		}

		//actions

		foreach ($jsonDomainObject['actionGroup'] as $jsonActionName => $actionValue) {
			if ($jsonActionName == 'customActions' && !empty($actionValue)) {
				$actionNames = $actionValue;
			} else if ($actionValue == 1) {
				$jsonActionName = preg_replace('/^_default[0-9]_*/', '', $jsonActionName);
				if ($jsonActionName == 'edit_update' || $jsonActionName == 'new_create') {
					$actionNames = explode('_', $jsonActionName);
				} else {
					$actionNames = array($jsonActionName);
				}
			} else {
				$actionNames = array();
			}

			if (!empty($actionNames)) {
				foreach ($actionNames as $actionName) {
					$action = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_ExtensionBuilder_Domain_Model_DomainObject_Action');
					$action->setName($actionName);
					$domainObject->addAction($action);
				}
			}
		}


		return $domainObject;
	}

	/**
	 *
	 * @param $relationJsonConfiguration
	 * @return Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_AbstractRelation
	 */
	public static function buildRelation($relationJsonConfiguration) {
		$relationSchemaClassName = 'Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_' . ucfirst($relationJsonConfiguration['relationType']) . 'Relation';
		if (!class_exists($relationSchemaClassName)) {

			\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Relation misconfiguration','extension_builder',2,$relationJsonConfiguration);
			throw new Exception('Relation of type ' . $relationSchemaClassName . ' not found (configured in "' . $relationJsonConfiguration['relationName'] . '")');
		}
		$relation = new $relationSchemaClassName;
		$relation->setName($relationJsonConfiguration['relationName']);
		//$relation->setInlineEditing((bool)$relationJsonConfiguration['inlineEditing']);
		$relation->setLazyLoading((bool)$relationJsonConfiguration['lazyLoading']);
		$relation->setExcludeField($relationJsonConfiguration['propertyIsExcludeField']);
		$relation->setDescription($relationJsonConfiguration['relationDescription']);
		$relation->setUniqueIdentifier($relationJsonConfiguration['uid']);
		return $relation;
	}
}

?>