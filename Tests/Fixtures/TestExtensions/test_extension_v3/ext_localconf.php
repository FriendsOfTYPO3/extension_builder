<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'TYPO3.' . $_EXTKEY,
	'Testplugin',
	array(
		'Main' => 'list, show, new, create, edit, update, delete',
		
	),
	// non-cacheable actions
	array(
		'Main' => 'create, update, delete',
		
	)
);