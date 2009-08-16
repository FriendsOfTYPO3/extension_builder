<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_extbasekickstarter_extension'] = array (
	'ctrl' => $TCA['tx_extbasekickstarter_extension']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,extensionkey,title,description,state,languages,persons,domainobjects'
	),
	'feInterface' => $TCA['tx_extbasekickstarter_extension']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'extensionkey' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_extension.extensionkey',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'title' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_extension.title',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'description' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_extension.description',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'state' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_extension.state',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'languages' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_extension.languages',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'persons' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_extension.persons',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'fe_users',	
				'foreign_table_where' => 'ORDER BY fe_users.uid',	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 100,
			)
		),
		'domainobjects' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_extension.domainobjects',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'tx_extbasekickstarter_domainObjects',	
				'foreign_table_where' => 'ORDER BY tx_extbasekickstarter_domainObjects.uid',	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 100,
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, extensionkey, title;;;;2-2-2, description;;;;3-3-3, state, languages, persons, domainobjects')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_extbasekickstarter_person'] = array (
	'ctrl' => $TCA['tx_extbasekickstarter_person']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,email,role,extension'
	),
	'feInterface' => $TCA['tx_extbasekickstarter_person']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_person.name',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'email' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_person.email',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'role' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_person.role',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'extension' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_person.extension',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'tx_extbasekickstarter_extension',	
				'foreign_table_where' => 'ORDER BY tx_extbasekickstarter_extension.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, email, role, extension')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_extbasekickstarter_domainObjects'] = array (
	'ctrl' => $TCA['tx_extbasekickstarter_domainObjects']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,description,relateddomainobjects,extension,backendconfigurationoptions,properties'
	),
	'feInterface' => $TCA['tx_extbasekickstarter_domainObjects']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_domainObjects.name',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'description' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_domainObjects.description',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'relateddomainobjects' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_domainObjects.relateddomainobjects',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'pages',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'extension' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_domainObjects.extension',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'tx_extbasekickstarter_extension',	
				'foreign_table_where' => 'ORDER BY tx_extbasekickstarter_extension.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'backendconfigurationoptions' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_domainObjects.backendconfigurationoptions',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'properties' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_domainObjects.properties',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'pages',	
				'foreign_table_where' => 'ORDER BY pages.uid',	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 100,
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, description, relateddomainobjects, extension, backendconfigurationoptions, properties')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_extbasekickstarter_properties'] = array (
	'ctrl' => $TCA['tx_extbasekickstarter_properties']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,description,validationrules,isrequired,accessmethods'
	),
	'feInterface' => $TCA['tx_extbasekickstarter_properties']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_properties.name',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'description' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_properties.description',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'validationrules' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_properties.validationrules',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'isrequired' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_properties.isrequired',		
			'config' => array (
				'type' => 'check',
			)
		),
		'accessmethods' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:extbase_kickstarter/locallang_db.xml:tx_extbasekickstarter_properties.accessmethods',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, description, validationrules, isrequired, accessmethods')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>