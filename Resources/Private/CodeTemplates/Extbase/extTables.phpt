{namespace k=Tx_ExtensionBuilder_ViewHelpers}<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
<f:for each="{extension.Plugins}" as="plugin">
Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'<k:format.uppercaseFirst>{plugin.key}</k:format.uppercaseFirst>',
	'<k:format.quoteString>{plugin.name}</k:format.quoteString>'
);
<f:if condition="{plugin.switchableControllerActions}">
$pluginSignature = str_replace('_','',$_EXTKEY) . '_{plugin.key}';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_{plugin.key}.xml');
</f:if></f:for>

<f:if condition="{extension.BackendModules}">
if (TYPO3_MODE === 'BE') {
<f:for each="{extension.BackendModules}" as="backendModule">
	/**
	 * Registers a Backend Module
	 */
	Tx_Extbase_Utility_Extension::registerModule(
		$_EXTKEY,
		'{backendModule.mainModule}',	 // Make module a submodule of '{backendModule.mainModule}'
		'{backendModule.key}',	// Submodule key
		'',						// Position
		array(<f:if condition="{backendModule.controllerActionCombinations}"><f:then>
			<f:for each="{backendModule.controllerActionCombinations}" as="actionNames" key="controllerName">'{controllerName}' => '<f:for each="{actionNames}" as="actionName" iteration="i">{actionName}<f:if condition="{i.isLast} == 0">, </f:if></f:for>',
			</f:for></f:then><f:else>
			<f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject">'{domainObject.name}' => '<f:for each="{domainObject.actions}" as="action" iteration="actionIterator">{action.name}<f:if condition="{actionIterator.isLast} == 0">, </f:if></f:for>',</f:for></f:else></f:if>
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_{backendModule.key}.{locallangFileFormat}',
		)
	);
</f:for>
}
</f:if>
t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', '<k:format.quoteString>{extension.name}</k:format.quoteString>');

<f:for each="{extension.domainObjects}" as="domainObject">
	<k:mapping renderCondition="needsTypeField" domainObject="{domainObject}">
<k:render partial="TCA/TypeField.phpt" arguments="{domainObject:domainObject, settings:settings, locallangFileFormat:locallangFileFormat}" />
	</k:mapping>
	<f:if condition="{domainObject.mapToTable}">
		<f:then>
			<k:mapping domainObject="{domainObject}" renderCondition="isMappedToExternalTable">
<k:render partial="TCA/Columns.phpt" arguments="{domainObject:domainObject, settings:settings, locallangFileFormat:locallangFileFormat}" />
			</k:mapping>
		</f:then>
		<f:else>
t3lib_extMgm::addLLrefForTCAdescr('{domainObject.databaseTableName}', 'EXT:{extension.extensionKey}/Resources/Private/Language/locallang_csh_{domainObject.databaseTableName}.{locallangFileFormat}');
t3lib_extMgm::allowTableOnStandardPages('{domainObject.databaseTableName}');
$TCA['{domainObject.databaseTableName}'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:{extension.extensionKey}/Resources/Private/Language/locallang_db.{locallangFileFormat}:{domainObject.databaseTableName}',
		'label' => '{domainObject.listModuleValueLabel}',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
<f:if condition="{domainObject.sorting}">		'sortby' => 'sorting',</f:if>
<f:if condition="{extension.supportVersioning}">		'versioningWS' => 2,
		'versioning_followPages' => TRUE,</f:if>
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => '<f:for each="{domainObject.properties}" as="property">{property.fieldName},</f:for>',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/{domainObject.name}.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/{domainObject.databaseTableName}.gif'
	),
);
		</f:else>
	</f:if>
</f:for>
?>
