[
    'type' => 'input',
    'renderType' => 'inputDateTime',
    'dbType' => 'time',
    'size' => 30,
    'eval' => 'time<f:if condition="{property.required}">,required</f:if><f:if condition="{property.nullable}">,null</f:if>',
    'default' => null
]
