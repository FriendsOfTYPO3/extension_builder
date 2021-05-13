{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
defined('TYPO3_MODE') || die();

<f:for each="{extension.Plugins}" as="plugin">
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    '{extension.extensionName}',
    '<k:format.uppercaseFirst>{plugin.key}</k:format.uppercaseFirst>',
    '<k:format.quoteString>{plugin.name}</k:format.quoteString>'
);
</f:for>
