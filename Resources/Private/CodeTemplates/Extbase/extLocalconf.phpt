{namespace k=Tx_ExtensionBuilder_ViewHelpers}<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
<f:for each="{extension.plugins}" as="plugin">
Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'<k:format.uppercaseFirst>{plugin.key}</k:format.uppercaseFirst>',
	array(<f:if condition="{plugin.cacheableControllerActions}"><f:then>
		<f:for each="{plugin.cacheableControllerActions}" as="cachableControllerAction">'{cachableControllerAction.controller}' => '{cachableControllerAction.actions}',
		</f:for></f:then><f:else>
		<f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject">'{domainObject.name}' => '<f:for each="{domainObject.actions}" as="action" iteration="actionIterator">{action.name}<f:if condition="{actionIterator.isLast} == 0">, </f:if></f:for>',
		</f:for></f:else></f:if>
	),
	// non-cacheable actions
	array(<f:if condition="{plugin.noncacheableControllerActions}"><f:then>
		<f:for each="{plugin.noncacheableControllerActions}" as="noncachableControllerAction">'{noncachableControllerAction.controller}' => '{noncachableControllerAction.actions}',
		</f:for></f:then><f:else>
		<f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject">'{domainObject.name}' => '<f:for each="{domainObject.actions}" as="action" iteration="actionIterator"><f:if condition="{action.cacheable} == 0">{action.name}<f:if condition="{actionIterator.isLast} == 0">, </f:if></f:if></f:for>',
		</f:for></f:else></f:if>
	)
);
</f:for>
?>