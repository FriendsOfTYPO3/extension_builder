<?php
namespace EBT\ExtensionBuilder\Domain\Model;

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
 * Schema for a Domain Object.
 */
class DomainObject {
	/**
	 * name of the domain object
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * @var string
	 */
	protected $uniqueIdentifier = '';

	/**
	 * description of the domain object
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * If TRUE, this is an aggregate root.
	 *
	 * @var bool
	 */
	protected $aggregateRoot = FALSE;

	/**
	 * If TRUE, the element is sortable in the TYPO3 backend.
	 *
	 * @var bool
	 */
	protected $sorting = FALSE;

	/**
	 * If TRUE, the related record has a "deleted" enable field.
	 *
	 * @var bool
	 */
	protected $addDeletedField = FALSE;

	/**
	 * If TRUE, the related record has a "hidden" enable field
	 *
	 * @var bool
	 */
	protected $addHiddenField = FALSE;

	/**
	 * If TRUE, the related record has a "starttime/endtime" enable field
	 *
	 * @var bool
	 */
	protected $addStarttimeEndtimeFields = FALSE;

	/**
	 * If TRUE, the element is categorizable in the TYPO3 backend
	 *
	 * @var bool
	 */
	protected $categorizable = FALSE;

	/**
	 * If TRUE, this is an entity. If FALSE, it is a ValueObject.
	 *
	 * @var bool
	 */
	protected $entity = FALSE;

	/**
	 * The extension this domain object belongs to.
	 *
	 * @var Extension
	 */
	protected $extension = NULL;

	/**
	 * List of properties the domain object has
	 * @var DomainObject\AbstractProperty[]
	 */
	protected $properties = array();

	/**
	 * List of actions the domain object has
	 * @var DomainObject\Action[]
	 */
	protected $actions = array();

	/**
	 * Is an upload folder required for this domain object
	 *
	 * @var bool
	 */
	protected $needsUploadFolder = FALSE;

	/**
	 * @var string
	 */
	protected $mapToTable = '';

	/**
	 * @var string
	 */
	protected $parentClass = '';

	/**
	 * Domain objects that extend the current object (as declared in this extension)
	 * @var DomainObject[]
	 */
	protected $childObjects = array();

	/**
	 * Set name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set name
	 * @param string $name Name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	public function getQualifiedClassName() {
		$qualifiedClassName = $this->extension->getNamespaceName() . '\\Domain\\Model\\' . $this->getName();
		if (strpos($qualifiedClassName, '\\') === 0) {
			$qualifiedClassName = substr($qualifiedClassName, 1);
		}
		return $qualifiedClassName;
	}

	public function getFullQualifiedClassName() {
		return '\\' . $this->getQualifiedClassName();
	}

	public function getControllerClassName() {
		return $this->extension->getNamespaceName() . '\\Controller\\' . $this->getName() . 'Controller';
	}

	public function getDatabaseTableName() {
		if (!empty($this->mapToTable)) {
			return $this->mapToTable;
		} else {
			return 'tx_' . strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($this->extension->getExtensionKey())) . '_domain_model_' . strtolower($this->getName());
		}
	}

	/**
	 * Get property uniqueIdentifier
	 *
	 * @return string
	 */
	public function getUniqueIdentifier() {
		return $this->uniqueIdentifier;
	}

	/**
	 * Set property uniqueIdentifier
	 *
	 * @param string Property uniqueIdentifier
	 */
	public function setUniqueIdentifier($uniqueIdentifier) {
		$this->uniqueIdentifier = $uniqueIdentifier;
	}

	/**
	 * Get description
	 * @return string
	 */
	public function getDescription() {
		if ($this->description) {
			return $this->description;
		} else {
			return $this->getName();
		}
	}

	/**
	 * Set description
	 * @param string $description Description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * get aggregate root
	 * @return boolean TRUE if it is an aggregate root, FALSE otherwise.
	 */
	public function getAggregateRoot() {
		return $this->aggregateRoot;
	}

	public function isAggregateRoot() {
		return $this->getAggregateRoot();
	}

	/**
	 * Setter for aggregate root flag
	 * @param boolean $aggregateRoot TRUE if Domain Object should be aggregate root.
	 */
	public function setAggregateRoot($aggregateRoot) {
		$this->aggregateRoot = (boolean)$aggregateRoot;
	}

	/**
	 *
	 * @return boolean TRUE if it is an entity, FALSE if it is a ValueObject
	 */
	public function getEntity() {
		return $this->entity;
	}

	/**
	 *
	 * @return boolean TRUE if it is an entity, FALSE if it is a ValueObject
	 */
	public function isEntity() {
		return $this->getEntity();
	}

	/**
	 *
	 * @param $entity $entity TRUE if it is an entity, FALSE if it is a ValueObject
	 *
	 * @return void
	 */
	public function setEntity($entity) {
		$this->entity = (boolean)$entity;
	}

	/**
	 * Adding a new property
	 * @param DomainObject\AbstractProperty $property The new property to add
	 *
	 * @return void
	 */
	public function addProperty(DomainObject\AbstractProperty $property) {
		$property->setDomainObject($this);
		if ($property->getNeedsUploadFolder()) {
			$this->needsUploadFolder = TRUE;
		}
		$this->properties[] = $property;
	}

	/**
	 * Get all properties
	 * @return array<DomainObject\AbstractProperty>
	 */
	public function getProperties() {
		return $this->properties;
	}

	/**
	 * Get property
	 *
	 * @return object <DomainObject\AbstractProperty>
	 */
	public function getPropertyByName($propertyName) {
		foreach ($this->properties as $property) {
			if ($property->getName() == $propertyName) {
				return $property;
			}
		}
		return NULL;
	}

	/**
	 * Get all properties holding relations of type Property_Relation_ZeroToManyRelation
	 *
	 * @return DomainObject\Relation\ZeroToManyRelation[]
	 */
	public function getZeroToManyRelationProperties() {
		$relationProperties = array();
		foreach ($this->properties as $property) {
			if (is_a($property, '\EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation')) {
				$relationProperties[] = $property;
			}
		}
		return $relationProperties;
	}

	/**
	 * Get all properties holding relations of type
	 * AnyToManyRelation
	 *
	 * @return DomainObject\Relation\AnyToManyRelation[]
	 */
	public function getAnyToManyRelationProperties() {
		$relationProperties = array();
		foreach ($this->properties as $property) {
			if (is_subclass_of($property, '\EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AnyToManyRelation')) {
				$relationProperties[] = $property;
			}
		}
		return $relationProperties;
	}

	/**
	 * Adding a new action
	 * @param DomainObject\Action $action The new action to add
	 *
	 * @return void
	 */
	public function addAction(DomainObject\Action $action) {
		$action->setDomainObject($this);
		if (!in_array($action, $this->actions)) {
			$this->actions[] = $action;
		}

	}

	/**
	 * Get all actions
	 *
	 * @return array<DomainObject\Action>
	 */
	public function getActions() {
		return $this->actions;
	}

	/**
	 * returns TRUE if the domainObject has actions
	 *
	 * @return boolean
	 */
	public function hasActions() {
		return count($this->actions) > 0;
	}

	/**
	 * DO NOT CALL DIRECTLY! This is being called by addDomainModel() automatically.
	 * @param Extension $extension the extension this domain model belongs to.
	 */
	public function setExtension(Extension $extension) {
		$this->extension = $extension;
	}

	/**
	 * @return Extension
	 */
	public function getExtension() {
		return $this->extension;
	}


	/**
	 * Get the base class for this Domain Object (different if it is entity or value object)
	 *
	 * @return string name of the base class
	 */
	public function getBaseClass() {
		if ($this->entity) {
			return '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractEntity';
		} else {
			return '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractValueObject';
		}
	}

	/**
	 * returns the name of the domain repository class name, if it is an aggregateroot.
	 *
	 * @return string
	 * @deprecated Use getFullyQualifiedDomainRepositoryClassName() instead
	 */
	public function getDomainRepositoryClassName() {
		if (!$this->aggregateRoot) return '';
		return '\\' . $this->extension->getNamespaceName() . '\\Domain\\Repository\\' . $this->getName() . 'Repository';
	}

	/**
	 * Returns the name of the domain repository class name, if it is an aggregate root.
	 *
	 * @return string
	 */
	public function getQualifiedDomainRepositoryClassName() {
		if (!$this->aggregateRoot) {
			return '';
		}

		return $this->extension->getNamespaceName() . '\\Domain\\Repository\\' . $this->getName() . 'Repository';
	}

	/**
	 * Returns the fully qualified name of the domain repository class name, if it is an aggregate root.
	 *
	 * @return string
	 */
	public function getFullyQualifiedDomainRepositoryClassName() {
		if (!$this->aggregateRoot) {
			return '';
		}

		return '\\' . $this->getQualifiedDomainRepositoryClassName();
	}


	/**
	 * @return string
	 */
	public function getCommaSeparatedFieldList() {
		$fieldNames = array();
		foreach ($this->properties as $property) {
			$fieldNames[] = $property->getFieldName();
		}
		return implode(',', $fieldNames);
	}

	/**
	 * Get the label to display in the list module.
	 * TODO: Needs to be configurable. Currently, the first property is the label in the backend.
	 * @return <type>
	 */
	public function getListModuleValueLabel() {
		if (isset($this->properties[0])) {
			return $this->properties[0]->getFieldName();
		} else {
			return 'uid';
		}
	}

	/**
	 * @return string
	 */
	public function getLabelNamespace() {
		return $this->extension->getShortExtensionKey() . '_domain_model_' . strtolower($this->getName());
	}

	/**
	 * @return bool
	 */
	public function getHasBooleanProperties() {
		foreach ($this->properties as $property) {
			if ($property->isBoolean()) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * @return array
	 */
	public function hasPropertiesThatNeedMappingStatements() {
		$propertiesWithMappingStatements = array();
		foreach ($this->properties as $property) {
			if ($property->getMappingStatement()) {
				$propertiesWithMappingStatements[] = $property;
			}
		}
		return $propertiesWithMappingStatements;
	}

	/**
	 * Getter for $needsUploadFolder
	 *
	 * @return boolean $needsUploadFolder
	 */
	public function getNeedsUploadFolder() {
		return $this->needsUploadFolder;
	}


	/**
	 * @return string
	 */
	public function getMapToTable() {
		return $this->mapToTable;
	}

	/**
	 * @param string $mapToTable
	 */
	public function setMapToTable($mapToTable) {
		$this->mapToTable = $mapToTable;
	}

	/**
	 * is this domain object mapped to an existing table?
	 * @return bool
	 */
	public function getNeedsMappingStatement() {
		if (!empty($this->mapToTable)) {
			return TRUE;
		}

		return $this->hasPropertiesThatNeedMappingStatements();
	}

	/**
	 * is this domain object mapped to a table?
	 * @return bool
	 */
	public function isMappedToExistingTable() {
		if (!empty($this->mapToTable)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getNeedsTableCtrlDefinition() {
		if ($this->mapToTable || $this->isSubClass()) {
			// ctrl definitions should already be defined in both cases
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * @param string $parentClass
	 */
	public function setParentClass($parentClass) {
		$this->parentClass = $parentClass;
	}

	/**
	 * @return string
	 */
	public function getParentClass() {
		return $this->parentClass;
	}

	/**
	 * @return string
	 */
	public function getRecordType() {
		$recordType = 'Tx_' .
				\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($this->extension->getExtensionKey()) . '_' .
				$this->getName();
		return $recordType;
	}

	/**
	 * @return bool
	 */
	public function isSubClass() {
		if (empty($this->parentClass)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}


	/**
	 * @param DomainObject $childObject
	 */
	public function addChildObject(DomainObject $childObject) {
		$this->childObjects[] = $childObject;
	}

	/**
	 * @return bool
	 */
	public function hasChildren() {
		return count($this->childObjects) > 0;
	}

	/**
	 * wrapper for fluid
	 * @return bool
	 */
	public function getHasChildren() {
		return $this->hasChildren();
	}

	/**
	 * @return array DomainObject
	 */
	public function getChildObjects() {
		return $this->childObjects;
	}

	/**
	 * @param boolean $sorting
	 */
	public function setSorting($sorting) {
		$this->sorting = $sorting;
	}

	/**
	 * @return boolean
	 */
	public function getSorting() {
		return $this->sorting;
	}

	/**
	 * @param boolean $addDeletedField
	 */
	public function setAddDeletedField($AddDeletedField) {
		$this->addDeletedField = $AddDeletedField;
	}

	/**
	 * @param boolean $categorizable
	 */
	public function setCategorizable($categorizable) {
		$this->categorizable = $categorizable;
	}

	/**
	 * @return boolean
	 */
	public function getAddDeletedField() {
		return $this->addDeletedField;
	}

	/**
	 * @param boolean $addHiddenField
	 */
	public function setAddHiddenField($addHiddenField) {
		$this->addHiddenField = $addHiddenField;
	}

	/**
	 * @return boolean
	 */
	public function getAddHiddenField() {
		return $this->addHiddenField;
	}

	/**
	 * @param boolean $addStarttimeEndtimeFields
	 */
	public function setAddStarttimeEndtimeFields($addStarttimeEndtimeFields) {
		$this->addStarttimeEndtimeFields = $addStarttimeEndtimeFields;
	}

	/**
	 * @return boolean
	 */
	public function getAddStarttimeEndtimeFields() {
		return $this->addStarttimeEndtimeFields;
	}

	/**
	 * @return bool
	 */
	public function getCategorizable() {
		return $this->categorizable;
	}

}
