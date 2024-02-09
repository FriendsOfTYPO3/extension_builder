{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
defined('TYPO3') || die();

<f:for each="{extension.Plugins}" as="plugin">
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    '{extension.extensionName}',
    '{plugin.key -> k:format.uppercaseFirst()}',
    '{plugin.name -> k:format.quoteString()}',
    '{extension.extensionKey}-plugin-{plugin.key}'
);


if (!is_array($GLOBALS['TCA']['tt_content']['types']['{plugin.key -> k:format.quoteString()}'] ?? false)) {
    $GLOBALS['TCA']['tt_content']['types']['{plugin.key -> k:format.quoteString()}'] = [];
}


// Add content element to selector list
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        '{plugin.name -> k:format.quoteString()}',
        '{plugin.key -> k:format.quoteString()}',
        '{extension.extensionKey}-plugin-{plugin.key}',
        '{extension.extensionKey}'
    ]
);
</f:for>
