<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}


/**
 * Register Backend Module
 */


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
	$_EXTKEY,
	'tools',
	'extensionbuilder',
	'',
	array(
		 'BuilderModule' => 'index,domainmodelling,dispatchRpc',
	),
	array(
		 'access' => 'user,group',
		 'icon' => 'EXT:extension_builder/ext_icon.gif',
		 'labels' => 'LLL:EXT:extension_builder/Resources/Private/Language/locallang_mod.xml',
	)
);


?>