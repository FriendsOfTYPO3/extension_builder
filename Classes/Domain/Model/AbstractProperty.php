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
 * Base class for all properties in the object schema.
 *
 * @package ExtbaseKickstarter
 * @version $ID:$
 */
abstract class Tx_ExtbaseKickstarter_Domain_Model_AbstractProperty {
	
	/**
	 * Reserved words by TYPO3 and MySQL
	 * @var array
	 */
	protected $reservedWords = array(
		'uid',
		'pid',
		'endtime',
		'starttime',
		'sorting',
		'fe_group',
		'hidden',
		'deleted',
		'cruser_id',
		'crdate',
		'tstamp',
		'data',
		'table',
		'field',
		'key',
		'desc',
		'all',
		'and',
		'asensitive',
		'bigint',
		'both',
		'cascade',
		'char',
		'character',
		'collate',
		'column',
		'connection',
		'convert',
		'current_date',
		'current_user',
		'databases',
		'day_minute',
		'decimal',
		'default',
		'delayed',
		'describe',
		'distinctrow',
		'drop',
		'else',
		'escaped',
		'explain',
		'float',
		'for',
		'from',
		'group',
		'hour_microsecond',
		'if',
		'index',
		'inout',
		'int',
		'int3',
		'integer',
		'is',
		'key',
		'leading',
		'like',
		'load',
		'lock',
		'longtext',
		'match',
		'mediumtext',
		'minute_second',
		'natural',
		'null',
		'optimize',
		'or',
		'outer',
		'primary',
		'raid0',
		'real',
		'release',
		'replace',
		'return',
		'rlike',
		'second_microsecond',
		'separator',
		'smallint',
		'specific',
		'sqlstate',
		'sql_cal_found_rows',
		'starting',
		'terminated',
		'tinyint',
		'trailing',
		'undo',
		'unlock',
		'usage',
		'utc_date',
		'values',
		'varcharacter',
		'where',
		'write',
		'year_month',
		'asensitive',
		'call',
		'condition',
		'connection',
		'continue',
		'cursor',
		'declare',
		'deterministic',
		'each',
		'elseif',
		'exit',
		'fetch',
		'goto',
		'inout',
		'insensitive',
		'iterate',
		'label',
		'leave',
		'loop',
		'modifies',
		'out',
		'reads',
		'release',
		'repeat',
		'return',
		'schema',
		'schemas',
		'sensitive',
		'specific',
		'sql',
		'sqlexception',
		'sqlstate',
		'sqlwarning',
		'trigger',
		'undo',
		'upgrade',
		'while'
	);
	
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
	 * Whether the property is an exclude field
	 * @var boolean
	 */
	protected $excludeField;

	/**
	 * The domain object this property belongs to.
	 * @var Tx_ExtbaseKickstarter_Domain_Model_DomainObject
	 */
	protected $domainObject;

	/**
	 * DO NOT CALL DIRECTLY! This is being called by addProperty() automatically.
	 *
	 * @param Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject the domain object this property belongs to
	 */
	public function setDomainObject(Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject) {
		$this->domainObject = $domainObject;
	}

	/**
	 * Get the domain object this property belongs to.
	 *
	 * @return Tx_ExtbaseKickstarter_Domain_Model_DomainObject
	 */
	public function getDomainObject() {
		return $this->domainObject;
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
	 * Returns a field name used in the database. This is the property name converted 
	 * to lowercase underscore (mySpecialProperty -> my_special_property).
	 *
	 * @return string the field name in lowercase underscore
	 */
	public function getFieldName() {
		$fieldName = Tx_Extbase_Utility_Extension::convertCamelCaseToLowerCaseUnderscored($this->name);
		if (in_array($fieldName, $this->reservedWords)) {
			$fieldName = $this->domainObject->getExtension()->getShorthandForTypoScript() . '_' . $fieldName;
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
	 * Get property description to be used in comments
	 *
	 * @return string Property description
	 */
	public function getDescription() {
		if ($this->description){
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
	 * Template Method which should return the type hinting information
	 * being used in PHPDoc Comments.
	 * Examples: integer, string, Tx_FooBar_Something, Tx_Extbase_Persistence_ObjectStorage<Tx_FooBar_Something>
	 *
	 * @return string
	 */
	abstract public function getTypeForComment();

	/**
	 * Template method which should return the PHP type hint
	 * Example: Tx_Extbase_Persistence_ObjectStorage, array, Tx_FooBar_Something
	 *
	 * @return string
	 */
	abstract public function getTypeHint();

	/**
	 * Get PHP type hint with a single trailing whitespace appended if needed, or if no type hint is set, omit this trailing whitespace.
	 *
	 * @return string
	 */
	public function getTypeHintWithTrailingWhiteSpace() {
		if ($typehint = $this->getTypeHint()) {
			return $typehint . ' ';
		}
	}

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
	 * Get the data type of this property. This is the last part after Tx_ExtbaseKickstarter_Domain_Model_Property_*
	 * 
	 * @return string the data type of this property
	 */
	public function getDataType() {
		return substr(get_class($this), strlen('Tx_ExtbaseKickstarter_Domain_Model_Property_'));
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
}
?>