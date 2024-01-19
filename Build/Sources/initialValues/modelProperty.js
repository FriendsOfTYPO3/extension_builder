const modelProperty = {
    name: '',
    type: '',
    description: '',
    isRequired: false,
    isNullable: false,
    isExcludeField: false,
    isl10nModeExlude: false,
    typeSelect: {
        selectboxValues: "",
        renderType: "selectSingle",
        foreignTable: "",
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
