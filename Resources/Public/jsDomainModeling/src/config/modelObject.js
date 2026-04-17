export const modelObjectModule = {
    name: 'New Model Object',
    container: {
        xtype: 'WireIt.FormContainer',
        title: 'Title',
        preventSelfWiring: false,
        fields: [
            {
                type: 'inplaceedit',
                inputParams: {
                    name: 'name',
                    className: 'inputEx-Field extbase-modelTitleEditor',
                    editorField: {
                        type: 'string',
                        inputParams: {
                            required: true,
                            firstCharNonNumeric: true,
                        },
                    },
                    animColors: { from: '#cccccc', to: '#cccccc' },
                },
            },
            {
                type: 'group',
                inputParams: {
                    collapsible: true,
                    collapsed: true,
                    legend: 'domainObjectSettings',
                    className: 'objectSettings',
                    name: 'objectsettings',
                    fields: [
                        {
                            inputParams: {
                                name: 'uid',
                                className: 'hiddenField',
                            },
                        },
                        {
                            type: 'select',
                            inputParams: {
                                name: 'type',
                                advancedMode: true,
                                label: 'objectType',
                                description: 'descr_objectType',
                                selectValues: ['Entity', 'ValueObject'],
                                selectOptions: ['entity', 'valueObject'],
                            },
                        },
                        {
                            type: 'boolean',
                            inputParams: {
                                name: 'aggregateRoot',
                                label: 'isAggregateRoot',
                                description: 'descr_isAggregateRoot',
                                value: false,
                            },
                        },
                        {
                            type: 'boolean',
                            inputParams: {
                                name: 'sorting',
                                advancedMode: true,
                                label: 'enableSorting',
                                description: 'descr_enableSorting',
                                value: false,
                            },
                        },
                        {
                            type: 'boolean',
                            inputParams: {
                                name: 'addDeletedField',
                                advancedMode: true,
                                label: 'addDeletedField',
                                description: 'descr_addDeletedField',
                                value: true,
                            },
                        },
                        {
                            type: 'boolean',
                            inputParams: {
                                name: 'addHiddenField',
                                advancedMode: true,
                                label: 'addHiddenField',
                                description: 'descr_addHiddenField',
                                value: true,
                            },
                        },
                        {
                            type: 'boolean',
                            inputParams: {
                                name: 'addStarttimeEndtimeFields',
                                advancedMode: true,
                                label: 'addStarttimeEndtimeFields',
                                description: 'descr_addStarttimeEndtimeFields',
                                value: true,
                            },
                        },
                        {
                            type: 'boolean',
                            inputParams: {
                                name: 'categorizable',
                                advancedMode: true,
                                label: 'enableCategorizable',
                                description: 'descr_enableCategorizable',
                                value: false,
                            },
                        },
                        {
                            type: 'text',
                            inputParams: {
                                name: 'description',
                                className: 'bottomBorder',
                                label: 'description',
                                placeholder: 'description',
                                required: false,
                                cols: 20,
                                rows: 2,
                            },
                        },
                        {
                            type: 'string',
                            inputParams: {
                                name: 'mapToTable',
                                advancedMode: true,
                                label: 'mapToTable',
                                description: 'descr_mapToTable',
                                required: false,
                            },
                        },
                        {
                            type: 'string',
                            inputParams: {
                                name: 'parentClass',
                                advancedMode: true,
                                label: 'parentClass',
                                placeholder: '\\Fully\\Qualified\\Classname',
                                description: 'descr_parentClass',
                                required: false,
                            },
                        },
                    ],
                },
            },
            {
                type: 'group',
                inputParams: {
                    collapsible: true,
                    collapsed: true,
                    legend: 'defaultActions',
                    name: 'actionGroup',
                    className: 'actionGroup',
                    fields: [
                        {
                            type: 'boolean',
                            inputParams: {
                                name: '_default0_index',
                                label: 'index',
                                value: false,
                            },
                        },
                        {
                            type: 'boolean',
                            inputParams: {
                                name: '_default1_list',
                                label: 'list',
                                value: false,
                            },
                        },
                        {
                            type: 'boolean',
                            inputParams: {
                                name: '_default2_show',
                                label: 'show',
                                value: false,
                            },
                        },
                        {
                            type: 'boolean',
                            inputParams: {
                                name: '_default3_new_create',
                                label: 'create_new',
                                value: false,
                            },
                        },
                        {
                            type: 'boolean',
                            inputParams: {
                                name: '_default4_edit_update',
                                label: 'edit_update',
                                value: false,
                            },
                        },
                        {
                            type: 'boolean',
                            inputParams: {
                                name: '_default5_delete',
                                label: 'delete',
                                value: false,
                            },
                        },
                        {
                            type: 'list',
                            inputParams: {
                                label: 'Custom actions',
                                name: 'customActions',
                                sortable: true,
                                elementType: {
                                    type: 'input',
                                    inputParams: {
                                        name: 'customAction',
                                        label: 'customAction',
                                        forceAlphaNumeric: true,
                                        firstCharNonNumeric: true,
                                        lcFirst: true,
                                    },
                                },
                            },
                        },
                    ],
                },
            },
            {
                type: 'group',
                inputParams: {
                    collapsible: true,
                    collapsed: true,
                    className: 'properties',
                    legend: 'properties',
                    name: 'propertyGroup',
                    fields: [
                        {
                            type: 'list',
                            inputParams: {
                                label: '',
                                name: 'properties',
                                wirable: false,
                                sortable: true,
                                elementType: {
                                    type: 'group',
                                    inputParams: {
                                        name: 'property',
                                        className: 'propertyGroup',
                                        fields: [
                                            {
                                                type: 'hidden',
                                                inputParams: {
                                                    name: 'uid',
                                                    className: 'hiddenField',
                                                },
                                            },
                                            {
                                                type: 'string',
                                                inputParams: {
                                                    name: 'propertyName',
                                                    forceAlphaNumeric: true,
                                                    lcFirst: true,
                                                    firstCharNonNumeric: true,
                                                    placeholder: 'propertyName',
                                                    description: 'descr_propertyName',
                                                    required: true,
                                                },
                                            },
                                            {
                                                type: 'select',
                                                inputParams: {
                                                    name: 'propertyType',
                                                    description: 'descr_propertyType',
                                                    selectValues: [
                                                        'String',
                                                        'Text',
                                                        'RichText',
                                                        'Slug',
                                                        'ColorPicker',
                                                        'Password',
                                                        'Email',
                                                        'Integer',
                                                        'Float',
                                                        'Boolean',
                                                        'InputLink',
                                                        'NativeDate',
                                                        'NativeDateTime',
                                                        'Date',
                                                        'DateTime',
                                                        'NativeTime',
                                                        'Time',
                                                        'TimeSec',
                                                        'Select',
                                                        'File',
                                                        'Image',
                                                        'PassThrough',
                                                        'None',
                                                    ],
                                                    selectOptions: [
                                                        'string',
                                                        'text',
                                                        'richText',
                                                        'slug',
                                                        'colorPicker',
                                                        'password',
                                                        'email',
                                                        'integer',
                                                        'floatingPoint',
                                                        'boolean',
                                                        'inputLink',
                                                        'nativeDate',
                                                        'nativeDateTime',
                                                        'date',
                                                        'dateTime',
                                                        'nativeTime',
                                                        'time',
                                                        'timeSec',
                                                        'selectList',
                                                        'file',
                                                        'image',
                                                        'passThrough',
                                                        'none',
                                                    ],
                                                },
                                            },
                                            {
                                                type: 'group',
                                                inputParams: {
                                                    collapsible: true,
                                                    collapsed: true,
                                                    flatten: true,
                                                    className: 'advancedSettings',
                                                    name: 'advancedSettings',
                                                    legend: 'advancedOptions',
                                                    fields: [
                                                        {
                                                            type: 'text',
                                                            inputParams: {
                                                                name: 'propertyDescription',
                                                                placeholder: 'description',
                                                                cols: 23,
                                                                rows: 2,
                                                            },
                                                        },
                                                        {
                                                            type: 'string',
                                                            inputParams: {
                                                                visibleForTypes: ['File'],
                                                                label: 'allowedFileTypes',
                                                                description: 'descr_allowedFileTypes',
                                                                name: 'allowedFileTypes',
                                                            },
                                                        },
                                                        {
                                                            type: 'string',
                                                            inputParams: {
                                                                visibleForTypes: ['File', 'Image'],
                                                                label: 'maxItems',
                                                                name: 'maxItems',
                                                                value: 1,
                                                            },
                                                        },
                                                        {
                                                            type: 'boolean',
                                                            inputParams: {
                                                                label: 'isRequired',
                                                                name: 'propertyIsRequired',
                                                                value: false,
                                                            },
                                                        },
                                                        {
                                                            type: 'boolean',
                                                            inputParams: {
                                                                visibleForTypes: [
                                                                    'String',
                                                                    'Text',
                                                                    'RichText',
                                                                    'Slug',
                                                                    'ColorPicker',
                                                                    'Password',
                                                                    'Email',
                                                                    'Integer',
                                                                    'Float',
                                                                    'Boolean',
                                                                    'InputLink',
                                                                    'NativeDate',
                                                                    'NativeDateTime',
                                                                    'Date',
                                                                    'DateTime',
                                                                    'NativeTime',
                                                                    'Time',
                                                                    'TimeSec',
                                                                    'Select',
                                                                ],
                                                                label: 'isNullable',
                                                                name: 'propertyIsNullable',
                                                                value: false,
                                                            },
                                                        },
                                                        {
                                                            type: 'boolean',
                                                            inputParams: {
                                                                label: 'isExcludeField',
                                                                name: 'propertyIsExcludeField',
                                                                description: 'descr_isExcludeField',
                                                                value: true,
                                                            },
                                                        },
                                                        {
                                                            type: 'boolean',
                                                            inputParams: {
                                                                label: 'isL10nModeExclude',
                                                                name: 'propertyIsL10nModeExclude',
                                                                description: 'descr_isL10nModeExclude',
                                                                value: false,
                                                            },
                                                        },
                                                    ],
                                                },
                                            },
                                        ],
                                    },
                                },
                            },
                        },
                    ],
                },
            },
            {
                type: 'group',
                inputParams: {
                    collapsible: true,
                    collapsed: false,
                    legend: 'relations',
                    name: 'relationGroup',
                    fields: [
                        {
                            type: 'list',
                            inputParams: {
                                name: 'relations',
                                className: 'relations',
                                wirable: false,
                                sortable: true,
                                elementType: {
                                    type: 'group',
                                    inputParams: {
                                        name: 'relation',
                                        className: 'relationGroup',
                                        fields: [
                                            {
                                                type: 'hidden',
                                                inputParams: {
                                                    name: 'uid',
                                                    className: 'hiddenField',
                                                },
                                            },
                                            {
                                                type: 'string',
                                                inputParams: {
                                                    placeholder: 'relationName',
                                                    name: 'relationName',
                                                    forceAlphaNumeric: true,
                                                    firstCharNonNumeric: true,
                                                    lcFirst: true,
                                                    description: 'descr_relationName',
                                                    required: true,
                                                },
                                            },
                                            {
                                                type: 'string',
                                                inputParams: {
                                                    label: '',
                                                    name: 'relationWire',
                                                    required: false,
                                                    wirable: true,
                                                    className: 'terminalFieldWrap',
                                                    ddConfig: {
                                                        type: 'input',
                                                        allowedTypes: ['output'],
                                                    },
                                                },
                                            },
                                            {
                                                type: 'group',
                                                inputParams: {
                                                    collapsible: true,
                                                    flatten: true,
                                                    collapsed: true,
                                                    className: 'advancedSettings',
                                                    name: 'advancedSettings',
                                                    fields: [
                                                        {
                                                            type: 'select',
                                                            inputParams: {
                                                                label: 'type',
                                                                name: 'relationType',
                                                                selectValues: [
                                                                    'zeroToOne',
                                                                    'zeroToMany',
                                                                    'manyToOne',
                                                                    'manyToMany',
                                                                ],
                                                                selectOptions: [
                                                                    '1:1 (zeroToOne)',
                                                                    '1:n (zeroToMany)',
                                                                    'n:1 (manyToOne)',
                                                                    'm:n (manyToMany)',
                                                                ],
                                                            },
                                                        },
                                                        {
                                                            type: 'select',
                                                            inputParams: {
                                                                label: 'renderType',
                                                                description: 'desc_renderType',
                                                                wrapperClassName:
                                                                    'inputEx-fieldWrapper dependant renderType',
                                                                className: 'inputEx-Field isDependant',
                                                                name: 'renderType',
                                                                selectValues: [
                                                                    'selectSingleBox',
                                                                    'selectCheckBox',
                                                                    'selectMultipleSideBySide',
                                                                    'inline',
                                                                    'selectSingle',
                                                                ],
                                                                selectOptions: [
                                                                    'Single box',
                                                                    'Checkboxes',
                                                                    'Side by side multi select',
                                                                    'Inline (IRRE)',
                                                                    'Dropdown',
                                                                ],
                                                            },
                                                        },
                                                        {
                                                            type: 'text',
                                                            inputParams: {
                                                                placeholder: 'description',
                                                                name: 'relationDescription',
                                                                cols: 20,
                                                                rows: 2,
                                                            },
                                                        },
                                                        {
                                                            type: 'boolean',
                                                            inputParams: {
                                                                label: 'isExcludeField',
                                                                name: 'propertyIsExcludeField',
                                                                advancedMode: true,
                                                                value: true,
                                                                description: 'descr_isExcludeField',
                                                            },
                                                        },
                                                        {
                                                            type: 'boolean',
                                                            inputParams: {
                                                                label: 'lazyLoading',
                                                                name: 'lazyLoading',
                                                                advancedMode: true,
                                                                description: 'descr_lazyLoading',
                                                                value: false,
                                                            },
                                                        },
                                                        {
                                                            type: 'string',
                                                            inputParams: {
                                                                label: 'foreignRelationClass',
                                                                name: 'foreignRelationClass',
                                                                placeholder: '\\Fully\\Qualified\\Classname',
                                                                advancedMode: true,
                                                                description: 'descr_foreignRelationClass',
                                                            },
                                                        },
                                                    ],
                                                },
                                            },
                                        ],
                                    },
                                },
                            },
                        },
                    ],
                },
            },
        ],
        terminals: [
            {
                name: 'SOURCES',
                direction: [0, -1],
                offsetPosition: {
                    left: 5,
                    top: -2,
                },
                ddConfig: {
                    type: 'output',
                    allowedTypes: ['input'],
                },
            },
        ],
    },
};
