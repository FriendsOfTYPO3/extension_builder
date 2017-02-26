{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
    {
<f:for each="{extension.Plugins}" as="plugin">
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            '{extension.vendorName}.{extension.extensionName}',
            '<k:format.uppercaseFirst>{plugin.key}</k:format.uppercaseFirst>',
            '<k:format.quoteString>{plugin.name}</k:format.quoteString>'
        );
<f:if condition="{plugin.switchableControllerActions}">
        $pluginSignature = str_replace('_', '', $extKey) . '_{plugin.key}';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $extKey . '/Configuration/FlexForms/flexform_{plugin.key}.xml');</f:if>
</f:for>

<f:if condition="{extension.BackendModules}">
        if (TYPO3_MODE === 'BE') {
<f:for each="{extension.BackendModules}" as="backendModule">
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                '{extension.vendorName}.{extension.extensionName}',
                '{backendModule.mainModule}', // Make module a submodule of '{backendModule.mainModule}'
                '{backendModule.key}', // Submodule key
                '', // Position
                [<f:if condition="{backendModule.controllerActionCombinations}"><f:then>
                    <f:for each="{backendModule.controllerActionCombinations}" as="actionNames" key="controllerName" iteration="ic">'{controllerName}' => '<f:for each="{actionNames}" as="actionName" iteration="i">{actionName}<f:if condition="{i.isLast} == 0">, </f:if></f:for>'<f:if condition="{ic.isLast}"><f:else>,
                    </f:else></f:if></f:for></f:then><f:else>
                    <f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject">'{domainObject.name}' => '<f:for each="{domainObject.actions}" as="action" iteration="actionIterator">{action.name}<f:if condition="{actionIterator.isLast} == 0">, </f:if></f:for>',</f:for></f:else></f:if>
                ],
                [
                    'access' => 'user,group',
					'icon'   => 'EXT:' . $extKey . '/Resources/Public/Icons/user_mod_{backendModule.key}.svg',
                    'labels' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_{backendModule.key}.xlf',
                ]
            );
</f:for>
        }
</f:if>
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($extKey, 'Configuration/TypoScript', '<k:format.quoteString>{extension.name}</k:format.quoteString>');

<f:for each="{extension.domainObjects}" as="domainObject"><f:if condition="{domainObject.mappedToExistingTable}"><f:else>
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('{domainObject.databaseTableName}', 'EXT:{extension.extensionKey}/Resources/Private/Language/locallang_csh_{domainObject.databaseTableName}.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('{domainObject.databaseTableName}');
</f:else></f:if></f:for>

<f:for each="{extension.domainObjects}" as="domainObject"><f:if condition="{domainObject.categorizable}">
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
            $extKey,
            '{domainObject.databaseTableName}'
        );
</f:if></f:for>
    },
    $_EXTKEY
);
