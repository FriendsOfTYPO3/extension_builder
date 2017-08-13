{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {
<f:for each="{extension.plugins}" as="plugin">
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            '{extension.vendorName}.{extension.extensionName}',
            '<k:format.uppercaseFirst>{plugin.key}</k:format.uppercaseFirst>',
            [<f:if condition="{plugin.controllerActionCombinations}"><f:then>
                <f:for each="{plugin.controllerActionCombinations}" as="actionNames" key="controllerName" iteration="j">'{controllerName}' => '<f:for each="{actionNames}" as="actionName" iteration="i">{actionName}<f:if condition="{i.isLast} == 0">, </f:if></f:for>'<f:if condition="{j.isLast} == 0">,
                </f:if></f:for></f:then><f:else>
                <f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject" iteration="j">'{domainObject.name}' => '<f:for each="{domainObject.actions}" as="action" iteration="actionIterator">{action.name}<f:if condition="{actionIterator.isLast} == 0">, </f:if></f:for>'<f:if condition="{j.isLast} == 0">,
                </f:if></f:for></f:else></f:if>
            ],
            // non-cacheable actions
            [<f:if condition="{plugin.noncacheableControllerActions}"><f:then>
                <f:for each="{plugin.noncacheableControllerActions}" as="noncachableActionNames" key="noncachableControllerName" iteration="j">'{noncachableControllerName}' => '<f:for each="{noncachableActionNames}" as="actionName" iteration="i">{actionName}<f:if condition="{i.isLast} == 0">, </f:if></f:for>'<f:if condition="{j.isLast} == 0">,
                </f:if></f:for></f:then><f:else>
                <f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject" iteration="j">'{domainObject.name}' => '<f:for each="{domainObject.actions}" as="action" iteration="actionIterator"><f:if condition="{action.cacheable} == 0">{action.name}<f:if condition="{actionIterator.isLast} == 0">, </f:if></f:if></f:for>'<f:if condition="{j.isLast} == 0">,
                </f:if></f:for></f:else></f:if>
            ]
        );
</f:for>
    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod <k:curlyBrackets>
            wizards.newContentElement.wizardItems.plugins <k:curlyBrackets>
                elements {<f:for each="{extension.plugins}" as="plugin">
                    {plugin.key} <k:curlyBrackets>
                        iconIdentifier = {extension.extensionKey}-plugin-{plugin.key}
                        title = LLL:EXT:{extension.extensionKey}/Resources/Private/Language/locallang_db.xlf:tx_{extension.extensionKey}_{plugin.key}.name
                        description = LLL:EXT:{extension.extensionKey}/Resources/Private/Language/locallang_db.xlf:tx_{extension.extensionKey}_{plugin.key}.description
                        tt_content_defValues <k:curlyBrackets>
                            CType = list
                            list_type = {extension.unprefixedShortExtensionKey}_{plugin.key}
                        </k:curlyBrackets>
                    </k:curlyBrackets></f:for>
                }
                show = *
            </k:curlyBrackets>
       </k:curlyBrackets>'
    );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		<f:for each="{extension.plugins}" as="plugin">
			$iconRegistry->registerIcon(
				'{extension.extensionKey}-plugin-{plugin.key}',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:{extension.extensionKey}/Resources/Public/Icons/user_plugin_{plugin.key}.svg']
			);
		</f:for>
    }
);
