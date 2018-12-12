<?php
namespace EBT\ExtensionBuilder\Domain\Model;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AnyToManyRelation;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation;

/**
 * Schema for a Domain Object
 */
class DomainObject
{
    /**
     * @var string
     */
    protected $name = '';
    /**
     * @var string
     */
    protected $uniqueIdentifier = '';
    /**
     * @var string
     */
    protected $description = '';
    /**
     * If true, this is an aggregate root.
     *
     * @var bool
     */
    protected $aggregateRoot = false;
    /**
     * If true, the element is sortable in the TYPO3 backend.
     *
     * @var bool
     */
    protected $sorting = false;
    /**
     * If true, the related record has a "deleted" enable field.
     *
     * @var bool
     */
    protected $addDeletedField = false;
    /**
     * If true, the related record has a "hidden" enable field.
     *
     * @var bool
     */
    protected $addHiddenField = false;
    /**
     * If true, the related record has a "starttime/endtime" enable field.
     *
     * @var bool
     */
    protected $addStarttimeEndtimeFields = false;
    /**
     * If true, the element is categorizable in the TYPO3 backend.
     *
     * @var bool
     */
    protected $categorizable = false;
    /**
     * If true, this is an entity. If false, it is a ValueObject.
     *
     * @var bool
     */
    protected $entity = false;
    /**
     * The extension this domain object belongs to.
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\Extension
     */
    protected $extension = null;
    /**
     * List of properties the domain object has.
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty[]
     */
    protected $properties = [];
    /**
     * List of actions the domain object has.
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject\Action[]
     */
    protected $actions = [];
    /**
     * Is an upload folder required for this domain object?
     *
     * @var bool
     */
    protected $needsUploadFolder = false;
    /**
     * @var string
     */
    protected $mapToTable = '';
    /**
     * @var string
     */
    protected $parentClass = '';
    /**
     * Domain objects that extend the current object (as declared in this extension).
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject[]
     */
    protected $childObjects = [];

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getQualifiedClassName()
    {
        $qualifiedClassName = $this->extension->getNamespaceName() . '\\Domain\\Model\\' . $this->getName();
        if (strpos($qualifiedClassName, '\\') === 0) {
            $qualifiedClassName = substr($qualifiedClassName, 1);
        }
        return $qualifiedClassName;
    }

    /**
     * @return string
     */
    public function getFullQualifiedClassName()
    {
        return '\\' . $this->getQualifiedClassName();
    }

    /**
     * @return string
     */
    public function getControllerClassName()
    {
        return $this->extension->getNamespaceName() . '\\Controller\\' . $this->getName() . 'Controller';
    }

    /**
     * @return string
     */
    public function getDatabaseTableName()
    {
        $result = 'tx_' .
            strtolower($this->extension->getExtensionName()) .
            '_domain_model_' .
            strtolower($this->getName());

        if (!empty($this->mapToTable)) {
            $result = $this->mapToTable;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getUniqueIdentifier()
    {
        return $this->uniqueIdentifier;
    }

    /**
     * @param string $uniqueIdentifier
     * @return void
     */
    public function setUniqueIdentifier($uniqueIdentifier)
    {
        $this->uniqueIdentifier = $uniqueIdentifier;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        if ($this->description) {
            return $this->description;
        } else {
            return $this->getName();
        }
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return bool true if it is an aggregate root, false otherwise.
     */
    public function getAggregateRoot()
    {
        return $this->aggregateRoot;
    }

    /**
     * @return bool
     */
    public function isAggregateRoot()
    {
        return $this->getAggregateRoot();
    }

    /**
     * @param bool $aggregateRoot true if Domain Object should be aggregate root
     * @return void
     */
    public function setAggregateRoot($aggregateRoot)
    {
        $this->aggregateRoot = (bool)$aggregateRoot;
    }

    /**
     * @return bool true if it is an entity, false if it is a ValueObject
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return bool true if it is an entity, false if it is a ValueObject
     */
    public function isEntity()
    {
        return $this->getEntity();
    }

    /**
     * @param $entity $entity true if it is an entity, false if it is a ValueObject
     * @return void
     */
    public function setEntity($entity)
    {
        $this->entity = (bool)$entity;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $property
     *
     * @return void
     */
    public function addProperty(DomainObject\AbstractProperty $property)
    {
        $property->setDomainObject($this);
        if ($property->getNeedsUploadFolder()) {
            $this->needsUploadFolder = true;
        }
        $this->properties[] = $property;
    }

    /**
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param string $propertyName
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty|null
     */
    public function getPropertyByName($propertyName)
    {
        foreach ($this->properties as $property) {
            if ($property->getName() == $propertyName) {
                return $property;
            }
        }
        return null;
    }

    /**
     * Get all properties holding relations of type
     * Property_Relation_ZeroToManyRelation
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation[]
     */
    public function getZeroToManyRelationProperties()
    {
        $relationProperties = [];
        foreach ($this->properties as $property) {
            if (is_a($property, ZeroToManyRelation::class)) {
                $relationProperties[] = $property;
            }
        }
        return $relationProperties;
    }

    /**
     * Get all properties holding relations of type AnyToManyRelation
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AnyToManyRelation[]
     */
    public function getAnyToManyRelationProperties()
    {
        $relationProperties = [];
        foreach ($this->properties as $property) {
            if (is_subclass_of($property, AnyToManyRelation::class)) {
                $relationProperties[] = $property;
            }
        }
        return $relationProperties;
    }

    /**
     * @return bool
     */
    public function hasRelations()
    {
        return count($this->getAnyToManyRelationProperties()) == 0 && count($this->getZeroToManyRelationProperties()) == 0;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\Action $action
     *
     * @return void
     */
    public function addAction(DomainObject\Action $action)
    {
        $action->setDomainObject($this);
        if (!in_array($action, $this->actions)) {
            $this->actions[] = $action;
        }
    }

    /**
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject\Action[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @return bool
     */
    public function hasActions()
    {
        return count($this->actions) > 0;
    }

    /**
     * DO NOT CALL DIRECTLY! This is being called by addDomainModel() automatically.
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension the extension this domain model belongs to.
     *
     * @return void
     */
    public function setExtension(Extension $extension)
    {
        $this->extension = $extension;
    }

    /**
     * @return \EBT\ExtensionBuilder\Domain\Model\Extension
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Get the base class for this Domain Object (different if it is entity or value
     * object)
     *
     * @return string name of the base class
     */
    public function getBaseClass()
    {
        if ($this->entity) {
            return '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractEntity';
        } else {
            return '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractValueObject';
        }
    }

    /**
     * Returns the name of the domain repository class without namespaces, (only if it is an
     * aggregate root).
     *
     * @return string
     */
    public function getDomainRepositoryClassName()
    {
        if (!$this->aggregateRoot) {
            return '';
        } else {
            return $this->getName() . 'Repository';
        }
    }

    /**
     * Returns the name of the domain repository class name, if it is an aggregate
     * root.
     *
     * @return string
     */
    public function getQualifiedDomainRepositoryClassName()
    {
        if (!$this->aggregateRoot) {
            return '';
        }

        return $this->extension->getNamespaceName() . '\\Domain\\Repository\\' . $this->getName() . 'Repository';
    }

    /**
     * Returns the fully qualified name of the domain repository class name, if it
     * is an aggregate root.
     *
     * @return string
     */
    public function getFullyQualifiedDomainRepositoryClassName()
    {
        if (!$this->aggregateRoot) {
            return '';
        }

        return '\\' . $this->getQualifiedDomainRepositoryClassName();
    }

    /**
     * @return string
     */
    public function getCommaSeparatedFieldList()
    {
        $fieldNames = [];
        foreach ($this->properties as $property) {
            $fieldNames[] = $property->getFieldName();
        }
        return implode(',', $fieldNames);
    }

    /**
     * Get the label to display in the list module.
     *
     * TODO: Needs to be configurable. Currently, the first property is the label in
     *       the backend.
     *
     * @return string
     */
    public function getListModuleValueLabel()
    {
        if (isset($this->properties[0])) {
            return $this->properties[0]->getFieldName();
        } else {
            return 'uid';
        }
    }

    /**
     * @return string
     */
    public function getLabelNamespace()
    {
        return $this->extension->getShortExtensionKey() . '_domain_model_' . strtolower($this->getName());
    }

    /**
     * @return bool
     */
    public function getHasBooleanProperties()
    {
        foreach ($this->properties as $property) {
            if ($property->isBoolean()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function hasPropertiesThatNeedMappingStatements()
    {
        $propertiesWithMappingStatements = [];
        foreach ($this->properties as $property) {
            if ($property->getMappingStatement()) {
                $propertiesWithMappingStatements[] = $property;
            }
        }
        return $propertiesWithMappingStatements;
    }

    /**
     * @return bool
     */
    public function getNeedsUploadFolder()
    {
        return $this->needsUploadFolder;
    }

    /**
     * @return bool
     */
    public function needsTcaOverride()
    {
        return $this->isMappedToExistingTable() || $this->hasChildren() || $this->categorizable;
    }

    /**
     * @return string
     */
    public function getMapToTable()
    {
        return $this->mapToTable;
    }

    /**
     * @param string $mapToTable
     * @return void
     */
    public function setMapToTable($mapToTable)
    {
        $this->mapToTable = $mapToTable;
    }

    /**
     * Is this domain object mapped to an existing table?
     *
     * @return bool|array
     */
    public function getNeedsMappingStatement()
    {
        if (!empty($this->mapToTable)) {
            return true;
        }

        return $this->hasPropertiesThatNeedMappingStatements();
    }

    /**
     * Is this domain object mapped to a table?
     *
     * @return bool
     */
    public function isMappedToExistingTable()
    {
        if (!empty($this->mapToTable)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function getNeedsTableCtrlDefinition()
    {
        if ($this->mapToTable || $this->isSubClass()) {
            // ctrl definitions should already be defined in both cases
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param string $parentClass
     * @return void
     */
    public function setParentClass($parentClass)
    {
        $this->parentClass = $parentClass;
    }

    /**
     * @return string
     */
    public function getParentClass()
    {
        return $this->parentClass;
    }

    /**
     * @return string
     */
    public function getRecordType()
    {
        $recordType = 'Tx_' . $this->extension->getExtensionName() . '_' . $this->getName();
        return $recordType;
    }

    /**
     * @return bool
     */
    public function isSubClass()
    {
        if (empty($this->parentClass)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $childObject
     *
     * @return void
     */
    public function addChildObject(DomainObject $childObject)
    {
        $this->childObjects[] = $childObject;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return count($this->childObjects) > 0;
    }

    /**
     * wrapper for fluid
     * @return bool
     */
    public function getHasChildren()
    {
        return $this->hasChildren();
    }

    /**
     * @return array DomainObject
     */
    public function getChildObjects()
    {
        return $this->childObjects;
    }

    /**
     * @param bool $sorting
     * @return void
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * @return bool
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param bool $addDeletedField
     * @return void
     */
    public function setAddDeletedField($addDeletedField)
    {
        $this->addDeletedField = $addDeletedField;
    }

    /**
     * @param bool $categorizable
     * @return void
     */
    public function setCategorizable($categorizable)
    {
        $this->categorizable = $categorizable;
    }

    /**
     * @return bool
     */
    public function getAddDeletedField()
    {
        return $this->addDeletedField;
    }

    /**
     * @param bool $addHiddenField
     * @return void
     */
    public function setAddHiddenField($addHiddenField)
    {
        $this->addHiddenField = $addHiddenField;
    }

    /**
     * @return bool
     */
    public function getAddHiddenField()
    {
        return $this->addHiddenField;
    }

    /**
     * @param bool $addStarttimeEndtimeFields
     * @return void
     */
    public function setAddStarttimeEndtimeFields($addStarttimeEndtimeFields)
    {
        $this->addStarttimeEndtimeFields = $addStarttimeEndtimeFields;
    }

    /**
     * @return bool
     */
    public function getAddStarttimeEndtimeFields()
    {
        return $this->addStarttimeEndtimeFields;
    }

    /**
     * @return bool
     */
    public function getCategorizable()
    {
        return $this->categorizable;
    }

    /**
     * @return array|DomainObject\AbstractProperty[]
     */
    public function getSearchableProperties() {
        return array_filter($this->properties, function($property) {
            return $property->isSearchable();
        });
    }
}
