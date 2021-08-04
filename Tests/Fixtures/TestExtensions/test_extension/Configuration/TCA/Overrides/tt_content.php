<?php
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'TestExtension',
    'Testplugin',
    'Test plugin',
	'EXT:testExtension/Resources/Public/Icons/user_plugin_testplugin.svg',
);
