<?php
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'TestExtension',
    'Testplugin',
    'LLL:EXT:testExtension/Resources/Private/Language/locallang_db.xlf:tx_testExtension_testplugin.name'
);
