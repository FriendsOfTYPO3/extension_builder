<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope',
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
        'searchFields' => 'name,brand,notes',
        'iconfile' => 'EXT:eb_astrophotography/Resources/Public/Icons/tx_ebastrophotography_domain_model_telescope.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'name, brand, telescope_type, focal_length, aperture, focal_ratio, purchase_date, active, notes, image, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
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
                'foreign_table' => 'tx_ebastrophotography_domain_model_telescope',
                'foreign_table_where' => 'AND {#tx_ebastrophotography_domain_model_telescope}.{#pid}=###CURRENT_PID### AND {#tx_ebastrophotography_domain_model_telescope}.{#sys_language_uid} IN (-1,0)',
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
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.name',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.name.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'default' => ''
            ],
            
        ],
        'brand' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.brand',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.brand.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'default' => ''
            ],
            
        ],
        'telescope_type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.telescope_type',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.telescope_type.description',
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
        'focal_length' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.focal_length',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.focal_length.description',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'default' => 0,
            ]
            
        ],
        'aperture' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.aperture',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.aperture.description',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'default' => 0,
            ]
            
        ],
        'focal_ratio' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.focal_ratio',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.focal_ratio.description',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
            ]
            
        ],
        'purchase_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.purchase_date',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.purchase_date.description',
            'config' => [
                'type' => 'datetime',
                'format' => 'date',
                'dbType' => 'date',
                'default' => null,
            ],
            
        ],
        'active' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.active',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.active.description',
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
        'notes' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.notes',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.notes.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'default' => ''
            ]
            
        ],
        'image' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.image',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_telescope.image.description',
            'config' => [
                'type' => 'file',
                'allowed' => 'common-image-types',
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
                ],
                'overrideChildTca' => [
                    'types' => [
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette',
                        ],
                    ],
                ],
                'maxitems' => 1,
            ],
            
        ],
    
    ],
];
