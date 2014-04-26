<?php
namespace EBT\ExtensionBuilder\Service;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Nico de Haen
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
 * provides validation against reserved words
 */

class ValidationService implements \TYPO3\CMS\Core\SingletonInterface {
	/**
	 * TODO: Check this list if it's still up to date
	 * Reserved words by MySQL
	 *
	 * @var string[]
	 */
	static public $reservedMYSQLWords = array(
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
	 * column names used by TYPO3
	 *
	 * @var string[]
	 */
	static public $reservedTYPO3ColumnNames = array(
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
		't3ver_move_id',
		't3_origuid',
		'sys_language_uid',
		'l18n_parent',
		'l18n_diffsource'
	);

	/**
	 * all these words may not be used as class or domain object names
	 *
	 * @var string[]
	 */
	static public $reservedExtbaseNames = array(
		'Class',
		'Format',
		'Action',
		'Interface',
		'Controller',
		'Closure',
		'Exception',
		'Iterator',
		'Traversable',
		'SeekableIterator',
		'Countable',
		'ReflectionException',
		'Reflection',
		'ReflectionFunction',
		'ReflectionParameter',
		'ReflectionMethod',
		'ReflectionClass',
		'ReflectionObject',
		'ReflectionProperty',
		'ReflectionExtension',
		'CachingRecursiveIterator',
		'ArrayObject',
		'AppendIterator',
		'ArrayIterator',
		'CachingIterator',
		'CallbackFilterIterator',
		'DirectoryIterator',
		'EmptyIterator',
		'FilesystemIterator',
		'FilterIterator',
		'GlobIterator',
		'InfiniteIterator',
		'IteratorIterator',
		'LimitIterator',
		'MultipleIterator',
		'NoRewindIterator',
		'ParentIterator',
		'RecursiveArrayIterator',
		'RecursiveCachingIterator',
		'RecursiveCallbackFilterIterator',
		'RecursiveDirectoryIterator',
		'RecursiveFilterIterator',
		'RecursiveIteratorIterator',
		'RecursiveRegexIterator',
		'RecursiveTreeIterator',
		'RegexIterator',
	);

	/**
	 *
	 * @param string $word
	 *
	 * @return boolean
	 */
	static public function isReservedTYPO3Word($word) {
		if (in_array(\TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($word), self::$reservedTYPO3ColumnNames)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 *
	 * @param string $word
	 *
	 * @return boolean
	 */
	static public function isReservedExtbaseWord($word) {
		if (in_array($word, self::$reservedExtbaseNames)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 *
	 * @param string $word
	 *
	 * @return boolean
	 */
	static public function isReservedMYSQLWord($word) {
		if (in_array(strtoupper($word), self::$reservedMYSQLWords)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 *
	 * @param string $word
	 *
	 * @return boolean
	 */
	static public function isReservedWord($word) {
		if (self::isReservedMYSQLWord($word) || self::isReservedTYPO3Word($word)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
