{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
return [
    'ctrl' => [
        'title'	=> 'LLL:EXT:{extension.extensionKey}/Resources/Private/Language/locallang_db.xlf:{domainObject.databaseTableName}',
        'label' => '{domainObject.listModuleValueLabel}',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => 1,<f:if condition="{domainObject.sorting}">
        'sortby' => 'sorting',</f:if>
<f:if condition="{extension.supportVersioning}">		'versioningWS' => 2,
        'versioning_followPages' => true,</f:if>
<f:if condition="{extension.supportLocalization}">
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',</f:if>
<f:if condition="{domainObject.addDeletedField}">		'delete' => 'deleted',</f:if>
        'enablecolumns' => [
<f:if condition="{domainObject.addHiddenField}">			'disabled' => 'hidden',</f:if>
<f:if condition="{domainObject.addStarttimeEndtimeFields}">			'starttime' => 'starttime',
            'endtime' => 'endtime',</f:if>
        ],
        'searchFields' => '<f:for each="{domainObject.properties}" as="property">{property.fieldName},</f:for>',
        'iconfile' => 'EXT:{domainObject.extension.extensionKey}/Resources/Public/Icons/{domainObject.databaseTableName}.gif'
    ],
    'interface' => [
        'showRecordFieldList' => '<f:if condition="{extension.supportLocalization}">sys_language_uid, l10n_parent, l10n_diffsource, </f:if><f:if condition="{domainObject.addHiddenField}">hidden, </f:if><f:for each="{domainObject.properties}" as="property" iteration="i">{property.fieldName}<f:if condition="{i.isLast}"><f:else>, </f:else></f:if></f:for>',
    ],
    'types' => [
        <f:if condition="{domainObject.hasChildren}"><f:then>'{domainObject.recordType}'</f:then><f:else>'1'</f:else></f:if> => ['showitem' => '<f:if condition="{extension.supportLocalization}">sys_language_uid, l10n_parent, l10n_diffsource, </f:if><f:if condition="{domainObject.addHiddenField}">hidden, </f:if><f:for each="{domainObject.properties}" as="property" iteration="i">{property.fieldName}, </f:for><f:if condition="{domainObject.addStarttimeEndtimeFields}">--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime</f:if>'],
    ],
    'columns' => [
<f:if condition="{extension.supportLocalization}">        'sys_language_uid' => [
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
                'foreign_table' => '{domainObject.databaseTableName}',
                'foreign_table_where' => 'AND {domainObject.databaseTableName}.pid=###CURRENT_PID### AND {domainObject.databaseTableName}.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],</f:if>
<f:if condition="{extension.supportVersioning}">        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],</f:if>
<f:if condition="{domainObject.addHiddenField}">        'hidden' => [
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
        ],</f:if>
<f:if condition="{domainObject.addStarttimeEndtimeFields}">        'starttime' => [
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
        ],</f:if>
        <k:format.indent indentation="1"><f:render partial="TCA/PropertiesDefinition.phpt" arguments="{domainObject:domainObject,settings:settings}"/></k:format.indent>
        <f:for each="{k:listForeignKeyRelations(extension: extension, domainObject: domainObject)}" as="relation">
        '{relation.foreignKeyName}' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],</f:for>
    ],
];
