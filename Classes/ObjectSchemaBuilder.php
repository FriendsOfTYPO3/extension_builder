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
class Tx_ExtbaseKickstarter_ObjectSchemaBuilder {
	public function build(array $jsonArray) {
		$extension = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Domain_Model_Extension');
		$globalProperties = $jsonArray['properties'];
		if (!is_array($globalProperties)) throw new Exception('Wrong 1');


		$extension->setName($globalProperties['name']);
		$extension->setDescription($globalProperties['description']);
		$extension->setExtensionKey($globalProperties['extensionKey']);

		$state = 0;
		switch ($globalProperties['state']) {
			case 'development':
				$state = Tx_ExtbaseKickstarter_Domain_Model_Extension::STATE_DEVELOPMENT;
				break;
			case 'alpha':
				$state = Tx_ExtbaseKickstarter_Domain_Model_Extension::STATE_ALPHA;
				break;
			case 'beta':
				$state = Tx_ExtbaseKickstarter_Domain_Model_Extension::STATE_BETA;
				break;
			case 'stable':
				$state = Tx_ExtbaseKickstarter_Domain_Model_Extension::STATE_STABLE;
				break;
		}
		$extension->setState($state);


		// classes
		if (isset($jsonArray['modules'])) {
			foreach ($jsonArray['modules'] as $singleModule) {
				$domainObject = $this->buildDomainObject($singleModule['value']);
				$extension->addDomainObject($domainObject);
			}
		}

		// relations
		if (isset($jsonArray['wires'])) {
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
			$property = new $propertyClassName;
			$property->setName($jsonProperty['propertyName']);

			if (isset($jsonProperty['propertyIsRequired'])) {
				$property->setRequired($jsonProperty['propertyIsRequired']);
			}

			$domainObject->addProperty($property);
		}
		return $domainObject;
	}
}
?>
