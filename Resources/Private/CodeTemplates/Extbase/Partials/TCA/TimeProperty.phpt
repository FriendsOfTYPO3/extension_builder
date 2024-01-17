[
    'type' => 'input',
    'renderType' => 'inputDateTime',
    'size' => 30,
    'eval' => 'time<f:if condition="{property.required}">,required</f:if><f:if condition="{property.nullable}">,null</f:if>',
    'default' => time()
]