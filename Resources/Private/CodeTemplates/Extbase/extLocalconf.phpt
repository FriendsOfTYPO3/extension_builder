{namespace k=Tx_ExtensionBuilder_ViewHelpers}<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
<f:for each="{extension.plugins}" as="plugin">
Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'<k:format.uppercaseFirst>{plugin.key}</k:format.uppercaseFirst>',
	array(
		<f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject">'{domainObject.name}' => '<f:for each="{domainObject.actions}" as="action" iteration="actionIterator">{action.name}<f:if condition="{actionIterator.isLast} == 0">, </f:if></f:for>',
		</f:for>
	),
	// non-cacheable actions
	array(
		<f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject">'{domainObject.name}' => '<f:for each="{domainObject.actions}" as="action" iteration="actionIterator"><f:if condition="{action.cacheable} == 0">{action.name}<f:if condition="{actionIterator.isLast} == 0">, </f:if></f:if></f:for>',
		</f:for>
	)
);
</f:for>
?>