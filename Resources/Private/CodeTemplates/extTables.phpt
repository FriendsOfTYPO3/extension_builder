<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Pi1',
	'{extension.name}'
);

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', '{extension.name}');

//$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi1'] = 'pi_flexform';
//t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_pi1', 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_list.xml');

<f:for each="{extension.domainObjects}" as="domainObject">
t3lib_extMgm::addLLrefForTCAdescr('{domainObject.databaseTableName}','EXT:{extension.extensionKey}/Resources/Private/Language/locallang_csh_{domainObject.databaseTableName}.xml');
t3lib_extMgm::allowTableOnStandardPages('{domainObject.databaseTableName}');
$TCA['{domainObject.databaseTableName}'] = array (
	'ctrl' => array (
		'title'             => 'LLL:EXT:{extension.extensionKey}/Resources/Private/Language/locallang_db.xml:{domainObject.databaseTableName}',
		'label' 			=> '{domainObject.listModuleValueLabel}',
		'tstamp' 			=> 'tstamp',
		'crdate' 			=> 'crdate',
		'versioningWS' 		=> 2,
		'versioning_followPages'	=> TRUE,
		'origUid' 			=> 't3_origuid',
		'languageField' 	=> 'sys_language_uid',
		'transOrigPointerField' 	=> 'l18n_parent',
		'transOrigDiffSourceField' 	=> 'l18n_diffsource',
		'delete' 			=> 'deleted',
		'enablecolumns' 	=> array(
			'disabled' => 'hidden'
			),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/tca.php',
		'iconfile' 			=> t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/{domainObject.databaseTableName}.gif'
	)
);
</f:for><f:for each="{extension.domainObjects}" as="domainObject"><f:for each="{domainObject.properties}" as="property"><f:if condition="{k:isOfType(object: property, type: 'Property_Relation_ManyToManyRelation')}">
t3lib_extMgm::allowTableOnStandardPages('{property.relationTableName}');
$TCA['{property.relationTableName}'] = array (
	'ctrl' => array (
		'title'             => '<k:humanize>{domainObject.name}</k:humanize> <-> <k:humanize>{property.foreignClass.name}</k:humanize>',
		'label'				=> 'uid_local',
		'label_alt'			=> 'uid_foreign',
		'label_alt_force'	=> TRUE,
		'tstamp'            => 'tstamp',
		'crdate'            => 'crdate',
		'enablecolumns'     => array(
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/relation.gif'
	)
);</f:if></f:for></f:for>

?>