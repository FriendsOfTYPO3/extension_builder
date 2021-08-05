{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
defined('TYPO3_MODE') || die();

<f:for each="{extension.Plugins}" as="plugin">
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    '{extension.extensionName}',
    '{plugin.key -> k:format.uppercaseFirst()}',
    '{plugin.name -> k:format.quoteString()}'
);
</f:for>
