[
    'type' => 'email',<f:if condition="{property.required}">
    'required' => true,</f:if><f:if condition="{property.nullable}">
    'nullable' => true,</f:if>
    'default' => <f:if condition="{property.nullable}"><f:then>null</f:then><f:else>''</f:else></f:if>
]