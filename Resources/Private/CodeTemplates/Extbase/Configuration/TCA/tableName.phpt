{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:{extension.extensionKey}/Resources/Private/Language/locallang_db.xlf:{domainObject.databaseTableName}',
        'label' => '{domainObject.listModuleValueLabel}',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',<f:if condition="{domainObject.sorting}">
        'sortby' => 'sorting',</f:if><f:if condition="{extension.supportVersioning}">
        'versioningWS' => true,</f:if><f:if condition="{extension.supportLocalization}">
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',</f:if><f:if condition="{domainObject.addDeletedField}">
        'delete' => 'deleted',</f:if>
        'enablecolumns' => [<f:if condition="{domainObject.addHiddenField}">
            'disabled' => 'hidden',</f:if><f:if condition="{domainObject.addStarttimeEndtimeFields}">
            'starttime' => 'starttime',
            'endtime' => 'endtime',</f:if>
        ],
        'searchFields' => '<f:for each="{domainObject.searchableProperties}" as="property" iteration="it">{property.fieldName}{f:if(condition: it.isLast, else: ',')}</f:for>',
        'iconfile' => 'EXT:{domainObject.extension.extensionKey}/Resources/Public/Icons/{domainObject.databaseTableName}.gif',<f:if condition="{domainObject.mappedToExistingTable}"><f:else>
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],</f:else></f:if>
    ],
    'types' => [
        <f:if condition="{domainObject.hasChildren}"><f:then>'{domainObject.recordType}'</f:then><f:else>'1'</f:else></f:if> => ['showitem' => '<f:for each="{domainObject.properties}" as="property" iteration="i">{property.fieldName}{f:if(condition: i.isLast, else: ', ')}</f:for><f:if condition="{extension.supportLocalization}">, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource</f:if><f:if condition="{domainObject.addStarttimeEndtimeFields} || {domainObject.addHiddenField}">, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, </f:if><f:if condition="{domainObject.addHiddenField}">hidden, </f:if><f:if condition="{domainObject.addStarttimeEndtimeFields}">starttime, endtime</f:if>'],
    ],
    'columns' => [<f:if condition="{extension.supportLocalization}">
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
                    ['', 0],
                ],
                'foreign_table' => '{domainObject.databaseTableName}',
                'foreign_table_where' => 'AND <k:curlyBrackets>#{domainObject.databaseTableName}</k:curlyBrackets>.<k:curlyBrackets>#pid</k:curlyBrackets>=###CURRENT_PID### AND <k:curlyBrackets>#{domainObject.databaseTableName}</k:curlyBrackets>.<k:curlyBrackets>#sys_language_uid</k:curlyBrackets> IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],</f:if><f:if condition="{domainObject.addHiddenField}">
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],</f:if><f:if condition="{domainObject.addStarttimeEndtimeFields}">
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
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
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],</f:if><f:if condition="{domainObject.categorizable}">
        'categories' => [
            'config'=> [
                'type' => 'category',
            ],
        ],</f:if><k:format.indent indentation="1"><f:render partial="TCA/PropertiesDefinition.phpt" arguments="{domainObject:domainObject,settings:settings}"/></k:format.indent><f:for each="{k:listForeignKeyRelations(domainObject: domainObject)}" as="relation">
        '{relation.foreignKeyName}' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],</f:for>
    ],
];
