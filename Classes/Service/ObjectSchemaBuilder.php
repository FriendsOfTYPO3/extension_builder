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
 * Creates a request an dispatches it to the controller which was specified
 * by TS Setup, Flexform and returns the content to the v4 framework.
 *
 * This class is the main entry point for extbase extensions in the frontend.
 *
 * @package ExtensionBuilder
 * @version $ID:$
 */
class Tx_ExtensionBuilder_Service_ObjectSchemaBuilder implements t3lib_singleton {

	/**
	 *
	 * @param array $jsonDomainObject
	 * @return Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 */
	static public function build(array $jsonDomainObject) {
		$domainObject = t3lib_div::makeInstance('Tx_ExtensionBuilder_Domain_Model_DomainObject');
		$domainObject->setUniqueIdentifier($jsonDomainObject['objectsettings']['uid']);

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
			$propertyClassName = 'Tx_ExtensionBuilder_Domain_Model_DomainObject_' . $propertyType . 'Property';
			if (!class_exists($propertyClassName)) throw new Exception('Property of type ' . $propertyType . ' not found');
			$property = t3lib_div::makeInstance($propertyClassName);
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

		if($domainObject->isAggregateRoot()){
			$defaultActions = array('list','show','new','create','edit','update','delete');
			foreach($defaultActions as $actionName){
				$action = t3lib_div::makeInstance('Tx_ExtensionBuilder_Domain_Model_DomainObject_Action');
				$action->setName($actionName);
				$domainObject->addAction($action);
			}

		}
		foreach ($jsonDomainObject['actionGroup']['actions'] as $jsonAction) {
			if($jsonAction == 'create'){
				$action = t3lib_div::makeInstance('Tx_ExtensionBuilder_Domain_Model_DomainObject_Action');
				$action->setName('new');
				$domainObject->addAction($action);
			}
			if($jsonAction == 'update'){
				$action = t3lib_div::makeInstance('Tx_ExtensionBuilder_Domain_Model_DomainObject_Action');
				$action->setName('edit');
				$domainObject->addAction($action);
			}
			$action = t3lib_div::makeInstance('Tx_ExtensionBuilder_Domain_Model_DomainObject_Action');
			$action->setName($jsonAction);
			$domainObject->addAction($action);

		}

		return $domainObject;
	}

	/**
	 *
	 * @param $relationJsonConfiguration
	 * @return Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_AbstractRelation
	 */
	public static function buildRelation($relationJsonConfiguration){
		$relationSchemaClassName = 'Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_' . ucfirst($relationJsonConfiguration['relationType']) . 'Relation';
		if (!class_exists($relationSchemaClassName)){
			throw new Exception('Relation of type ' . $relationSchemaClassName . ' not found');
		}
		$relation = new $relationSchemaClassName;
		$relation->setName($relationJsonConfiguration['relationName']);
		$relation->setInlineEditing((bool)$relationJsonConfiguration['inlineEditing']);
		$relation->setLazyLoading((bool)$relationJsonConfiguration['lazyLoading']);
		$relation->setDescription($relationJsonConfiguration['relationDescription']);
		$relation->setUniqueIdentifier($relationJsonConfiguration['uid']);
		return $relation;
	}
}

?>