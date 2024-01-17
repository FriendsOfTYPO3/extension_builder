[
    'dbType' => 'datetime',
    'type' => 'input',
    'renderType' => 'inputDateTime',
    'size' => 30,
    'eval' => 'datetime<f:if condition="{property.required}">,required</f:if><f:if condition="{property.nullable}">,null</f:if>',
    'default' => null,
],