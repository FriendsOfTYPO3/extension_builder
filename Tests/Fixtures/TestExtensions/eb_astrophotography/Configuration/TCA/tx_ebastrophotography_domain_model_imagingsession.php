<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession',
        'label' => 'session_date',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'notes',
        'iconfile' => 'EXT:eb_astrophotography/Resources/Public/Icons/tx_ebastrophotography_domain_model_imagingsession.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'session_date, start_time, end_time, frame_exposure, temperature, humidity, seeing_conditions, transparency, moon_phase, total_frames, usable_frames, notes, observing_sites, telescopes, cameras, astro_filters, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['label' => '', 'value' => 0],
                ],
                'foreign_table' => 'tx_ebastrophotography_domain_model_imagingsession',
                'foreign_table_where' => 'AND {#tx_ebastrophotography_domain_model_imagingsession}.{#pid}=###CURRENT_PID### AND {#tx_ebastrophotography_domain_model_imagingsession}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => '',
                        'value' => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],

        'session_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.session_date',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.session_date.description',
            'config' => [
                'type' => 'datetime',
                'format' => 'date',
                'dbType' => 'date',
                'default' => null,
            ],
            
        ],
        'start_time' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.start_time',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.start_time.description',
            'config' => [
                'type' => 'datetime',
                'format' => 'time',
                'dbType' => 'time',
                'default' => null
            ]
            
        ],
        'end_time' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.end_time',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.end_time.description',
            'config' => [
                'type' => 'datetime',
                'format' => 'time',
                'default' => time()
            ]
            
        ],
        'frame_exposure' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.frame_exposure',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.frame_exposure.description',
            'config' => [
                'type' => 'datetime',
                'format' => 'timesec',
                'default' => time()
            ]
            
        ],
        'temperature' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.temperature',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.temperature.description',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
            ]
            
        ],
        'humidity' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.humidity',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.humidity.description',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'default' => 0,
            ]
            
        ],
        'seeing_conditions' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.seeing_conditions',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.seeing_conditions.description',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'default' => 0,
            ]
            
        ],
        'transparency' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.transparency',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.transparency.description',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'default' => 0,
            ]
            
        ],
        'moon_phase' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.moon_phase',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.moon_phase.description',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'default' => 0,
            ]
            
        ],
        'total_frames' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.total_frames',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.total_frames.description',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'default' => 0,
            ]
            
        ],
        'usable_frames' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.usable_frames',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.usable_frames.description',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'default' => 0,
            ]
            
        ],
        'notes' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.notes',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.notes.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'default' => ''
            ]
            
        ],
        'observing_sites' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.observing_sites',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.observing_sites.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_ebastrophotography_domain_model_observingsite',
                'default' => 0,
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 9999,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],

        ],
        'telescopes' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.telescopes',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.telescopes.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_ebastrophotography_domain_model_telescope',
                'default' => 0,
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 9999,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],

        ],
        'cameras' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.cameras',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.cameras.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_ebastrophotography_domain_model_camera',
                'default' => 0,
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 9999,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],

        ],
        'astro_filters' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.astro_filters',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_imagingsession.astro_filters.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_ebastrophotography_domain_model_astrofilter',
                'default' => 0,
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 9999,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],

        ],
    
    ],
];
