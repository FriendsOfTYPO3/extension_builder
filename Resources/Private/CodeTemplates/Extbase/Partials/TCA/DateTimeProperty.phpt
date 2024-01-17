[
    'type' => 'datetime',<f:if condition="{property.formatDateTime}">
    'format' => '{property.formatDateTime}',</f:if>
    'size' => 20,<f:if condition="{property.required}">
    'required' => true,</f:if><f:if condition="{property.nullable}">
    'nullable' => true,</f:if>
    'default' => <f:if condition="{property.nullable}"><f:then>null</f:then><f:else>time()</f:else></f:if>
],