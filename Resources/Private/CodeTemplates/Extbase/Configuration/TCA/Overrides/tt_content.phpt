{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
defined('TYPO3') || die();

<f:for each="{extension.Plugins}" as="plugin">
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    '{extension.extensionName}',
    '{extension.extensionKey}_{plugin.key}',
    '{plugin.name -> k:format.quoteString()}',
    '{extension.extensionKey}-plugin-{plugin.key}'
);


if (!is_array($GLOBALS['TCA']['tt_content']['types']['{extension.extensionKey}_{plugin.key}'] ?? false)) {
    $GLOBALS['TCA']['tt_content']['types']['{extension.extensionKey}_{plugin.key}'] = [];
}


// Add content element to selector list
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        '{plugin.name -> k:format.quoteString()}',
        '{extension.extensionKey}_{plugin.key}',
        '{extension.extensionKey}-plugin-{plugin.key}',
        '{extension.extensionKey}'
    ]
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:{extension.extensionKey}/Configuration/FlexForms/flexform_{plugin.key}.xml',
    '{extension.extensionKey}_{plugin.key}'
);

$GLOBALS['TCA']['tt_content']['types']['{extension.extensionKey}_{plugin.key}']['showitem'] = '
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
        --palette--;;general,
        --palette--;;headers,
    --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin,
        pi_flexform,
    --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
        --palette--;;frames,
        --palette--;;appearanceLinks,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
        --palette--;;language,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
        --palette--;;hidden,
        --palette--;;access,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
        categories,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
        rowDescription,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
';
</f:for>
