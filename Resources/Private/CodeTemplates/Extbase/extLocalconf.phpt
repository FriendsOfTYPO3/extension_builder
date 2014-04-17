{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}
<f:for each="{extension.plugins}" as="plugin">
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'{extension.vendorName}.' . $_EXTKEY,
	'<k:format.uppercaseFirst>{plugin.key}</k:format.uppercaseFirst>',
	array(<f:if condition="{plugin.controllerActionCombinations}"><f:then>
		<f:for each="{plugin.controllerActionCombinations}" as="actionNames" key="controllerName">'{controllerName}' => '<f:for each="{actionNames}" as="actionName" iteration="i">{actionName}<f:if condition="{i.isLast} == 0">, </f:if></f:for>',
		</f:for></f:then><f:else>
		<f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject">'{domainObject.name}' => '<f:for each="{domainObject.actions}" as="action" iteration="actionIterator">{action.name}<f:if condition="{actionIterator.isLast} == 0">, </f:if></f:for>',
		</f:for></f:else></f:if>
	),
	// non-cacheable actions
	array(<f:if condition="{plugin.noncacheableControllerActions}"><f:then>
		<f:for each="{plugin.noncacheableControllerActions}" as="noncachableActionNames" key="noncachableControllerName">'{noncachableControllerName}' => '<f:for each="{noncachableActionNames}" as="actionName" iteration="i">{actionName}<f:if condition="{i.isLast} == 0">, </f:if></f:for>',
		</f:for></f:then><f:else>
		<f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject">'{domainObject.name}' => '<f:for each="{domainObject.actions}" as="action" iteration="actionIterator"><f:if condition="{action.cacheable} == 0">{action.name}<f:if condition="{actionIterator.isLast} == 0">, </f:if></f:if></f:for>',
		</f:for></f:else></f:if>
	)
);
</f:for>
