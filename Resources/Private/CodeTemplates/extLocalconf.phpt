{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
<f:for each="{extension.plugins}" as="plugin">
Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'<k:uppercaseFirst>{plugin.key}</k:uppercaseFirst>',
	array(
		<f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject">'{domainObject.name}' => 'list, show, new, create, edit, update, delete',
		</f:for>
	),
	array(
		<f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject">'{domainObject.name}' => 'create, update, delete',
		</f:for>
	)
);
</f:for>
?>