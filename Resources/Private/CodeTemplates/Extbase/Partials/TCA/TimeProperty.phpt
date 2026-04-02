[
    'type' => 'datetime',
    'format' => 'time',<f:if condition="{property.required}">
    'required' => true,</f:if><f:if condition="{property.nullable}">
    'nullable' => true,</f:if>
    'default' => time()
]
