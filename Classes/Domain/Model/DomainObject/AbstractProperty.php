<?php

namespace EBT\ExtensionBuilder\Domain\Model\DomainObject;

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

use EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AnyToManyRelation;
use EBT\ExtensionBuilder\Service\ValidationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * property representing a "property" in the context of software development
 */
abstract class AbstractProperty
{
    /**
     * @var string
     */
    protected $uniqueIdentifier = '';

    /**
     * name of the property
     *
     * @var string
     */
    protected $name = '';

    /**
     * description of property
     *
     * @var string
     */
    protected $description = '';

    /**
     * whether the property is required
     *
     * @var bool
     */
    protected $required = false;

    /**
     * property's default value
     *
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Is an upload folder required for this property
     *
     * @var bool
     */
    protected $needsUploadFolder = false;

    /**
     * The domain object this property belongs to.
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject
     */
    protected $class;

    /**
     * is set to true, if this property was new added
     *
     * @var bool
     */
    protected $new = true;

    /**
     * use RTE in Backend
     *
     * @var bool
     */
    protected $useRTE = false;

    /**
     * @var string the property type of this property
     */
    protected $type = '';

    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject
     */
    protected $domainObject;

    /**
     * @var bool
     */
    protected $excludeField = false;

    /**
     * @var bool
     */
    protected $l10nModeExclude = false;

    /**
     * @var bool
     */
    protected $cascadeRemove = false;

    /**
     * @var bool
     */
    protected $searchable = false;

    /**
     * @param string $propertyName
     */
    public function __construct($propertyName = '')
    {
        if (!empty($propertyName)) {
            $this->name = $propertyName;
        }
    }

    /**
     * DO NOT CALL DIRECTLY! This is being called by addProperty() automatically.
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $class the class this property belongs to
     */
    public function setClass(ClassObject $class): void
    {
        $this->class = $class;
    }

    /**
     * Get the domain object this property belongs to.
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject
     */
    public function getClass(): DomainObject
    {
        return $this->class;
    }

    /**
     * Get property name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set property name
     *
     * @param string $name Property name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get property defaultValue
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set property defaultValue
     *
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return bool
     */
    public function getHasDefaultValue(): bool
    {
        return isset($this->defaultValue);
    }

    /**
     * Get property uniqueIdentifier
     *
     * @return string
     */
    public function getUniqueIdentifier(): string
    {
        return $this->uniqueIdentifier;
    }

    /**
     * Set property uniqueIdentifier
     *
     * @param string|null $uniqueIdentifier
     */
    public function setUniqueIdentifier(?string $uniqueIdentifier): void
    {
        $this->uniqueIdentifier = $uniqueIdentifier;
    }

    /**
     * @return bool true (if property is of type relation any to many)
     */
    public function isAnyToManyRelation(): bool
    {
        return is_subclass_of($this, AnyToManyRelation::class);
    }

    /**
     * @return bool true (if property is of type relation any to many)
     * @deprecated Use `!instanceof ZeroToManyRelation` instead
     */
    public function isZeroToManyRelation(): bool
    {
        return false;
    }

    /**
     * @return bool true (if property is of type relation)
     */
    public function isRelation(): bool
    {
        return is_subclass_of($this, AbstractRelation::class);
    }

    /**
     * @return bool true (if property is of type boolean)
     */
    public function isBoolean(): bool
    {
        return is_a($this, BooleanProperty::class);
    }

    /**
     * Get property description to be used in comments
     *
     * @return string Property description
     */
    public function getDescription(): string
    {
        if ($this->description) {
            return $this->description;
        }

        return $this->getName();
    }

    /**
     * Set property description
     *
     * @param string|null $description Property description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Returns a field name used in the database. This is the property name converted
     * to lowercase underscore (mySpecialProperty -> my_special_property).
     *
     * @return string the field name in lowercase underscore
     */
    public function getFieldName(): string
    {
        $fieldName = GeneralUtility::camelCaseToLowerCaseUnderscored($this->name);
        if (ValidationService::isReservedMYSQLWord($fieldName)) {
            $fieldName = $this->domainObject->getExtension()->getShortExtensionKey() . '_' . $fieldName;
        }
        return $fieldName;
    }

    /**
     * Get SQL Definition to be used inside CREATE TABLE.
     *
     * @return string the SQL definition
     */
    abstract public function getSqlDefinition(): string;

    /**
     * Template Method which should return the type hinting information
     * being used in PHPDoc Comments.
     * Examples: int, string, Tx_FooBar_Something, \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Tx_FooBar_Something>
     *
     * @return string|null
     */
    abstract public function getTypeForComment(): ?string;

    /**
     * Template method which should return the PHP type hint
     * Example: \TYPO3\CMS\Extbase\Persistence\ObjectStorage, array, Tx_FooBar_Something
     *
     * @return string|null
     */
    abstract public function getTypeHint(): ?string;

    /**
     * true if this property is required, false otherwise.
     *
     * @return bool
     */
    public function getRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return bool
     */
    public function getCascadeRemove(): bool
    {
        return $this->cascadeRemove;
    }

    /**
     * Set whether this property is required
     *
     * @param bool $required
     */
    public function setRequired($required): void
    {
        $this->required = $required;
    }

    /**
     * Set whether this property is exclude field
     *
     * @param bool $excludeField
     * @return void
     */
    public function setExcludeField($excludeField): void
    {
        $this->excludeField = $excludeField;
    }

    /**
     * true if this property is an exclude field, false otherwise.
     *
     * @return bool
     */
    public function getExcludeField(): bool
    {
        return $this->excludeField;
    }

    /**
     * Set whether this property is l10n_mode = exclude
     *
     * @param bool $l10nModeExclude
     * @return void
     */
    public function setL10nModeExclude($l10nModeExclude): void
    {
        $this->l10nModeExclude = $l10nModeExclude;
    }

    /**
     * true if this property  l10n_mode = exclude, false otherwise.
     *
     * @return bool
     */
    public function getL10nModeExclude(): bool
    {
        return $this->l10nModeExclude;
    }

    /**
     * Get the validate annotation to be used in the domain model for this property.
     *
     * @return string
     */
    public function getValidateAnnotation(): string
    {
        if ($this->required) {
            return '@TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")';
        }
        return '';
    }

    /**
     * @return string
     */
    public function getCascadeRemoveAnnotation(): string
    {
        if ($this->cascadeRemove) {
            return '@TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")';
        }
        return '';
    }

    /**
     * Get the data type of this property. This is the last part after EBT\\ExtensionBuilder\\Domain\\Model\\DomainObject_*
     *
     * @return string the data type of this property
     */
    public function getDataType(): string
    {
        $shortClassNameParts = explode('\\', get_class($this));
        return end($shortClassNameParts);
    }

    /**
     * Is this property displayable inside a Fluid template?
     *
     * @return bool true if this property can be displayed inside a fluid template
     */
    public function getIsDisplayable(): bool
    {
        return true;
    }

    /**
     * The string to be used inside object accessors to display this property.
     *
     * @return string
     */
    public function getNameToBeDisplayedInFluidTemplate(): string
    {
        return $this->name;
    }

    /**
     * The locallang key for this property which contains the label.
     *
     * @return string
     */
    public function getLabelNamespace(): string
    {
        return $this->domainObject->getLabelNamespace() . '.' . $this->getFieldName();
    }

    /**
     * DO NOT CALL DIRECTLY! This is being called by addProperty() automatically.
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject the domain object this property belongs to
     */
    public function setDomainObject(DomainObject $domainObject): void
    {
        $this->domainObject = $domainObject;
    }

    /**
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     */
    public function getDomainObject(): DomainObject
    {
        return $this->domainObject;
    }

    /**
     * The Typoscript statement used by extbase to map the property to
     * a specific database fieldname
     *
     * @return string $mappingStatement
     */
    public function getMappingStatement(): ?string
    {
        if ($this->getFieldName() != GeneralUtility::camelCaseToLowerCaseUnderscored($this->name)) {
            return $this->getFieldName() . '.mapOnProperty = ' . $this->name;
        }

        return null;
    }

    /**
     * Getter for $needsUploadFolder
     *
     * @return bool $needsUploadFolder
     */
    public function getNeedsUploadFolder(): bool
    {
        return $this->needsUploadFolder;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->new;
    }

    /**
     * @param bool $new
     */
    public function setNew(bool $new): void
    {
        $this->new = $new;
    }

    /**
     * Getter for $useRTE
     *
     * @return bool $useRTE
     */
    public function getUseRTE(): bool
    {
        return $this->useRTE;
    }

    /**
     * @return string
     */
    public function getUnqualifiedType()
    {
        $type = $this->getTypeForComment();
        if (substr($type, 0, 1) === chr(92)) {
            return substr($type, 1);
        }

        return $type;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isFileReference(): bool
    {
        return in_array($this->type, ['Image', 'File']);
    }

    /**
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }
}
