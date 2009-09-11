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
		$extension->setState($globalProperties['state']);

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

			$domainObject->addProperty($property);
		}
		return $domainObject;
	}
}
?>
