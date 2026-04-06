<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter',
        'label' => 'name',
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
        'searchFields' => 'name,manufacturer',
        'iconfile' => 'EXT:eb_astrophotography/Resources/Public/Icons/tx_ebastrophotography_domain_model_astrofilter.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'name, filter_type, central_wavelength, bandwidth, color, manufacturer, diameter, active, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
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
                'foreign_table' => 'tx_ebastrophotography_domain_model_astrofilter',
                'foreign_table_where' => 'AND {#tx_ebastrophotography_domain_model_astrofilter}.{#pid}=###CURRENT_PID### AND {#tx_ebastrophotography_domain_model_astrofilter}.{#sys_language_uid} IN (-1,0)',
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

        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.name',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.name.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'default' => ''
            ],
            
        ],
        'filter_type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.filter_type',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.filter_type.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => '-- Label --', 'value' => 0],
                ],
                'size' => 1,
                'maxitems' => 1,
            ],
        ],
        'central_wavelength' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.central_wavelength',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.central_wavelength.description',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'default' => 0,
            ]
            
        ],
        'bandwidth' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.bandwidth',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.bandwidth.description',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
            ]
            
        ],
        'color' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.color',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.color.description',
            'config' => [
                'type' => 'color',
                'default' => ''
            ]
            
        ],
        'manufacturer' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.manufacturer',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.manufacturer.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'default' => ''
            ],
            
        ],
        'diameter' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.diameter',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.diameter.description',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
            ]
            
        ],
        'active' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.active',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_astrofilter.active.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => '',
                        'value' => '',
                    ]
                ],
                'default' => 0,
            ]
        ],
    
    ],
];
