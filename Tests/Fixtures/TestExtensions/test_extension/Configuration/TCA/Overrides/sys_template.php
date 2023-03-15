<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
defined('TYPO3') || die();

ExtensionManagementUtility::addStaticFile('test_extension', 'Configuration/TypoScript', 'Extension Builder Test Extension');
