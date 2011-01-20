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
 * @package ExtbaseKickstarter
 * @version $ID:$
 */
abstract class Tx_ExtbaseKickstarter_Domain_Model_DomainObject_AbstractProperty {

	/**
	 * Reserved words by MySQL
	 * @var array
	 */
	static protected $reservedMYSQLWords = array(
		'ACCESSIBLE',
		'ADD',
		'ALL',
		'ALTER',
		'ANALYZE',
		'AND',
		'AS',
		'ASC',
		'ASENSITIVE',
		'BEFORE',
		'BETWEEN',
		'BIGINT',
		'BINARY',
		'BLOB',
		'BOTH',
		'BY',
		'CALL',
		'CASCADE',
		'CASE',
		'CHANGE',
		'CHAR',
		'CHARACTER',
		'CHECK',
		'COLLATE',
		'COLUMN',
		'CONDITION',
		'CONSTRAINT',
		'CONTINUE',
		'CONVERT',
		'CREATE',
		'CROSS',
		'CURRENT_DATE',
		'CURRENT_TIME',
		'CURRENT_TIMESTAMP',
		'CURRENT_USER',
		'CURSOR',
		'DATABASE',
		'DATABASES',
		'DAY_HOUR',
		'DAY_MICROSECOND',
		'DAY_MINUTE',
		'DAY_SECOND',
		'DEC',
		'DECIMAL',
		'DECLARE',
		'DEFAULT',
		'DELAYED',
		'DELETE',
		'DESC',
		'DESCRIBE',
		'DETERMINISTIC',
		'DISTINCT',
		'DISTINCTROW',
		'DIV',
		'DOUBLE',
		'DROP',
		'DUAL',
		'EACH',
		'ELSE',
		'ELSEIF',
		'ENCLOSED',
		'ESCAPED',
		'EXISTS',
		'EXIT',
		'EXPLAIN',
		'FALSE',
		'FETCH',
		'FLOAT',
		'FLOAT4',
		'FLOAT8',
		'FOR',
		'FORCE',
		'FOREIGN',
		'FROM',
		'FULLTEXT',
		'GENERAL',
		'GOTO',
		'GRANT',
		'GROUP',
		'HAVING',
		'HIGH_PRIORITY',
		'HOUR_MICROSECOND',
		'HOUR_MINUTE',
		'HOUR_SECOND',
		'IF',
		'IGNORE',
		'IGNORE_SERVER_IDS',
		'IN',
		'INDEX',
		'INFILE',
		'INNER',
		'INOUT',
		'INSENSITIVE',
		'INSERT',
		'INT',
		'INT1',
		'INT2',
		'INT3',
		'INT4',
		'INT8',
		'INTEGER',
		'INTERVAL',
		'INTO',
		'IS',
		'ITERATE',
		'JOIN',
		'KEY',
		'KEYS',
		'KILL',
		'LABEL',
		'LEADING',
		'LEAVE',
		'LEFT',
		'LIKE',
		'LIMIT',
		'LINEAR',
		'LINES',
		'LOAD',
		'LOCALTIME',
		'LOCALTIMESTAMP',
		'LOCK',
		'LONG',
		'LONGBLOB',
		'LONGTEXT',
		'LOOP',
		'LOW_PRIORITY',
		'MASTER_HEARTBEAT_PERIOD',
		'MASTER_SSL_VERIFY_SERVER_CERT',
		'MATCH',
		'MAXVALUE',
		'MEDIUMBLOB',
		'MEDIUMINT',
		'MEDIUMTEXT',
		'MIDDLEINT',
		'MINUTE_MICROSECOND',
		'MINUTE_SECOND',
		'MOD',
		'MODIFIES',
		'NATURAL',
		'NOT',
		'NO_WRITE_TO_BINLOG',
		'NULL',
		'NUMERIC',
		'ON',
		'OPTIMIZE',
		'OPTION',
		'OPTIONALLY',
		'OR',
		'ORDER',
		'OUT',
		'OUTER',
		'OUTFILE',
		'PRECISION',
		'PRIMARY',
		'PROCEDURE',
		'PURGE',
		'RANGE',
		'READ',
		'READS',
		'READ_WRITE',
		'READ_ONLY',
		'REAL',
		'REFERENCES',
		'REGEXP',
		'RELEASE',
		'RENAME',
		'REPEAT',
		'REPLACE',
		'REQUIRE',
		'RESIGNAL',
		'RESTRICT',
		'RETURN',
		'REVOKE',
		'RIGHT',
		'RLIKE',
		'SCHEMA',
		'SCHEMAS',
		'SECOND_MICROSECOND',
		'SELECT',
		'SENSITIVE',
		'SEPARATOR',
		'SET',
		'SHOW',
		'SIGNAL',
		'SLOW',
		'SMALLINT',
		'SPATIAL',
		'SPECIFIC',
		'SQL',
		'SQLEXCEPTION',
		'SQLSTATE',
		'SQLWARNING',
		'SQL_BIG_RESULT',
		'SQL_CALC_FOUND_ROWS',
		'SQL_SMALL_RESULT',
		'SSL',
		'STARTING',
		'STRAIGHT_JOIN',
		'TABLE',
		'TERMINATED',
		'THEN',
		'TINYBLOB',
		'TINYINT',
		'TINYTEXT',
		'TO',
		'TRAILING',
		'TRIGGER',
		'TRUE',
		'UNDO',
		'UNION',
		'UNIQUE',
		'UNLOCK',
		'UNSIGNED',
		'UPDATE',
		'USAGE',
		'USE',
		'USING',
		'UTC_DATE',
		'UTC_TIME',
		'UTC_TIMESTAMP',
		'VALUES',
		'VARBINARY',
		'VARCHAR',
		'VARCHARACTER',
		'VARYING',
		'WHEN',
		'WHERE',
		'WHILE',
		'WITH',
		'WRITE',
		'XOR',
		'YEAR_MONTH',
		'ZEROFILL'
	);
	
	/**
	 * 
	 * column names used by TYPO3
	 * @var array
	 */
	static protected $reservedTYPO3ColumnNames = array(
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
		'sys_language',
		't3ver_oid',
		't3ver_id',
		't3ver_wsid',
		't3ver_label',
		't3ver_state',
		't3ver_stage',
		't3ver_count',
		't3ver_tstamp',
		't3_origuid',
		'sys_language_uid',
		'l18n_parent',
		'l18n_diffsource'
	);
	
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
	 * The domain object this property belongs to.
	 * @var Tx_ExtbaseKickstarter_Domain_Model_DomainObject
	 */
	protected $class;

	/**
	 * DO NOT CALL DIRECTLY! This is being called by addProperty() automatically.
	 *
	 * @param Tx_ExtbaseKickstarter_Domain_Model_Class_Schema $class the class this property belongs to
	 */
	public function setClass(Tx_ExtbaseKickstarter_Domain_Model_Class_Schema $class) {
		$this->class = $class;
	}

	/**
	 * Get the domain object this property belongs to.
	 *
	 * @return Tx_ExtbaseKickstarter_Domain_Model_Class_Schema
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
	 * @return boolean true (if property is of type relation any to many)
	 */
	public function isAnyToManyRelation(){
		return is_subclass_of($this, 'Tx_ExtbaseKickstarter_Domain_Model_DomainObject_Relation_AnyToManyRelation');
	}
	

	/**
	 * 
	 * @return boolean true (if property is of type relation)
	 */
	public function isRelation(){
		return is_subclass_of($this, 'Tx_ExtbaseKickstarter_Domain_Model_DomainObject_Relation_AbstractRelation');
	}
	
	/**
	 * 
	 * @return boolean true (if property is of type boolean)
	 */
	public function isBoolean(){
		return is_a($this, 'Tx_ExtbaseKickstarter_Domain_Model_DomainObject_BooleanProperty');
	}
	

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
	 * Returns a field name used in the database. This is the property name converted
	 * to lowercase underscore (mySpecialProperty -> my_special_property).
	 *
	 * @return string the field name in lowercase underscore
	 */
	public function getFieldName() {
		$fieldName = Tx_Extbase_Utility_Extension::convertCamelCaseToLowerCaseUnderscored($this->name);
		if ($this->isReservedWord($fieldName)) {
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
	 * Get the data type of this property. This is the last part after Tx_ExtbaseKickstarter_Domain_Model_DomainObject_*
	 *
	 * @return string the data type of this property
	 */
	public function getDataType() {
		return substr(get_class($this), strlen('Tx_ExtbaseKickstarter_Domain_Model_DomainObject_'));
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
	 *
	 * @param string $propertyName
	 * @return void
	 */
	public function __construct($propertyName){
		$this->name = $propertyName;
	}

	/**
	 * DO NOT CALL DIRECTLY! This is being called by addProperty() automatically.
	 *
	 * @param Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject the domain object this property belongs to
	 */
	public function setDomainObject(Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject) {
		$this->domainObject = $domainObject;
	}
	
	/**
	 * 
	 * @return Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject
	 */
	public function getDomainObject() {
		return $this->domainObject;
	}
	
	/**
	 * 
	 * @param string $word
	 */
	static public function isReservedTYPO3Word($word){
		if(in_array(Tx_Extbase_Utility_Extension::convertCamelCaseToLowerCaseUnderscored($word),self::$reservedTYPO3ColumnNames)){
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * TODO: Enable property names with reserved MYSQL words 
	 *       by mapping properties to a prefixed column name
	 * 
	 * @param string $word
	 */
	static public function isReservedMYSQLWord($word){
		if(in_array(strtoupper($word),self::$reservedMYSQLWords)){
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * 
	 * @param string $word
	 */
	static public function isReservedWord($word){
		if(self::isReservedMYSQLWord($word) || self::isReservedTYPO3Word($word)){
			return true;
		}
		else {
			return false;
		}
	}
}

?>
