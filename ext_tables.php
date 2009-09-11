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



$TCA['tx_extbasekickstarter_extension'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_extension',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_extbasekickstarter_extension.gif',
	),
);

$TCA['tx_extbasekickstarter_person'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_person',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_extbasekickstarter_person.gif',
	),
);

$TCA['tx_extbasekickstarter_domainObjects'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_domainObjects',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_extbasekickstarter_domainObjects.gif',
	),
);

$TCA['tx_extbasekickstarter_properties'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_properties',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_extbasekickstarter_properties.gif',
	),
);
?>