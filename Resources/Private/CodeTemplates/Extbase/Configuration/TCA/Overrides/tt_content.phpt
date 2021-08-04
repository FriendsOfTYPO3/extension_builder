{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItemGroup(
    'tt_content',
    'list_type',
    '<k:format.lowercaseFirst>{extension.extensionName}</k:format.lowercaseFirst>',
    '{extension.extensionName}',
    'after:default'
);

<f:for each="{extension.Plugins}" as="plugin">
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    '{extension.extensionName}',
    '<k:format.uppercaseFirst>{plugin.key}</k:format.uppercaseFirst>',
    '<k:format.quoteString>{plugin.name}</k:format.quoteString>',
    'EXT:<k:format.lowercaseFirst>{extension.extensionName}</k:format.lowercaseFirst>/Resources/Public/Icons/user_plugin_<k:format.lowercaseFirst>{plugin.key}</k:format.lowercaseFirst>.svg',
    '<k:format.lowercaseFirst>{extension.extensionName}</k:format.lowercaseFirst>'
);
</f:for>
