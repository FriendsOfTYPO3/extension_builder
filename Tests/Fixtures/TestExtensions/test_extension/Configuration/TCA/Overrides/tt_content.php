<?php
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
defined('TYPO3') || die();

ExtensionUtility::registerPlugin(
    'TestExtension',
    'Testplugin',
    'Test plugin'
);
