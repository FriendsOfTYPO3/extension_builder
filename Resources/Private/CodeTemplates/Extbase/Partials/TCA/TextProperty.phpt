[
    'type' => 'text',
    'cols' => 40,
    'rows' => 15,<f:if condition="{property.required}">
    'required' => true,</f:if>
    'default' => <f:if condition="{property.nullable}"><f:then>null</f:then><f:else>''</f:else></f:if>
]
