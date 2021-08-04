{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
defined('TYPO3_MODE') || die();

<f:for each="{extension.Plugins}" as="plugin">
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    '{extension.extensionName}',
    'LLL:EXT:<k:format.lowercaseFirst>{extension.extensionName}</k:format.lowercaseFirst>/Resources/Private/Language/locallang_db.xlf:tx_<k:format.lowercaseFirst>{extension.extensionName}</k:format.lowercaseFirst>_{plugin.key}.name',
    '<k:format.quoteString>{plugin.name}</k:format.quoteString>'
);
</f:for>
