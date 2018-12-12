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
    protected $defaultValue = null;

    /**
     * @var mixed
     */
    protected $value = null;

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
    protected $class = null;

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
    protected $domainObject = null;

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
     *
     * @param string $propertyName
     * @return void
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
    public function setClass(ClassObject $class)
    {
        $this->class = $class;
    }

    /**
     * Get the domain object this property belongs to.
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Get property name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set property name
     *
     * @param string $name Property name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get property defaultValue
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set property defaultValue
     *
     * @param string $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return bool
     */
    public function getHasDefaultValue()
    {
        return isset($this->defaultValue);
    }

    /**
     * Get property uniqueIdentifier
     *
     * @return string
     */
    public function getUniqueIdentifier()
    {
        return $this->uniqueIdentifier;
    }

    /**
     * Set property uniqueIdentifier
     *
     * @param string $uniqueIdentifier
     */
    public function setUniqueIdentifier($uniqueIdentifier)
    {
        $this->uniqueIdentifier = $uniqueIdentifier;
    }

    /**
     * @return bool true (if property is of type relation any to many)
     */
    public function isAnyToManyRelation()
    {
        return is_subclass_of($this, AnyToManyRelation::class);
    }

    /**
     * @return bool true (if property is of type relation any to many)
     */
    public function isZeroToManyRelation()
    {
        return false;
    }

    /**
     * @return bool true (if property is of type relation)
     */
    public function isRelation()
    {
        return is_subclass_of($this, AbstractRelation::class);
    }

    /**
     * @return bool true (if property is of type boolean)
     */
    public function isBoolean()
    {
        return is_a($this, BooleanProperty::class);
    }

    /**
     * Get property description to be used in comments
     *
     * @return string Property description
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
     * Set property description
     *
     * @param string $description Property description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns a field name used in the database. This is the property name converted
     * to lowercase underscore (mySpecialProperty -> my_special_property).
     *
     * @return string the field name in lowercase underscore
     */
    public function getFieldName()
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
     * @retrun string the SQL definition
     */
    abstract public function getSqlDefinition();

    /**
     * Template Method which should return the type hinting information
     * being used in PHPDoc Comments.
     * Examples: int, string, Tx_FooBar_Something, \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Tx_FooBar_Something>
     *
     * @return string
     */
    abstract public function getTypeForComment();

    /**
     * Template method which should return the PHP type hint
     * Example: \TYPO3\CMS\Extbase\Persistence\ObjectStorage, array, Tx_FooBar_Something
     *
     * @return string
     */
    abstract public function getTypeHint();

    /**
     * true if this property is required, false otherwise.
     *
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @return bool
     */
    public function getCascadeRemove()
    {
        return $this->cascadeRemove;
    }

    /**
     * Set whether this property is required
     *
     * @param bool $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * Set whether this property is exclude field
     *
     * @param bool $excludeField
     * @return void
     */
    public function setExcludeField($excludeField)
    {
        $this->excludeField = $excludeField;
    }

    /**
     * true if this property is an exclude field, false otherwise.
     *
     * @return bool
     */
    public function getExcludeField()
    {
        return $this->excludeField;
    }

    /**
     * Set whether this property is l10n_mode = exclude
     *
     * @param bool $l10nModeExclude
     * @return void
     */
    public function setL10nModeExclude($l10nModeExclude)
    {
        $this->l10nModeExclude = $l10nModeExclude;
    }

    /**
     * true if this property  l10n_mode = exclude, false otherwise.
     *
     * @return bool
     */
    public function getL10nModeExclude()
    {
        return $this->l10nModeExclude;
    }

    /**

     * Get the validate annotation to be used in the domain model for this property.
     *
     * @return string
     */
    public function getValidateAnnotation()
    {
        if ($this->required) {
            return '@validate NotEmpty';
        }
        return '';
    }

    /**
     * @return string
     */
    public function getCascadeRemoveAnnotation()
    {
        if ($this->cascadeRemove) {
            return '@cascade remove';
        }
        return '';
    }

    /**
     * Get the data type of this property. This is the last part after EBT\\ExtensionBuilder\\Domain\\Model\\DomainObject_*
     *
     * @return string the data type of this property
     */
    public function getDataType()
    {
        $shortClassNameParts = explode('\\', get_class($this));
        return end($shortClassNameParts);
    }

    /**
     * Is this property displayable inside a Fluid template?
     *
     * @return bool true if this property can be displayed inside a fluid template
     */
    public function getIsDisplayable()
    {
        return true;
    }

    /**
     * The string to be used inside object accessors to display this property.
     *
     * @return string
     */
    public function getNameToBeDisplayedInFluidTemplate()
    {
        return $this->name;
    }

    /**
     * The locallang key for this property which contains the label.
     *
     * @return string
     */
    public function getLabelNamespace()
    {
        return $this->domainObject->getLabelNamespace() . '.' . $this->getFieldName();
    }

    /**
     * DO NOT CALL DIRECTLY! This is being called by addProperty() automatically.
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject the domain object this property belongs to
     */
    public function setDomainObject(DomainObject $domainObject)
    {
        $this->domainObject = $domainObject;
    }

    /**
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     */
    public function getDomainObject()
    {
        return $this->domainObject;
    }

    /**
     * The Typoscript statement used by extbase to map the property to
     * a specific database fieldname
     *
     * @return string $mappingStatement
     */
    public function getMappingStatement()
    {
        if ($this->getFieldName() != GeneralUtility::camelCaseToLowerCaseUnderscored($this->name)) {
            return $this->getFieldName() . '.mapOnProperty = ' . $this->name;
        } else {
            return null;
        }
    }

    /**
     * Getter for $needsUploadFolder
     *
     * @return bool $needsUploadFolder
     */
    public function getNeedsUploadFolder()
    {
        return $this->needsUploadFolder;
    }

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * @param bool $new
     */
    public function setNew($new)
    {
        $this->new = $new;
    }

    /**
     * Getter for $useRTE
     *
     * @return bool $useRTE
     */
    public function getUseRTE()
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
        } else {
            return $type;
        }
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isFileReference()
    {
        return in_array($this->type, ['Image', 'File']);
    }

    /**
     * @return bool
     */
    public function isSearchable() {
        return $this->searchable;
    }
}
