<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nico de Haen, Ingmar Schlecht, Stephan Petzl
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
 * property representing a "property" in the context of software development
 *
 * @version $ID:$
 */
abstract class Tx_ExtensionBuilder_Domain_Model_DomainObject_AbstractProperty {


	/**
	 *
	 * @var string
	 */
	protected $uniqueIdentifier;

	/**
	 * Name of the property
	 * @var string
	 */
	protected $name;

	/**
	 * Description of property
	 * @var string
	 */
	protected $description;

	/**
	 * Whether the property is required
	 * @var boolean
	 */
	protected $required;

	/**
	 * The string representation of the properties default value
	 * @var string
	 */
	protected $defaultValue;

	/**
	 * Is an upload folder required for this property
	 *
	 * @var boolean
	 */
	protected $needsUploadFolder = FALSE;

	/**
	 * The domain object this property belongs to.
	 * @var Tx_ExtensionBuilder_Domain_Model_DomainObject
	 */
	protected $class;

	/**
	 * is set to TRUE, if this property was new added
	 *
	 * @var boolean
	 */
	protected $new = TRUE;

	/**
	 * use RTE in Backend
	 *
	 * @var boolean
	 */
	protected $useRTE = FALSE;

	/**
	 *
	 * @param string $propertyName
	 * @return void
	 */
	public function __construct($propertyName = '') {
		if (!empty($propertyName)) {
			$this->name = $propertyName;
		}
	}

	/**
	 * DO NOT CALL DIRECTLY! This is being called by addProperty() automatically.
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_Class_Schema $class the class this property belongs to
	 */
	public function setClass(Tx_ExtensionBuilder_Domain_Model_Class_Schema $class) {
		$this->class = $class;
	}

	/**
	 * Get the domain object this property belongs to.
	 *
	 * @return Tx_ExtensionBuilder_Domain_Model_Class_Schema
	 */
	public function getClass() {
		return $this->class;
	}

	/**
	 * Get property name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set property name
	 *
	 * @param string $name Property name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Get property defaultValue
	 *
	 * @return string
	 */
	public function getDefaultValue() {
		return $this->defaultValue;
	}

	/**
	 * Set property defaultValue
	 *
	 * @param string $defaultValue
	 */
	public function setDefaultValue($defaultValue) {
		$this->defaultValue = $defaultValue;
	}

	/**
	 *
	 * @return boolean
	 */
	public function getHasDefaultValue() {
		return isset($this->defaultValue);
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
	 *
	 * @return boolean TRUE (if property is of type relation any to many)
	 */
	public function isAnyToManyRelation() {
		return is_subclass_of($this, 'Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_AnyToManyRelation');
	}


	/**
	 *
	 * @return boolean TRUE (if property is of type relation)
	 */
	public function isRelation() {
		return is_subclass_of($this, 'Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_AbstractRelation');
	}

	/**
	 *
	 * @return boolean TRUE (if property is of type boolean)
	 */
	public function isBoolean() {
		return is_a($this, 'Tx_ExtensionBuilder_Domain_Model_DomainObject_BooleanProperty');
	}


	/**
	 * Get property description to be used in comments
	 *
	 * @return string Property description
	 */
	public function getDescription() {
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
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Returns a field name used in the database. This is the property name converted
	 * to lowercase underscore (mySpecialProperty -> my_special_property).
	 *
	 * @return string the field name in lowercase underscore
	 */
	public function getFieldName() {
		$fieldName = \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($this->name);
		if (Tx_ExtensionBuilder_Domain_Validator_ExtensionValidator::isReservedMYSQLWord($fieldName)) {
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
	 * Examples: integer, string, Tx_FooBar_Something, \TYPO3\CMS\Extbase\Persistence\Generic\ObjectStorage<Tx_FooBar_Something>
	 *
	 * @return string
	 */
	abstract public function getTypeForComment();

	/**
	 * Template method which should return the PHP type hint
	 * Example: \TYPO3\CMS\Extbase\Persistence\Generic\ObjectStorage, array, Tx_FooBar_Something
	 *
	 * @return string
	 */
	abstract public function getTypeHint();

	/**
	 * TRUE if this property is required, FALSE otherwise.
	 *
	 * @return boolean
	 */
	public function getRequired() {
		return $this->required;
	}

	/**
	 * Set whether this property is required
	 *
	 * @param boolean $required
	 */
	public function setRequired($required) {
		$this->required = $required;
	}

	/**
	 * Set whether this property is exclude field
	 *
	 * @param boolean $excludeField
	 * @return void
	 */
	public function setExcludeField($excludeField) {
		$this->excludeField = $excludeField;
	}

	/**
	 * TRUE if this property is an exclude field, FALSE otherwise.
	 *
	 * @return boolean
	 */
	public function getExcludeField() {
		return $this->excludeField;
	}

	/**
	 * Get the validate annotation to be used in the domain model for this property.
	 *
	 * @return string
	 */
	public function getValidateAnnotation() {
		if ($this->required) {
			return '@validate NotEmpty';
		}
		return '';
	}

	/**
	 * Get the data type of this property. This is the last part after Tx_ExtensionBuilder_Domain_Model_DomainObject_*
	 *
	 * @return string the data type of this property
	 */
	public function getDataType() {
		return substr(get_class($this), strlen('Tx_ExtensionBuilder_Domain_Model_DomainObject_'));
	}

	/**
	 * Is this property displayable inside a Fluid template?
	 *
	 * @return boolean TRUE if this property can be displayed inside a fluid template
	 */
	public function getIsDisplayable() {
		return TRUE;
	}

	/**
	 * The string to be used inside object accessors to display this property.
	 *
	 * @return string
	 */
	public function getNameToBeDisplayedInFluidTemplate() {
		return $this->name;
	}

	/**
	 * The locallang key for this property which contains the label.
	 *
	 * @return <type>
	 */
	public function getLabelNamespace() {
		return $this->domainObject->getLabelNamespace() . '.' . $this->getFieldName();
	}

	/**
	 * DO NOT CALL DIRECTLY! This is being called by addProperty() automatically.
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject the domain object this property belongs to
	 */
	public function setDomainObject(Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject) {
		$this->domainObject = $domainObject;
	}

	/**
	 *
	 * @return Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 */
	public function getDomainObject() {
		return $this->domainObject;
	}

	/**
	 * The Typoscript statement used by extbase to map the property to
	 * a specific database fieldname
	 *
	 * @return string $mappingStatement
	 */
	public function getMappingStatement() {
		if ($this->getFieldName() != \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($this->name)) {
			return $this->getFieldName() . '.mapOnProperty = ' . $this->name;
		}
		else return NULL;
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
	 *
	 */
	public function isNew() {
		return $this->new;
	}


	/**
	 *
	 * @param boolean $new
	 */
	public function setNew($new) {
		$this->new = $new;
	}

	/**
	 * Getter for $useRTE
	 *
	 * @return boolean $useRTE
	 */
	public function getUseRTE() {
		return $this->useRTE;
	}

	public function getUnqualifiedType() {
		return substr($this->getTypeForComment(),1);
	}

}

?>
