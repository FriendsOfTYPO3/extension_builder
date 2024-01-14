[
    'type' => 'number',
    'format' => 'decimal',
    'size' => 30,<f:if condition="{property.required}">
    'required' => true,</f:if><f:if condition="{property.nullable}">
    'nullable' => true,</f:if><f:if condition="{property.enableSlider}">
    'slider' => [
        'step' => {property.steps}
    ],</f:if><f:if condition="{property.setRange}">
    'range' => [
        'lower' => {property.lowerRange},
        'upper' => {property.upperRange}
    ],</f:if>
    'default' => <f:if condition="{property.nullable}"><f:then>null</f:then><f:else>0</f:else></f:if>
]