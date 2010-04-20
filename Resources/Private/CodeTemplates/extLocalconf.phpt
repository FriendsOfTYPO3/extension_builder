<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi1',
	array(
		<f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject">'{domainObject.name}' => 'index, show, new, create, edit, update, delete',</f:for>
	),
	array(
		<f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject">'{domainObject.name}' => 'create, update, delete',</f:for>
	)
);

?>