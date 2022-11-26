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

namespace EBT\ExtensionBuilder\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * provides validation against reserved words
 */
class ValidationService implements SingletonInterface
{
    /**
     * Reserved words by MySQL
     *
     * @see https://dev.mysql.com/doc/refman/8.0/en/keywords.html
     *
     * @var string[]
     */
    public static array $reservedMYSQLWords = [
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
        'CUBE',
        'CUME_DIST',
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
        'DENSE_RANK',
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
        'EMPTY',
        'ENCLOSED',
        'ESCAPED',
        'EXCEPT',
        'EXISTS',
        'EXIT',
        'EXPLAIN',

        'FALSE',
        'FETCH',
        'FIRST_VALUE',
        'FLOAT',
        'FLOAT4',
        'FLOAT8',
        'FOR',
        'FORCE',
        'FOREIGN',
        'FROM',
        'FULLTEXT',
        'FUNCTION',

        'GENERATED',
        'GET',
        'GRANT',
        'GROUP',
        'GROUPING',
        'GROUPS',

        'HAVING',
        'HIGH_PRIORITY',
        'HOUR_MICROSECOND',
        'HOUR_MINUTE',
        'HOUR_SECOND',

        'IF',
        'IGNORE',
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
        'IO_AFTER_GTIDS',
        'IO_BEFORE_GTIDS',
        'IS',
        'ITERATE',

        'JOIN',
        'JSON_TABLE',

        'KEY',
        'KEYS',
        'KILL',

        'LAG',
        'LAST_VALUE',
        'LATERAL',
        'LEAD',
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

        'MASTER_BIND',
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
        'NTH_VALUE',
        'NTILE',
        'NULL',
        'NUMERIC',

        'OF',
        'ON',
        'OPTIMIZE',
        'OPTIMIZER_COSTS',
        'OPTION',
        'OPTIONALLY',
        'OR',
        'ORDER',
        'OUT',
        'OUTER',
        'OUTFILE',
        'OVER',

        'PARTITION',
        'PERCENT_RANK',
        'PRECISION',
        'PRIMARY',
        'PROCEDURE',
        'PURGE',

        'RANGE',
        'RANK',
        'READ',
        'READS',
        'READ_WRITE',
        'REAL',
        'RECURSIVE',
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
        'ROW',
        'ROWS',
        'ROW_NUMBER',

        'SCHEMA',
        'SCHEMAS',
        'SECOND_MICROSECOND',
        'SELECT',
        'SENSITIVE',
        'SEPARATOR',
        'SET',
        'SHOW',
        'SIGNAL',
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
        'STORED',
        'STRAIGHT_JOIN',
        'SYSTEM',

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
        'VIRTUAL',

        'WHEN',
        'WHERE',
        'WHILE',
        'WINDOW',
        'WITH',
        'WRITE',

        'XOR',

        'YEAR_MONTH',

        'ZEROFILL'
    ];
    /**
     * column names used by TYPO3
     *
     * @var string[]
     */
    public static array $reservedTYPO3ColumnNames = [
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
    ];
    /**
     * all these words may not be used as class or domain object names
     *
     * @var string[]
     */
    public static array $reservedExtbaseNames = [
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
    ];

    public static function isReservedTYPO3Word(string $word): bool
    {
        return in_array(GeneralUtility::camelCaseToLowerCaseUnderscored($word), self::$reservedTYPO3ColumnNames);
    }

    public static function isReservedExtbaseWord(string $word): bool
    {
        return in_array($word, self::$reservedExtbaseNames);
    }

    public static function isReservedMYSQLWord(string $word): bool
    {
        return in_array(strtoupper($word), self::$reservedMYSQLWords);
    }

    public static function isReservedWord(string $word): bool
    {
        return self::isReservedMYSQLWord($word) || self::isReservedTYPO3Word($word);
    }
}
