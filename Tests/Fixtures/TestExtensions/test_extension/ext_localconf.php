<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'FIXTURE.' . $_EXTKEY,
	'Testplugin',
	array(
		'Main' => 'list, show, new, create, edit, update, delete',
		
	),
	// non-cacheable actions
	array(
		'Main' => 'create, update, delete',
		
	)
);
