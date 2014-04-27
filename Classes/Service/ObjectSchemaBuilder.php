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
use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Utility\Tools;

/**
 * Builder for domain objects
 */
class ObjectSchemaBuilder implements \TYPO3\CMS\Core\SingletonInterface {
	/**
	 * @var \EBT\ExtensionBuilder\Configuration\ConfigurationManager
	 */
	protected $configurationManager = NULL;

	/**
	 * @var string[]
	 */
	protected $relatedForeignTables = array();

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
		$domainObject->setCategorizable($jsonDomainObject['objectsettings']['categorizable']);

			// extended settings
		if (!empty($jsonDomainObject['objectsettings']['mapToTable'])) {
			$domainObject->setMapToTable($jsonDomainObject['objectsettings']['mapToTable']);
		}
		if (!empty($jsonDomainObject['objectsettings']['parentClass'])) {
			$domainObject->setParentClass($jsonDomainObject['objectsettings']['parentClass']);
		}
			// properties
		if (isset($jsonDomainObject['propertyGroup']['properties'])) {

			foreach ($jsonDomainObject['propertyGroup']['properties'] as $propertyJsonConfiguration) {
				$propertyType = $propertyJsonConfiguration['propertyType'];
				if (in_array($propertyType, array('Image', 'File')) && !empty($propertyJsonConfiguration['maxItems']) && $propertyJsonConfiguration['maxItems'] > 1) {
					$propertyJsonConfiguration['relationType'] = 'zeroToMany';
					$propertyJsonConfiguration['relationName'] = $propertyJsonConfiguration['propertyName'];
					$propertyJsonConfiguration['relationDescription'] = $propertyJsonConfiguration['propertyDescription'];
					$propertyJsonConfiguration['foreignRelationClass'] = '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FileReference';
					$propertyJsonConfiguration['type'] = $propertyJsonConfiguration['propertyType'];
					$propertyJsonConfiguration['maxItems'] = $propertyJsonConfiguration['maxItems'];

					$property = $this->buildRelation($propertyJsonConfiguration, $domainObject);
				} else {
					$property = self::buildProperty($propertyJsonConfiguration);
				}
				$domainObject->addProperty($property);
			}
		}

		if (isset($jsonDomainObject['relationGroup']['relations'])) {
			foreach ($jsonDomainObject['relationGroup']['relations'] as $relationJsonConfiguration) {
				$relation = $this->buildRelation($relationJsonConfiguration, $domainObject);
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
	 * @param array $relationJsonConfiguration
	 * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
	 * @throws \Exception
	 * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation
	 */
	public function buildRelation($relationJsonConfiguration, $domainObject) {
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
		$relation->setType($relationJsonConfiguration['type']);

		if (!empty($relationJsonConfiguration['foreignRelationClass'])) {
			// relations without wires
			if (strpos($relationJsonConfiguration['foreignRelationClass'], '\\') > 0) {
				// add trailing slash if not set
				$relationJsonConfiguration['foreignRelationClass'] = '\\' . $relationJsonConfiguration['foreignRelationClass'];
			}
			$relation->setForeignClassName($relationJsonConfiguration['foreignRelationClass']);
			$relation->setRelatedToExternalModel(TRUE);
			$extbaseClassConfiguration = $this->configurationManager->getExtbaseClassConfiguration(
					$relationJsonConfiguration['foreignRelationClass']
			);
			if (isset($extbaseClassConfiguration['tableName'])) {
				$foreignDatabaseTableName = $extbaseClassConfiguration['tableName'];
				$this->relatedForeignTables[$foreignDatabaseTableName] = 1;
			} else {
				$foreignDatabaseTableName = Tools::parseTableNameFromClassName(
						$relationJsonConfiguration['foreignRelationClass']
				);
			}
			$relation->setForeignDatabaseTableName($foreignDatabaseTableName);
			if ($relation instanceof \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation) {
				$foreignKeyName = strtolower($domainObject->getName());
				if (\EBT\ExtensionBuilder\Service\ValidationService::isReservedMYSQLWord($foreignKeyName)) {
					$foreignKeyName = 'tx_' . $foreignKeyName;
				}
				if (isset($this->relatedForeignTables[$foreignDatabaseTableName])) {
					$foreignKeyName .= $this->relatedForeignTables[$foreignDatabaseTableName];
					$this->relatedForeignTables[$foreignDatabaseTableName] += 1;
				} else {
					$foreignDatabaseTableName = Tools::parseTableNameFromClassName(
							$relationJsonConfiguration['foreignRelationClass']
					);
				}
				$relation->setForeignDatabaseTableName($foreignDatabaseTableName);
			}
			if ($relation->isFileReference() && !empty($relationJsonConfiguration['maxItems'])) {
				/** @var $relation \EBT\ExtensionBuilder\Domain\Model\DomainObject\FileProperty */
				$relation->setMaxItems($relationJsonConfiguration['maxItems']);
			}
		}
		return $relation;
	}

	/**
	 * @param $propertyJsonConfiguration
	 * @return object
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	public static function buildProperty($propertyJsonConfiguration) {
		$propertyType = $propertyJsonConfiguration['propertyType'];
		$propertyClassName = 'EBT\\ExtensionBuilder\\Domain\Model\\DomainObject\\' . $propertyType . 'Property';
		if (!class_exists($propertyClassName)) {
			throw new \Exception('Property of type ' . $propertyType . ' not found');
		}
		$property = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($propertyClassName);
		$property->setUniqueIdentifier($propertyJsonConfiguration['uid']);
		$property->setName($propertyJsonConfiguration['propertyName']);
		$property->setDescription($propertyJsonConfiguration['propertyDescription']);

		if ($propertyType == 'File' && !empty($propertyJsonConfiguration['allowedFileTypes'])) {
			$property->setAllowedFileTypes($propertyJsonConfiguration['allowedFileTypes']);
		}

		if (isset($propertyJsonConfiguration['propertyIsRequired'])) {
			$property->setRequired($propertyJsonConfiguration['propertyIsRequired']);
		}
		if (isset($propertyJsonConfiguration['propertyIsExcludeField'])) {
			$property->setExcludeField($propertyJsonConfiguration['propertyIsExcludeField']);
		}
		if ($property->isFileReference() && !empty($propertyJsonConfiguration['maxItems'])) {
			$property->setMaxItems($propertyJsonConfiguration['maxItems']);
		}
		return $property;
	}
}
