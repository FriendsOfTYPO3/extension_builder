[
    'type' => 'input',
    'size' => 4,
    'eval' => 'int<f:if condition="{property.required}">,required</f:if><f:if condition="{property.nullable}">,null</f:if>',
    'default' => <f:if condition="{property.nullable}"><f:then>null</f:then><f:else>0</f:else></f:if>
]