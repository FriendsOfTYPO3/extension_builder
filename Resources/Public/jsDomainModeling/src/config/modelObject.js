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
                                            // --- Conditional field visibility ---
                                            // Two mechanisms control which advanced fields are shown
                                            // for a given property type (see eb-group.js:_applyPropertyTypeVisibility):
                                            //
                                            // visibleForTypes: [] — allowlist. Field is shown ONLY for the listed types.
                                            //   Use for fields that are only relevant to a small set of types.
                                            //   Adding a new property type: field stays hidden unless you add it here.
                                            //
                                            // hiddenForTypes: [] — denylist. Field is shown for ALL types EXCEPT the listed ones.
                                            //   Use for fields that apply broadly but must be hidden for a few special types.
                                            //   Adding a new property type: field is shown automatically. Only add it
                                            //   to hiddenForTypes if it genuinely does not apply to the new type.
                                            {
                                                type: 'text',
                                                inputParams: {
                                                    name: 'propertyDescription',
                                                    advancedMode: true,
                                                    placeholder: 'description',
                                                    cols: 23,
                                                    rows: 2,
                                                },
                                            },
                                            {
                                                type: 'string',
                                                inputParams: {
                                                    visibleForTypes: ['File'],
                                                    advancedMode: true,
                                                    label: 'allowedFileTypes',
                                                    description: 'descr_allowedFileTypes',
                                                    name: 'allowedFileTypes',
                                                },
                                            },
                                            {
                                                type: 'string',
                                                inputParams: {
                                                    visibleForTypes: ['File', 'Image'],
                                                    advancedMode: true,
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
                                                    advancedMode: true,
                                                    value: false,
                                                },
                                            },
                                            {
                                                type: 'boolean',
                                                inputParams: {
                                                    hiddenForTypes: ['File', 'Image', 'PassThrough', 'None'],
                                                    label: 'isNullable',
                                                    name: 'propertyIsNullable',
                                                    advancedMode: true,
                                                    value: false,
                                                },
                                            },
                                            {
                                                type: 'boolean',
                                                inputParams: {
                                                    label: 'isExcludeField',
                                                    name: 'propertyIsExcludeField',
                                                    advancedMode: true,
                                                    description: 'descr_isExcludeField',
                                                    value: true,
                                                },
                                            },
                                            {
                                                type: 'boolean',
                                                inputParams: {
                                                    label: 'isL10nModeExclude',
                                                    name: 'propertyIsL10nModeExclude',
                                                    advancedMode: true,
                                                    description: 'descr_isL10nModeExclude',
                                                    value: false,
                                                },
                                            },
                                            {
                                                type: 'list',
                                                inputParams: {
                                                    visibleForTypes: ['Select'],
                                                    advancedMode: true,
                                                    label: 'selectItems',
                                                    name: 'selectItems',
                                                    sortable: true,
                                                    elementType: {
                                                        type: 'group',
                                                        inputParams: {
                                                            name: 'selectItem',
                                                            fields: [
                                                                {
                                                                    type: 'string',
                                                                    inputParams: {
                                                                        name: 'label',
                                                                        placeholder: 'label',
                                                                        required: true,
                                                                    },
                                                                },
                                                                {
                                                                    type: 'string',
                                                                    inputParams: {
                                                                        name: 'value',
                                                                        placeholder: 'value',
                                                                        required: true,
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
                                                type: 'select',
                                                inputParams: {
                                                    label: 'type',
                                                    name: 'relationType',
                                                    advancedMode: true,
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
                                                    name: 'renderType',
                                                    advancedMode: true,
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
                                                    advancedMode: true,
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
