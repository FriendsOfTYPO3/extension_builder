<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}


/**
* Register Backend Module
*/

Tx_Extbase_Utility_Extension::registerModule(
	$_EXTKEY,
	'tools',
	'kickstarter',
	'',
	array(
		'KickstarterModule' => 'index,generateCode'
	),
	array(
		'access' => 'user,group',
		'icon'   => 'EXT:extbase_kickstarter/ext_icon.gif',
		'labels' => 'LLL:EXT:extbase_kickstarter/Resources/Private/Language/locallang_mod.xml',
	)
);
?>