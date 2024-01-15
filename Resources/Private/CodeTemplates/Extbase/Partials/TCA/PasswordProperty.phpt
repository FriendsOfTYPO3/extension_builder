[
    'type' => 'password',<f:if condition="{property.required}">
    'required' => true,</f:if><f:if condition="{property.nullable}">
    'nullable' => true,</f:if>
    'hashed' => true,<f:if condition="{property.renderPasswordGenerator}">
    'fieldControl' => [
        'passwordGenerator' => [
            'renderType' => 'passwordGenerator',
        ],
    ],</f:if>
    'default' => <f:if condition="{property.nullable}"><f:then>null</f:then><f:else>''</f:else></f:if>
]