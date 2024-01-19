[
    'type' => 'text',<f:if condition="{property.enableRichtext}">
    'enableRichtext' => 'true',</f:if><f:if condition="{property.rows} && !{property.enableRichtext}">
    'rows' => {property.rows},</f:if>
    'eval' => 'trim<f:if condition="{property.nullable}">,null</f:if>',<f:if condition="{property.required}">
    'required' => true,</f:if>
    'default' => <f:if condition="{property.nullable}"><f:then>null</f:then><f:else>''</f:else></f:if>
]