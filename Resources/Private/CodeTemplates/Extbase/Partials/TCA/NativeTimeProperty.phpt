[
    'type' => 'datetime',
    'format' => 'time',
    'dbType' => 'time',<f:if condition="{property.required}">
    'required' => true,</f:if><f:if condition="{property.nullable}">
    'nullable' => true,</f:if>
    'default' => null
]
