{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
return array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:{extension.extensionKey}/Resources/Private/Language/locallang_db.xlf:{domainObject.databaseTableName}',
		'label' => '{domainObject.listModuleValueLabel}',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,<f:if condition="{domainObject.sorting}">
		'sortby' => 'sorting',</f:if>
<f:if condition="{extension.supportVersioning}">		'versioningWS' => 2,
		'versioning_followPages' => TRUE,</f:if>
<f:if condition="{extension.supportLocalization}">
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',</f:if>
<f:if condition="{domainObject.addDeletedField}">		'delete' => 'deleted',</f:if>
		'enablecolumns' => array(
<f:if condition="{domainObject.addHiddenField}">			'disabled' => 'hidden',</f:if>
<f:if condition="{domainObject.addStarttimeEndtimeFields}">			'starttime' => 'starttime',
			'endtime' => 'endtime',</f:if>
		),
		'searchFields' => '<f:for each="{domainObject.properties}" as="property">{property.fieldName},</f:for>',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('{domainObject.extension.extensionKey}') . 'Resources/Public/Icons/{domainObject.databaseTableName}.gif'
	),
	'interface' => array(
		'showRecordFieldList' => '<f:if condition="{extension.supportLocalization}">sys_language_uid, l10n_parent, l10n_diffsource, </f:if><f:if condition="{domainObject.addHiddenField}">hidden, </f:if><f:for each="{domainObject.properties}" as="property" iteration="i">{property.fieldName}<f:if condition="{i.isLast}"><f:else>, </f:else></f:if></f:for>',
	),
	'types' => array(
		<f:if condition="{domainObject.hasChildren}"><f:then>'{domainObject.recordType}'</f:then><f:else>'1'</f:else></f:if> => array('showitem' => '<f:if condition="{extension.supportLocalization}">sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, </f:if><f:if condition="{domainObject.addHiddenField}">hidden;;1, </f:if><f:for each="{domainObject.properties}" as="property" iteration="i">{property.fieldName}<f:if condition="{property.useRTE}">;;;richtext:rte_transform[mode=ts_links]</f:if>, </f:for><f:if condition="{domainObject.addStarttimeEndtimeFields}">--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime</f:if>'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
	<f:if condition="{extension.supportLocalization}">
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => '{domainObject.databaseTableName}',
				'foreign_table_where' => 'AND {domainObject.databaseTableName}.pid=###CURRENT_PID### AND {domainObject.databaseTableName}.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		</f:if>
		<f:if condition="{extension.supportVersioning}">
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
	</f:if>
<f:if condition="{domainObject.addHiddenField}">		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),</f:if>
<f:if condition="{domainObject.addStarttimeEndtimeFields}">		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),</f:if>
		<k:format.indent indentation="1"><f:render partial="TCA/PropertiesDefinition.phpt" arguments="{domainObject:domainObject,settings:settings}"/></k:format.indent>
		<f:for each="{k:listForeignKeyRelations(extension: extension, domainObject: domainObject)}" as="relation">
		'{relation.foreignKeyName}' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),</f:for>
	),
);