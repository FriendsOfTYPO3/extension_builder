[
    'type' => 'color',
    'size' => 30,<f:if condition="{property.required}">
    'required' => true,</f:if><f:if condition="{property.nullable}">
    'nullable' => true,</f:if>
    'default' => '',<f:if condition="{property.setValuesColorPicker}">
    'valuePicker' => [
        'items' => [<f:for each="{property.colorPickerValues}" as="value" key="label">
            ['{label}', '{value}'],</f:for>
        ],
    ],</f:if>
]