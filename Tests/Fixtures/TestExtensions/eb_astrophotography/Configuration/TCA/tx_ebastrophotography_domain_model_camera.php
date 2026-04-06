<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera',
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
        'searchFields' => 'name,brand',
        'iconfile' => 'EXT:eb_astrophotography/Resources/Public/Icons/tx_ebastrophotography_domain_model_camera.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'name, brand, sensor_type, sensor_width, sensor_height, pixel_size, megapixels, cooled, purchase_date, active, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
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
                'foreign_table' => 'tx_ebastrophotography_domain_model_camera',
                'foreign_table_where' => 'AND {#tx_ebastrophotography_domain_model_camera}.{#pid}=###CURRENT_PID### AND {#tx_ebastrophotography_domain_model_camera}.{#sys_language_uid} IN (-1,0)',
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
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.name',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.name.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'default' => ''
            ],
            
        ],
        'brand' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.brand',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.brand.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'default' => ''
            ],
            
        ],
        'sensor_type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.sensor_type',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.sensor_type.description',
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
        'sensor_width' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.sensor_width',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.sensor_width.description',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
            ]
            
        ],
        'sensor_height' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.sensor_height',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.sensor_height.description',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
            ]
            
        ],
        'pixel_size' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.pixel_size',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.pixel_size.description',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
            ]
            
        ],
        'megapixels' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.megapixels',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.megapixels.description',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
            ]
            
        ],
        'cooled' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.cooled',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.cooled.description',
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
        'purchase_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.purchase_date',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.purchase_date.description',
            'config' => [
                'type' => 'datetime',
                'format' => 'date',
                'dbType' => 'date',
                'default' => null,
            ],
            
        ],
        'active' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.active',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_camera.active.description',
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
