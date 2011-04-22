<?php

########################################################################
# Extension Manager/Repository config file for ext: "extension_builder"
#
# Auto generated 16-08-2009 17:55
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Extension Builder',
	'description' => 'The Extension Builder helps you build and manage your Extbase based TYPO3 extensions.',
	'category' => '',
	'author' => 'Ingmar Schlecht',
	'author_email' => 'ingmar@typo3.org',
	'shy' => '',
	'dependencies' => 'extbase,fluid',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => 'uploads/tx_extensionbuilder/backups',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.9.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'extbase' => '',
			'fluid' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'phpunit' => '',
		),
	),
);

?>