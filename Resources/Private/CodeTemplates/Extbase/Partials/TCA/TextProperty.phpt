[
    'type' => 'text',
    'cols' => 40,
    'rows' => 15,
    'eval' => 'trim<f:if condition="{property.required}">,required</f:if><f:if condition="{property.nullable}">,null</f:if>',
    'default' => <f:if condition="{property.nullable}"><f:then>null</f:then><f:else>''</f:else></f:if>
]