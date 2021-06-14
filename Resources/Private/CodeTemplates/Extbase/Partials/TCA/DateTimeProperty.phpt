[
    'type' => 'input',
    'renderType' => 'inputDateTime',
    'size' => 10,
    'eval' => 'datetime<f:if condition="{property.required}">,required</f:if><f:if condition="{property.nullable}">,null</f:if>',
    'default' => <f:if condition="{property.nullable}"><f:then>null</f:then><f:else>time()</f:else></f:if>
],