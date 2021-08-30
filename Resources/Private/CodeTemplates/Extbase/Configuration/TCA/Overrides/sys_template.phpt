{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
defined('TYPO3') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('{extension.extensionKey}', 'Configuration/TypoScript', '{extension.name -> k:format.quoteString()}');
