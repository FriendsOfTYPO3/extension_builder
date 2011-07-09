<?php

########################################################################
# Extension Manager/Repository config file for ext: "extension_builder"
#
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

/** @var $_EXTKEY string */

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Extension Builder',
	'description' => 'The Extension Builder helps you build and manage your Extbase based TYPO3 extensions.',
	'category' => 'module',
	'author' => 'Ingmar Schlecht, Nico de Haen',
	'author_email' => 'ingmar@typo3.org,mail@ndh-websolutions.de',
	'shy' => '',
	'dependencies' => 'extbase,fluid',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => 'uploads/tx_extensionbuilder/backups',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '1.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5',
			'extbase' => '1.3',
			'fluid' => '1.3',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'phpunit' => '',
		),
	),
);

?>