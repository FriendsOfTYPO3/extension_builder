{namespace k=Tx_ExtensionBuilder_ViewHelpers}<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
<f:if condition="{domainObject.mapToTable}"><f:then>
<k:mapping domainObject="{domainObject}" renderCondition="isMappedToInternalTable">
<k:render partial="TCA/Columns.phpt" arguments="{domainObject:domainObject, settings:settings}" />
</k:mapping></f:then><f:else>
$TCA['{domainObject.databaseTableName}'] = array(
	'ctrl' => $TCA['{domainObject.databaseTableName}']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden<f:for each="{domainObject.properties}" as="property">, {property.fieldName}</f:for>',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1<f:for each="{domainObject.properties}" as="property">, {property.fieldName}</f:for>,--div--;LLL:EXT:cms/locallang_ttc.{locallangFileFormat}:tabs.access,starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.{locallangFileFormat}:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.{locallangFileFormat}:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.{locallangFileFormat}:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.{locallangFileFormat}:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
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
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.{locallangFileFormat}:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.{locallangFileFormat}:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.{locallangFileFormat}:LGL.starttime',
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
			'label' => 'LLL:EXT:lang/locallang_general.{locallangFileFormat}:LGL.endtime',
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
		),<f:for each="{domainObject.properties}" as="property">
		'{property.fieldName}' => array(
			'exclude' => <f:if condition="{property.excludeField}"><f:then>1</f:then><f:else>0</f:else></f:if>,
			'label' => 'LLL:EXT:{extension.extensionKey}/Resources/Private/Language/locallang_db.{locallangFileFormat}:{property.labelNamespace}',
			'config' => array(
				<k:format.indent indentation="4"><k:render partial="TCA/{property.dataType}.phpt" arguments="{property: property,extension:extension,settings:settings,locallangFileFormat:locallangFileFormat}" /></k:format.indent>
			),<f:if condition="{property.useRTE}">
			'defaultExtras' => 'richtext[]',</f:if>
		),</f:for><f:for each="{k:listForeignKeyRelations(extension: extension, domainObject: domainObject)}" as="relation">
		'{relation.foreignKeyName}' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),</f:for>
	),
);

<f:if condition="{domainObject.childObjects}">
<k:render partial="TCA/TypeField.phpt" arguments="{domainObject:domainObject, settings:settings}" />
</f:if>

</f:else></f:if>

<f:for each="{domainObject.childObjects}" as="childObject">require("{childObject.name}.php");</f:for>
?>
