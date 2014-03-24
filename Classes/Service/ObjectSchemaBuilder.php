<?php
namespace EBT\ExtensionBuilder\Service;
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
use EBT\ExtensionBuilder\Utility\Tools;

/**
 * Builder for domain objects
 */
class ObjectSchemaBuilder implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var \EBT\ExtensionBuilder\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @param \EBT\ExtensionBuilder\Configuration\ConfigurationManager
	 * @return void
	 */
	public function injectConfigurationManager(\EBT\ExtensionBuilder\Configuration\ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 *
	 * @param array $jsonDomainObject
	 * @throws \Exception
	 * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
	 */
	public function build(array $jsonDomainObject) {
		$domainObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
			'EBT\\ExtensionBuilder\\Domain\\Model\\DomainObject'
		);
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
		$domainObject->setAddDeletedField($jsonDomainObject['objectsettings']['addDeletedField']);
		$domainObject->setAddHiddenField($jsonDomainObject['objectsettings']['addHiddenField']);
		$domainObject->setAddStarttimeEndtimeFields($jsonDomainObject['objectsettings']['addStarttimeEndtimeFields']);

			// extended settings
		if (!empty($jsonDomainObject['objectsettings']['mapToTable'])) {
			$domainObject->setMapToTable($jsonDomainObject['objectsettings']['mapToTable']);
		}
		if (!empty($jsonDomainObject['objectsettings']['parentClass'])) {
			$domainObject->setParentClass($jsonDomainObject['objectsettings']['parentClass']);
		}
			// properties
		if (isset($jsonDomainObject['propertyGroup']['properties'])) {

			foreach ($jsonDomainObject['propertyGroup']['properties'] as $jsonProperty) {
				$propertyType = $jsonProperty['propertyType'];
				$propertyClassName = 'EBT\\ExtensionBuilder\\Domain\Model\\DomainObject\\' . $propertyType . 'Property';
				if (!class_exists($propertyClassName)) {
					throw new \Exception('Property of type ' . $propertyType . ' not found');
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
				$domainObject->addProperty($property);
			}
		}

		$relatedForeignTables = array();
		if (isset($jsonDomainObject['relationGroup']['relations'])) {
			foreach ($jsonDomainObject['relationGroup']['relations'] as $jsonRelation) {
				$relation = self::buildRelation($jsonRelation);
				if (!empty($jsonRelation['foreignRelationClass'])) {
					// relations without wires
					if (strpos($jsonRelation['foreignRelationClass'], '\\') > 0) {
						// add trailing slash if not set
						$jsonRelation['foreignRelationClass'] = '\\' . $jsonRelation['foreignRelationClass'];
					}
					$relation->setForeignClassName($jsonRelation['foreignRelationClass']);
					$relation->setRelatedToExternalModel(TRUE);
					$extbaseClassConfiguration = $this->configurationManager->getExtbaseClassConfiguration(
						$jsonRelation['foreignRelationClass']
					);
					if (isset($extbaseClassConfiguration['tableName'])) {
						$foreignDatabaseTableName = $extbaseClassConfiguration['tableName'];
						$relatedForeignTables[$foreignDatabaseTableName] = 1;
					} else {
						$foreignDatabaseTableName = Tools::parseTableNameFromClassName(
							$jsonRelation['foreignRelationClass']
						);
					}
					$relation->setForeignDatabaseTableName($foreignDatabaseTableName);
					if (is_a($relation, 'EBT\\ExtensionBuilder\\Domain\\Model\\DomainObject\\Relation\\ZeroToManyRelation')) {
						$foreignKeyName = strtolower($domainObject->getName());
						if (\EBT\ExtensionBuilder\Service\ValidationService::isReservedMYSQLWord($foreignKeyName)) {
							$foreignKeyName = 'tx_' . $foreignKeyName;
						}
						if (isset($relatedForeignTables[$foreignDatabaseTableName])) {
							$foreignKeyName .= $relatedForeignTables[$foreignDatabaseTableName];
							$relatedForeignTables[$foreignDatabaseTableName] += 1;
						} else {
							$foreignDatabaseTableName = Tools::parseTableNameFromClassName(
								$jsonRelation['foreignRelationClass']
							);
						}
						$relation->setForeignDatabaseTableName($foreignDatabaseTableName);
					}
				}
				$domainObject->addProperty($relation);
			}
		}

			//actions
		if (isset($jsonDomainObject['actionGroup'])) {
			foreach ($jsonDomainObject['actionGroup'] as $jsonActionName => $actionValue) {
				if ($jsonActionName == 'customActions' && !empty($actionValue)) {
					$actionNames = $actionValue;
				} elseif ($actionValue == 1) {
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
						$action = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
							'EBT\\ExtensionBuilder\\Domain\\Model\\DomainObject\\Action'
						);
						$action->setName($actionName);
						$domainObject->addAction($action);
					}
				}
			}
		}
		return $domainObject;
	}

	/**
	 *
	 * @param $relationJsonConfiguration
	 * @throws \Exception
	 * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation
	 */
	public static function buildRelation($relationJsonConfiguration) {
		$relationSchemaClassName = 'EBT\\ExtensionBuilder\\Domain\\Model\\DomainObject\\Relation\\';
		$relationSchemaClassName .= ucfirst($relationJsonConfiguration['relationType']) . 'Relation';
		if (!class_exists($relationSchemaClassName)) {
			throw new \Exception(
				'Relation of type ' . $relationSchemaClassName . ' not found (configured in "' .
					$relationJsonConfiguration['relationName'] . '")'
			);
		}
		/**
		 * @var $relation \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation
		 */
		$relation = new $relationSchemaClassName;
		$relation->setName($relationJsonConfiguration['relationName']);
		$relation->setLazyLoading((bool)$relationJsonConfiguration['lazyLoading']);
		$relation->setExcludeField($relationJsonConfiguration['propertyIsExcludeField']);
		$relation->setDescription($relationJsonConfiguration['relationDescription']);
		$relation->setUniqueIdentifier($relationJsonConfiguration['uid']);
		return $relation;
	}
}

?>