<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}

<f:for each="{extension.domainObjects}" as="domainObject">
$TCA['{domainObject.databaseTableName}'] = array(
	'ctrl' => $TCA['{domainObject.databaseTableName}']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => '{domainObject.commaSeparatedFieldList}'
	),
	'types' => array(
		'1' => array('showitem' => '{domainObject.commaSeparatedFieldList}')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	),
	'columns' => array(

		'sys_language_uid' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
					array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => array (
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => array (
				'type' => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table' => 'tt_news',
				'foreign_table_where' => 'AND tt_news.uid=###REC_FIELD_l18n_parent### AND tt_news.sys_language_uid IN (-1,0)', // TODO
			)
		),
		'l18n_diffsource' => array(
			'config'=>array(
				'type'=>'passthrough')
		),
		't3ver_label' => array (
			'displayCond' => 'FIELD:t3ver_label:REQ:true',
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.versionLabel',
			'config' => array (
				'type'=>'none',
				'cols' => 27
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type' => 'check'
			)
		),
		<f:for each="{domainObject.properties}" as="property">
		'{property.name}' => array(
			'exclude' => 0,
			'label'   => '{property.name}', // TODO 'LLL:EXT:blog_example/Resources/Private/Language/locallang_db.xml:tx_blogexample_domain_model_blog.title',
			'config'  => array(
				<k:indent indentation="4"><k:render partial="TCA/{property.dataType}.phpt" arguments="{property: property}" /></k:indent>
			)
		),
		</f:for>
		<f:for each="{k:listForeignKeyRelations(extension: extension, domainObject: domainObject)}" as="relation">
		'{relation.foreignKeyName}' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		</f:for>
	),
);
</f:for>
?>