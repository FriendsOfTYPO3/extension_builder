[
    'type' => 'select',<f:if condition="{property.renderType} != 'selectSingle'"><f:then>
    'renderType' => '{property.renderType}',</f:then><f:else>
    'renderType' => 'selectSingle',</f:else></f:if><f:if condition="{property.foreignTable} == ''">
    'items' => [
        ['Default', 0],<f:for each="{property.selectboxValues}" as="value" key="label">
        ['{label}', '{value}'],</f:for>
    ],</f:if><f:if condition="{property.foreignTable} != ''">
    'foreign_table' => '{property.foreignTable}',</f:if><f:if condition="{property.whereClause} != ''">
    'foreign_table_where' => '{property.whereClause}',</f:if><f:if condition="{property.size} != ''"><f:then>
    'size' => {property.size},</f:then><f:else>
    'size' => 5,</f:else></f:if><f:if condition="{property.minitems} != '' && {property.renderType} != 'selectSingle' && {property.renderType} != 'selectSingleBox'">
    'minitems' => {property.minitems},</f:if><f:if condition="{property.maxitems} != '' && {property.maxitems} != 0 && {property.renderType} != 'selectSingle' && {property.renderType} != 'selectSingleBox'">
    'maxitems' => {property.maxitems},</f:if><f:if condition="{property.required}">
    'required' => true</f:if>
],