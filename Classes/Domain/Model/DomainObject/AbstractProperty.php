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

namespace EBT\ExtensionBuilder\Domain\Model\DomainObject;

use EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AnyToManyRelation;
use EBT\ExtensionBuilder\Service\ClassBuilder;
use EBT\ExtensionBuilder\Service\ValidationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * property representing a "property" in the context of software development
 */
abstract class AbstractProperty
{
    protected ?string $uniqueIdentifier = '';

    protected string $name = '';

    protected ?string $description = '';

    /**
     * whether the property is required
     */
    protected bool $required = false;

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
     * The domain object this property belongs to.
     *
     * @var DomainObject
     */
    protected DomainObject $class;

    /**
     * is set to true, if this property was new added
     */
    protected bool $new = true;

    /**
     * use RTE in Backend
     */
    protected bool $useRTE = false;

    /**
     * @var string|null the data type of this property
     */
    protected ?string $type = '';

    protected ?DomainObject $domainObject = null;

    protected bool $nullable = false;

    /**
     * @var bool define whether a property is nullable in TCA
     */
    protected static bool $isNullable = false;

    protected bool $excludeField = false;

    protected bool $l10nModeExclude = false;

    protected bool $cascadeRemove = false;

    protected bool $searchable = false;

    protected int $size = 0;
    protected int $maxItems = 0;
    protected int $minItems = 0;

    // Select Box
    protected array $selectboxValues = [];
    protected string $foreignTable = '';
    protected string $renderType = '';
    protected string $whereClause = '';

    // Text and RichText
    protected bool $enableRichtext = false;
    protected int $rows = 0;

    // Number
    protected bool $enableSlider = false;
    protected bool $setRange = false;
    protected int $lowerRange = 0;
    protected int $upperRange = 100;
    protected int|float $steps = 1;

    // Color
    protected bool $setValuesColorPicker = false;
    protected array $colorPickerValues = [];

    // Password
    protected bool $renderPasswordGenerator = false;

    // Boolean
    protected string $renderTypeBoolean = '';
    protected array $booleanValues = [];

    // DateTime
    protected string $dbTypeDateTime = '';
    protected string $formatDateTime = '';

    public function __construct(string $propertyName = '')
    {
        if (!empty($propertyName)) {
            $this->name = $propertyName;
        }
    }

    /**
     * DO NOT CALL DIRECTLY! This is being called by addProperty() automatically.
     *
     * @param ClassObject $class the class this property belongs to
     */
    public function setClass(ClassObject $class): void
    {
        $this->class = $class;
    }

    /**
     * Get the domain object this property belongs to.
     *
     * @return DomainObject
     */
    public function getClass(): DomainObject
    {
        return $this->class;
    }

    public function getName(): string
    {
        return $this->name;
    }

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

    public function getHasDefaultValue(): bool
    {
        return isset($this->defaultValue);
    }

    public function getUniqueIdentifier(): string
    {
        return $this->uniqueIdentifier;
    }

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

    public function getRequired(): bool
    {
        return $this->required;
    }

    public function getCascadeRemove(): bool
    {
        return $this->cascadeRemove;
    }

    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    public function setNullable(bool $nullable): void
    {
        $this->nullable = $nullable;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }

    public function isNullableProperty(): bool
    {
        return static::$isNullable;
    }

    public function setExcludeField(bool $excludeField): void
    {
        $this->excludeField = $excludeField;
    }

    public function getExcludeField(): bool
    {
        return $this->excludeField;
    }

    /**
     * Set whether this property is l10n_mode = exclude
     *
     * @param bool $l10nModeExclude
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
            return '@' . ClassBuilder::VALIDATE_ANNOTATION;
        }
        return '';
    }

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
     * Is this property persistable in a database?
     *
     * @return bool true if this property can be persisted in a database
     */
    public function getIsPersistable(): bool
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

    public function getDescriptionNamespace(): string
    {
        return $this->getLabelNamespace() . '.description';
    }

    /**
     * DO NOT CALL DIRECTLY! This is being called by addProperty() automatically.
     *
     * @param DomainObject $domainObject the domain object this property belongs to
     */
    public function setDomainObject(DomainObject $domainObject): void
    {
        $this->domainObject = $domainObject;
    }

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
            return "'" . $this->name . "' => [\n\t'fieldName' => '" . $this->getFieldName() . "'\n],";
        }

        return null;
    }

    public function isNew(): bool
    {
        return $this->new;
    }

    public function setNew(bool $new): void
    {
        $this->new = $new;
    }

    public function getUseRTE(): bool
    {
        return $this->useRTE;
    }

    public function getUnqualifiedType(): string
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function isFileReference(): bool
    {
        return in_array($this->type, ['Image', 'File']);
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function getSelectboxValues(): array
    {
        return $this->selectboxValues;
    }


    public function setSelectboxValues(string $value): void
    {
        $pairs = explode("\n", $value);
        $assocArray = [];

        foreach ($pairs as $pair) {
            $parts = explode(';', $pair);
            if (count($parts) == 2) {
                [$label, $val] = $parts;
                $assocArray[$label] = $val;
            }
        }

        $this->selectboxValues = $assocArray;
    }

    public function getBooleanValues(): array
    {
        return $this->booleanValues;
    }

    public function setBooleanValues(string $value): void
    {
        $pairs = explode("\n", $value);
        // $assocArray = [];
//
        // foreach ($pairs as $pair) {
        //     $parts = explode(';', $pair);
        //     if (count($parts) == 2) {
        //         [$label, $val] = $parts;
        //         $assocArray[$label] = $val;
        //     }
        // }

        $this->booleanValues = $pairs;
    }



    public function isSetValuesColorPicker(): bool
    {
        return $this->setValuesColorPicker;
    }

    public function setSetValuesColorPicker(bool $setValuesColorPicker): void
    {
        $this->setValuesColorPicker = $setValuesColorPicker;
    }

    public function getColorPickerValues(): array
    {
        return $this->colorPickerValues;
    }

    public function setColorPickerValues(string $value): void
    {
        $pairs = explode("\n", $value);
        $assocArray = [];

        foreach ($pairs as $pair) {
            $parts = explode(';', $pair);
            if (count($parts) == 2) {
                [$label, $val] = $parts;
                $assocArray[$label] = $val;
            }
        }

        $this->colorPickerValues = $assocArray;
    }


    public function getForeignTable(): string
    {
        return $this->foreignTable;
    }

    public function setForeignTable(string $foreignTable): void
    {
        $this->foreignTable = $foreignTable;
    }

    public function getRenderType(): string
    {
        return $this->renderType;
    }

    public function setRenderType(string $renderType): void
    {
        $this->renderType = $renderType;
    }

    public function getWhereClause(): string
    {
        return $this->whereClause;
    }

    public function setWhereClause(string $whereClause): void
    {
        $this->whereClause = $whereClause;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    public function getMaxItems(): int
    {
        return $this->maxItems;
    }

    public function setMaxItems(int $maxItems): void
    {
        $this->maxItems = $maxItems;
    }

    public function getMinItems(): int
    {
        return $this->minItems;
    }

    public function setMinItems(int $minItems): void
    {
        $this->minItems = $minItems;
    }

    public function isEnableRichtext(): bool
    {
        return $this->enableRichtext;
    }

    public function setEnableRichtext(bool $enableRichtext): void
    {
        $this->enableRichtext = $enableRichtext;
    }

    public function getRows(): int
    {
        return $this->rows;
    }

    public function setRows(int $rows): void
    {
        $this->rows = $rows;
    }

    public function isEnableSlider(): bool
    {
        return $this->enableSlider;
    }

    public function setEnableSlider(bool $enableSlider): void
    {
        $this->enableSlider = $enableSlider;
    }

    public function isSetRange(): bool
    {
        return $this->setRange;
    }

    public function setSetRange(bool $setRange): void
    {
        $this->setRange = $setRange;
    }

    public function getLowerRange(): int
    {
        return $this->lowerRange;
    }

    public function setLowerRange(int $lowerRange): void
    {
        $this->lowerRange = $lowerRange;
    }

    public function getUpperRange(): int
    {
        return $this->upperRange;
    }

    public function setUpperRange(int $upperRange): void
    {
        $this->upperRange = $upperRange;
    }

    public function getSteps(): float|int
    {
        return $this->steps;
    }

    public function setSteps(float|int $steps): void
    {
        $this->steps = $steps;
    }

    public function isRenderPasswordGenerator(): bool
    {
        return $this->renderPasswordGenerator;
    }

    public function setRenderPasswordGenerator(bool $renderPasswordGenerator): void
    {
        $this->renderPasswordGenerator = $renderPasswordGenerator;
    }

    public function isRenderTypeBoolean(): string
    {
        return $this->renderTypeBoolean;
    }

    public function setRenderTypeBoolean(string $renderTypeBoolean): void
    {
        $this->renderTypeBoolean = $renderTypeBoolean;
    }

    public function getDbTypeDateTime(): string
    {
        return $this->dbTypeDateTime;
    }

    public function setDbTypeDateTime(string $dbTypeDateTime): void
    {
        $this->dbTypeDateTime = $dbTypeDateTime;
    }

    public function getFormatDateTime(): string
    {
        return $this->formatDateTime;
    }

    public function setFormatDateTime(string $formatDateTime): void
    {
        $this->formatDateTime = $formatDateTime;
    }
}
