<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Ingmar Schlecht
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
class Tx_ExtbaseKickstarter_ObjectSchemaBuilder implements t3lib_Singleton {
	public function build(array $jsonArray) {
		$extension = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Domain_Model_Extension');
		$globalProperties = $jsonArray['properties'];
		if (!is_array($globalProperties)) throw new Exception('Wrong 1');


			// name
		$extension->setName($globalProperties['name']);
			// description
		$extension->setDescription($globalProperties['description']);
			// extensionKey
		$extension->setExtensionKey($globalProperties['extensionKey']);
		
		foreach($globalProperties['persons'] as $personValues) {
			$person=t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Domain_Model_Person');
			$person->setName($personValues['name']);
			$person->setRole($personValues['role']);
			$person->setEmail($personValues['email']);
			$person->setCompany($personValues['company']);
			$extension->addPerson($person);
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
				$domainObject = $this->buildDomainObject($singleModule['value']);
				$extension->addDomainObject($domainObject);
			}
		}

		// relations
		if (is_array($jsonArray['wires'])) {
			foreach ($jsonArray['wires'] as $wire) {
				$relationJsonConfiguration = $jsonArray['modules'][$wire['src']['moduleId']]['value']['relationGroup']['relations'][substr($wire['src']['terminal'], 13)];
				if (!is_array($relationJsonConfiguration)) throw new Exception('Error. Relation JSON config was not found');

				if ($wire['tgt']['terminal'] !== 'SOURCES') throw new Exception('Connections to other places than SOURCES not supported.');

				$foreignClassName = $jsonArray['modules'][$wire['tgt']['moduleId']]['value']['name'];
				$localClassName = $jsonArray['modules'][$wire['src']['moduleId']]['value']['name'];

				$relationSchemaClassName = 'Tx_ExtbaseKickstarter_Domain_Model_Property_Relation_' . ucfirst($relationJsonConfiguration['relationType']) . 'Relation';

				if (!class_exists($relationSchemaClassName)) throw new Exception('Relation of type ' . $relationSchemaClassName . ' not found');
				$relation = new $relationSchemaClassName;
				$relation->setName($relationJsonConfiguration['relationName']);
				$relation->setForeignClass($extension->getDomainObjectByName($foreignClassName));

				$extension->getDomainObjectByName($localClassName)->addProperty($relation);
			}
		}

		return $extension;
	}

	protected function buildDomainObject(array $jsonDomainObject) {
		$domainObject = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Domain_Model_DomainObject');
		$domainObject->setName($jsonDomainObject['name']);
		$domainObject->setDescription($jsonDomainObject['objectsettings']['description']);
		if ($jsonDomainObject['objectsettings']['type'] === 'Entity') {
			$domainObject->setEntity(TRUE);
		} else {
			$domainObject->setEntity(FALSE);
		}

		$domainObject->setAggregateRoot($jsonDomainObject['objectsettings']['aggregateRoot']);

		foreach ($jsonDomainObject['propertyGroup']['properties'] as $jsonProperty) {
			$propertyType = $jsonProperty['propertyType'];
			$propertyClassName = 'Tx_ExtbaseKickstarter_Domain_Model_Property_' . $propertyType . 'Property';
			if (!class_exists($propertyClassName)) throw new Exception('Property of type ' . $propertyType . ' not found');
			$property = t3lib_div::makeInstance($propertyClassName);
			$property->setName($jsonProperty['propertyName']);
			$property->setDescription($jsonProperty['propertyDescription']);

			if (isset($jsonProperty['propertyIsRequired'])) {
				$property->setRequired($jsonProperty['propertyIsRequired']);
			}

			$domainObject->addProperty($property);
		}
		
		foreach ($jsonDomainObject['actionGroup']['actions'] as $jsonAction) {
			$action = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Domain_Model_Action');
			$action->setName($jsonAction);
			
			$domainObject->addAction($action);
		}
		return $domainObject;
	}

	/**
	 * @return Tx_ExtbaseKickstarter_Domain_Model_DomainObject
	 */
	public function buildDomainObjectByReflection($extensionName, $domainObjectName) {
		$extension = new Tx_ExtbaseKickstarter_Domain_Model_Extension();

		$extension->setExtensionKey(t3lib_div::camelCaseToLowerCaseUnderscored($extensionName));

		$domainObject = new Tx_ExtbaseKickstarter_Domain_Model_DomainObject();
		$extension->addDomainObject($domainObject);
		$domainObject->setName($domainObjectName);

		$reflectionService = t3lib_div::makeInstance('Tx_Extbase_Reflection_Service');
		$classSchema = $reflectionService->getClassSchema(Tx_ExtbaseKickstarter_Utility_Naming::getDomainObjectClassName($extensionName, $domainObjectName));

		foreach ($classSchema->getProperties() as $propertyName => $propertyDescription) {
			if (in_array($propertyName, array('uid', '_localizedUid', '_languageUid'))) continue;
			$propertyType = 'Tx_ExtbaseKickstarter_Domain_Model_Property_' . $this->resolveKickstarterPropertyTypeFromPropertyDescription($propertyDescription['type'], $propertyDescription['elementType']);
			$property = new $propertyType;
			$property->setName($propertyName);
			$domainObject->addProperty($property);
		}

		return $domainObject;
	}


	/**
	 * See Tx_Extbase_Reflection_ClassSchema::ALLOWED_TYPES_PATTERN
	 */
	protected function resolveKickstarterPropertyTypeFromPropertyDescription($phpPropertyType, $elementType) {
		switch ($phpPropertyType) {
			case 'integer': return 'IntegerProperty';
			case 'float': return 'FloatProperty';
			case 'boolean': return 'BooleanProperty';
			case 'string' : return 'StringProperty';
			case 'DateTime': return 'DateTimeProperty';
			case 'array':
			case 'ArrayObject':
			case 'Tx_Extbase_Persistence_ObjectStorage':
				return 'Relation_ZeroToManyRelation'; // TODO: Is this correct?
			default:
			// Tx_*
				return 'Relation_ZeroToOneRelation';
		}
	}
}
?>
