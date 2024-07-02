const modelProperty = {
    propertyName: '',
    propertyType: '',
    propertyDescription: '',
    propertyIsRequired: false,
    propertyIsNullable: false,
    propertyIsExcludeField: false,
    propertyIsL10nModeExclude: false,
    typeSelect: {
        selectboxValues: "",
        renderType: "selectSingle",
        foreignTable: "",
        whereClause: "",
    },
    typeText: {
        enableRichtext: false,
    },
    typeNumber: {
        enableSlider: false,
        steps: 1,
        setRange: false,
        upperRange: 255,
        lowerRange: 0,
    },
    typeColor: {
        setValuesColorPicker: false,
        colorPickerValues: '',
    },
    typeBoolean: {
        renderType: "default",
        booleanValues: "",
    },
    typePassword: {
        renderPasswordGenerator: false,
    },
    typeDateTime: {
        dbTypeDateTime: "",
        formatDateTime: "",
    },
    typeFile: {
        allowedFileTypes: "",
    },
    size: "",
    minItems: "",
    maxItems: "",
}

export default modelProperty;
