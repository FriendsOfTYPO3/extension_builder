[
    'dbType' => 'date',
    'type' => 'input',
    'renderType' => 'inputDateTime',
    'size' => 7,
    'eval' => 'date<f:if condition="{property.required}">,required</f:if><f:if condition="{property.nullable}">,null</f:if>',
    'default' => null,
],