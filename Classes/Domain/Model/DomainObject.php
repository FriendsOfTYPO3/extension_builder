<?php

declare(strict_types=1);

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

namespace EBT\ExtensionBuilder\Domain\Model;

use EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Action;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AnyToManyRelation;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation;

/**
 * Schema for a Domain Object
 */
class DomainObject
{
    protected string $name = '';
    protected ?string $uniqueIdentifier = null;
    protected string $description = '';
    /**
     * If true, this is an aggregate root.
     */
    protected bool $aggregateRoot = false;
    protected string $controllerScope = "Frontend";

    /**
     * If true, the element is sortable in the TYPO3 backend.
     */
    protected bool $sorting = false;
    /**
     * If true, the related record has a "deleted" enable field.
     */
    protected bool $addDeletedField = false;
    /**
     * If true, the related record has a "hidden" enable field.
     */
    protected bool $addHiddenField = false;
    /**
     * If true, the related record has a "starttime/endtime" enable field.
     */
    protected bool $addStarttimeEndtimeFields = false;
    /**
     * If true, the element is categorizable in the TYPO3 backend.
     */
    protected bool $categorizable = false;
    /**
     * If true, this is an entity. If false, it is a ValueObject.
     */
    protected bool $entity = false;
    /**
     * The extension this domain object belongs to.
     */
    protected ?Extension $extension = null;
    /**
     * List of properties the domain object has.
     *
     * @var AbstractProperty[]
     */
    protected array $properties = [];
    /**
     * List of actions the domain object has.
     *
     * @var Action[]
     */
    protected array $actions = [];
    protected string $mapToTable = '';
    protected string $parentClass = '';
    /**
     * Domain objects that extend the current object (as declared in this extension).
     *
     * @var DomainObject[]
     */
    protected array $childObjects = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getQualifiedClassName(): string
    {
        $qualifiedClassName = $this->extension->getNamespaceName() . '\\Domain\\Model\\' . $this->getName();
        if (strpos($qualifiedClassName, '\\') === 0) {
            $qualifiedClassName = substr($qualifiedClassName, 1);
        }
        return $qualifiedClassName;
    }

    public function getFullQualifiedClassName(): string
    {
        return '\\' . $this->getQualifiedClassName();
    }

    public function getControllerClassName(): string
    {
        return $this->extension->getNamespaceName() . '\\Controller\\' . $this->getName() . 'Controller';
    }

    public function getDatabaseTableName(): string
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

    public function getUniqueIdentifier(): ?string
    {
        return $this->uniqueIdentifier;
    }

    public function setUniqueIdentifier(?string $uniqueIdentifier): void
    {
        $this->uniqueIdentifier = $uniqueIdentifier;
    }

    public function getDescription(): string
    {
        if ($this->description) {
            return $this->description;
        }

        return $this->getName();
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return bool true if it is an aggregate root, false otherwise.
     */
    public function getAggregateRoot(): bool
    {
        return $this->aggregateRoot;
    }

    public function isAggregateRoot(): bool
    {
        return $this->getAggregateRoot();
    }

    /**
     * @param bool $aggregateRoot true if Domain Object should be aggregate root
     */
    public function setAggregateRoot(bool $aggregateRoot): void
    {
        $this->aggregateRoot = $aggregateRoot;
    }

    public function getControllerScope(): string
    {
        return $this->controllerScope;
    }

    public function setControllerScope(string $controllerScope): void
    {
        $this->controllerScope = $controllerScope;
    }

    /**
     * @return bool true if it is an entity, false if it is a ValueObject
     */
    public function getEntity(): bool
    {
        return $this->entity;
    }

    /**
     * @return bool true if it is an entity, false if it is a ValueObject
     */
    public function isEntity(): bool
    {
        return $this->getEntity();
    }

    /**
     * @param $entity $entity true if it is an entity, false if it is a ValueObject
     */
    public function setEntity(bool $entity): void
    {
        $this->entity = $entity;
    }

    public function addProperty(AbstractProperty $property): void
    {
        $property->setDomainObject($this);
        $this->properties[] = $property;
    }

    /**
     * @return AbstractProperty[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getPropertyByName(string $propertyName): ?AbstractProperty
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
     * @return ZeroToManyRelation[]
     */
    public function getZeroToManyRelationProperties(): array
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
     * @return AnyToManyRelation[]
     */
    public function getAnyToManyRelationProperties(): array
    {
        $relationProperties = [];
        foreach ($this->properties as $property) {
            if (is_subclass_of($property, AnyToManyRelation::class)) {
                $relationProperties[] = $property;
            }
        }
        return $relationProperties;
    }

    public function hasRelations(): bool
    {
        return count($this->getAnyToManyRelationProperties()) === 0 && count($this->getZeroToManyRelationProperties()) === 0;
    }

    public function addAction(Action $action): void
    {
        $action->setDomainObject($this);
        if (!in_array($action, $this->actions)) {
            $this->actions[] = $action;
        }
    }

    /**
     * @return Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function hasActions(): bool
    {
        return count($this->actions) > 0;
    }

    /**
     * DO NOT CALL DIRECTLY! This is being called by addDomainModel() automatically.
     *
     * @param Extension $extension the extension this domain model belongs to.
     */
    public function setExtension(Extension $extension): void
    {
        $this->extension = $extension;
    }

    public function getExtension(): ?Extension
    {
        return $this->extension;
    }

    /**
     * Get the base class for this Domain Object (different if it is entity or value
     * object)
     *
     * @return string name of the base class
     */
    public function getBaseClass(): string
    {
        if ($this->entity) {
            return '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractEntity';
        }

        return '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractValueObject';
    }

    /**
     * Returns the name of the domain repository class without namespaces, (only if it is an
     * aggregate root).
     *
     * @return string
     */
    public function getDomainRepositoryClassName(): string
    {
        if (!$this->aggregateRoot) {
            return '';
        }

        return $this->getName() . 'Repository';
    }

    /**
     * Returns the name of the domain repository class name, if it is an aggregate
     * root.
     *
     * @return string
     */
    public function getQualifiedDomainRepositoryClassName(): string
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
    public function getFullyQualifiedDomainRepositoryClassName(): string
    {
        if (!$this->aggregateRoot) {
            return '';
        }

        return '\\' . $this->getQualifiedDomainRepositoryClassName();
    }

    public function getCommaSeparatedFieldList(): string
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
    public function getListModuleValueLabel(): string
    {
        if (isset($this->properties[0])) {
            return $this->properties[0]->getFieldName();
        }

        return 'uid';
    }

    public function getLabelNamespace(): string
    {
        return $this->extension->getShortExtensionKey() . '_domain_model_' . strtolower($this->getName());
    }

    public function getDescriptionNamespace(): string
    {
        return $this->getLabelNamespace() . '.description';
    }

    public function getHasBooleanProperties(): bool
    {
        foreach ($this->properties as $property) {
            if ($property->isBoolean()) {
                return true;
            }
        }
        return false;
    }

    public function getHasPropertiesWithMappingStatements(): bool
    {
        return count($this->getPropertiesThatNeedMappingStatements()) > 0;
    }

    public function getPropertiesThatNeedMappingStatements(): array
    {
        $propertiesWithMappingStatements = [];
        foreach ($this->properties as $property) {
            if ($property->getMappingStatement()) {
                $propertiesWithMappingStatements[] = $property;
            }
        }
        return $propertiesWithMappingStatements;
    }

    public function needsTcaOverride(): bool
    {
        return $this->isMappedToExistingTable() || $this->hasChildren() || $this->categorizable;
    }

    public function getMapToTable(): string
    {
        return $this->mapToTable;
    }

    public function setMapToTable(string $mapToTable): void
    {
        $this->mapToTable = $mapToTable;
    }

    /**
     * Is this domain object mapped to an existing table?
     *
     * @return bool|array
     */
    public function getNeedsMappingStatement(): bool
    {
        if (!empty($this->mapToTable)) {
            return true;
        }

        return $this->getHasPropertiesWithMappingStatements();
    }

    /**
     * Is this domain object mapped to a table?
     *
     * @return bool
     */
    public function isMappedToExistingTable(): bool
    {
        return !empty($this->mapToTable);
    }

    public function getNeedsTableCtrlDefinition(): bool
    {
        // ctrl definitions should already be defined in both cases
        return !($this->mapToTable || $this->isSubClass());
    }

    public function setParentClass(string $parentClass): void
    {
        $this->parentClass = $parentClass;
    }

    public function getParentClass(): string
    {
        return $this->parentClass;
    }

    public function getRecordType(): string
    {
        return 'Tx_' . $this->extension->getExtensionName() . '_' . $this->getName();
    }

    public function isSubClass(): bool
    {
        return !empty($this->parentClass);
    }

    public function addChildObject(self $childObject): void
    {
        $this->childObjects[] = $childObject;
    }

    public function hasChildren(): bool
    {
        return count($this->childObjects) > 0;
    }

    /**
     * wrapper for fluid
     * @return bool
     */
    public function getHasChildren(): bool
    {
        return $this->hasChildren();
    }

    /**
     * @return array DomainObject
     */
    public function getChildObjects(): array
    {
        return $this->childObjects;
    }

    public function setSorting(bool $sorting): void
    {
        $this->sorting = $sorting;
    }

    public function getSorting(): bool
    {
        return $this->sorting;
    }

    public function setAddDeletedField(bool $addDeletedField): void
    {
        $this->addDeletedField = $addDeletedField;
    }

    public function setCategorizable(bool $categorizable): void
    {
        $this->categorizable = $categorizable;
    }

    public function getAddDeletedField(): bool
    {
        return $this->addDeletedField;
    }

    public function setAddHiddenField(bool $addHiddenField): void
    {
        $this->addHiddenField = $addHiddenField;
    }

    public function getAddHiddenField(): bool
    {
        return $this->addHiddenField;
    }

    public function setAddStarttimeEndtimeFields(bool $addStarttimeEndtimeFields): void
    {
        $this->addStarttimeEndtimeFields = $addStarttimeEndtimeFields;
    }

    public function getAddStarttimeEndtimeFields(): bool
    {
        return $this->addStarttimeEndtimeFields;
    }

    public function getCategorizable(): bool
    {
        return $this->categorizable;
    }

    /**
     * @return array|AbstractProperty[]
     */
    public function getSearchableProperties(): array
    {
        return array_filter($this->properties, static function ($property) {
            return $property->isSearchable();
        });
    }
}
