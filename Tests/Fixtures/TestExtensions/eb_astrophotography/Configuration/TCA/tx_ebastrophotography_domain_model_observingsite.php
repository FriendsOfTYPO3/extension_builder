<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite',
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
        'searchFields' => 'name,description,website,contact_email',
        'iconfile' => 'EXT:eb_astrophotography/Resources/Public/Icons/tx_ebastrophotography_domain_model_observingsite.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'name, description, latitude, longitude, altitude, bortle_class, website, contact_email, active, image, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
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
                'foreign_table' => 'tx_ebastrophotography_domain_model_observingsite',
                'foreign_table_where' => 'AND {#tx_ebastrophotography_domain_model_observingsite}.{#pid}=###CURRENT_PID### AND {#tx_ebastrophotography_domain_model_observingsite}.{#sys_language_uid} IN (-1,0)',
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
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.name',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.name.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'default' => ''
            ],
            
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.description',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.description.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'default' => ''
            ]
            
        ],
        'latitude' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.latitude',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.latitude.description',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
            ]
            
        ],
        'longitude' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.longitude',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.longitude.description',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
            ]
            
        ],
        'altitude' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.altitude',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.altitude.description',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'default' => 0,
            ]
            
        ],
        'bortle_class' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.bortle_class',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.bortle_class.description',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'default' => 0,
            ]
            
        ],
        'website' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.website',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.website.description',
            'config' => [
                'type' => 'link',
            ]
        ],
        'contact_email' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.contact_email',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.contact_email.description',
            'config' => [
                'type' => 'email',
                'default' => ''
            ]
            
        ],
        'active' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.active',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.active.description',
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
        'image' => [
            'exclude' => true,
            'label' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.image',
            'description' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_db.xlf:tx_ebastrophotography_domain_model_observingsite.image.description',
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
