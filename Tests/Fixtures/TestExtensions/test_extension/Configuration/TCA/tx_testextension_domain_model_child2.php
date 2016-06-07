<?php
return [
    'ctrl' => [
        'title'    => 'LLL:EXT:test_extension/Resources/Private/Language/locallang_db.xlf:tx_testextension_domain_model_child2',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => 1,
        'versioningWS' => 2,
        'versioning_followPages' => true,

        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'name,date_property1,date_property2,date_property3,date_property4,',
        'iconfile' => 'EXT:test_extension/Resources/Public/Icons/tx_testextension_domain_model_child2.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, date_property1, date_property2, date_property3, date_property4',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, date_property1, date_property2, date_property3, date_property4, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
     'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages'
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_testextension_domain_model_child2',
                'foreign_table_where' => 'AND tx_testextension_domain_model_child2.pid=###CURRENT_PID### AND tx_testextension_domain_model_child2.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],

        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:test_extension/Resources/Private/Language/locallang_db.xlf:tx_testextension_domain_model_child2.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],

        ],
        'date_property1' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:test_extension/Resources/Private/Language/locallang_db.xlf:tx_testextension_domain_model_child2.date_property1',
            'config' => [
                'dbType' => 'date',
                'type' => 'input',
                'size' => 7,
                'eval' => 'date',
                'checkbox' => 0,
                'default' => '0000-00-00'
            ],

        ],
        'date_property2' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:test_extension/Resources/Private/Language/locallang_db.xlf:tx_testextension_domain_model_child2.date_property2',
            'config' => [
                'dbType' => 'datetime',
                'type' => 'input',
                'size' => 12,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => '0000-00-00 00:00:00'
            ],

        ],
        'date_property3' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:test_extension/Resources/Private/Language/locallang_db.xlf:tx_testextension_domain_model_child2.date_property3',
            'config' => [
                'type' => 'input',
                'size' => 7,
                'eval' => 'date',
                'checkbox' => 1,
                'default' => time()
            ],

        ],
        'date_property4' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:test_extension/Resources/Private/Language/locallang_db.xlf:tx_testextension_domain_model_child2.date_property4',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'datetime',
                'checkbox' => 1,
                'default' => time()
            ],

        ],

        'main' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
