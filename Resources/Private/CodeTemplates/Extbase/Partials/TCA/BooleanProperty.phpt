[
    'type' => 'check',<f:if condition="{property.renderTypeBoolean} != 'default'">
    'renderType' => '{property.renderTypeBoolean}',</f:if><f:if condition="{property.booleanValues} !== ''">
    'items' => [<f:for each="{property.booleanValues}" as="value">
        ['label' => '{value}'],</f:for>
    ],</f:if>
    'default' => 0,
]