[
    'type' => 'number',
    'format' => 'integer',
    'default' => <f:if condition="{property.nullable}"><f:then>null</f:then><f:else>0</f:else></f:if>,<f:if condition="{property.required}">
    'required' => true,</f:if><f:if condition="{property.nullable}">
    'nullable' => true,</f:if>
]
